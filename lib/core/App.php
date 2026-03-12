<?php
/**
 * GatewayOS2 Website - Application Kernel
 *
 * Singleton entry point that boots configuration, registers routes,
 * builds the middleware pipeline, and dispatches the matched controller.
 */

require_once BASE_DIR . '/lib/core/Router.php';
require_once BASE_DIR . '/lib/core/Request.php';
require_once BASE_DIR . '/lib/core/Response.php';
require_once BASE_DIR . '/lib/core/View.php';
require_once BASE_DIR . '/lib/core/Controller.php';

class App
{
    /** @var App|null Singleton instance */
    private static $instance = null;

    /** @var Router */
    private $router;

    /** @var array Middleware stack configuration */
    private $middlewareConfig = [];

    /** @var bool Whether boot() has run */
    private $booted = false;

    // ──────────────────────────────────────────────────────────────
    // Singleton
    // ──────────────────────────────────────────────────────────────

    private function __construct()
    {
        $this->router = new Router();
    }

    /**
     * Return the single application instance, creating it on first call.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup()
    {
        throw new \RuntimeException('Cannot unserialize singleton');
    }

    // ──────────────────────────────────────────────────────────────
    // Boot
    // ──────────────────────────────────────────────────────────────

    /**
     * Load configuration files and register all routes.
     * Safe to call multiple times; work is done only once.
     */
    public function boot(): self
    {
        if ($this->booted) {
            return $this;
        }

        // Load application constants
        require_once BASE_DIR . '/config/app.php';

        // Ensure writable directories exist
        $this->ensureDirectories();

        // Load middleware map
        $this->middlewareConfig = require BASE_DIR . '/config/middleware.php';

        // Register public routes
        $this->registerRoutes(require BASE_DIR . '/config/routes.php');

        // Register admin routes
        $this->registerRoutes(require BASE_DIR . '/config/admin_routes.php');

        $this->booted = true;
        return $this;
    }

    // ──────────────────────────────────────────────────────────────
    // Run
    // ──────────────────────────────────────────────────────────────

    /**
     * Handle the incoming HTTP request through the full lifecycle:
     *   Request -> Middleware -> Controller -> Response
     */
    public function run(): void
    {
        $request  = new Request();
        $response = new Response();

        // Match a route
        $match = $this->router->match($request->method(), $request->path());

        if ($match === null) {
            $this->sendNotFound($request, $response);
            return;
        }

        [$handler, $params, $middlewareGroup] = $match;
        $request->params = $params;

        // Build the middleware pipeline: global + group-specific
        $middlewareClasses = $this->resolveMiddleware($middlewareGroup);

        // Run middleware pipeline, then dispatch
        $this->runPipeline($middlewareClasses, $request, $response, function () use ($handler, $request, $response) {
            $this->dispatch($handler, $request, $response);
        });
    }

    // ──────────────────────────────────────────────────────────────
    // Route registration
    // ──────────────────────────────────────────────────────────────

    /**
     * Register an array of route definitions.
     *
     * @param array $routes Each entry: [method, pattern, handler, group]
     */
    private function registerRoutes(array $routes): void
    {
        foreach ($routes as $route) {
            $method          = $route[0];
            $pattern         = $route[1];
            $handler         = $route[2];
            $middlewareGroup = $route[3] ?? 'web';

            $this->router->add($method, $pattern, $handler, $middlewareGroup);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Middleware
    // ──────────────────────────────────────────────────────────────

    /**
     * Resolve middleware class names for the given group.
     * Always prepends the 'global' stack.
     *
     * @param  string $group
     * @return string[]
     */
    private function resolveMiddleware(string $group): array
    {
        $global = $this->middlewareConfig['global'] ?? [];
        $groupMiddleware = $this->middlewareConfig[$group] ?? [];
        return array_merge($global, $groupMiddleware);
    }

    /**
     * Execute middleware classes in order, then call $core.
     * Each middleware must implement handle(Request, Response, callable $next).
     */
    private function runPipeline(array $middlewareClasses, Request $request, Response $response, callable $core): void
    {
        // Build an onion: wrap the core in successive middleware layers (inside-out)
        $pipeline = $core;

        foreach (array_reverse($middlewareClasses) as $className) {
            $file = BASE_DIR . '/lib/middleware/' . $className . '.php';
            if (!file_exists($file)) {
                // Skip missing middleware gracefully during development
                continue;
            }
            require_once $file;

            if (!class_exists($className)) {
                continue;
            }

            $middleware = new $className();
            $next = $pipeline; // capture current layer
            $pipeline = function () use ($middleware, $request, $response, $next) {
                $middleware->handle($request, $response, $next);
            };
        }

        $pipeline();
    }

    // ──────────────────────────────────────────────────────────────
    // Dispatch
    // ──────────────────────────────────────────────────────────────

    /**
     * Instantiate the controller and call the action method.
     *
     * @param string   $handler  "ControllerName@method"
     * @param Request  $request
     * @param Response $response
     */
    private function dispatch(string $handler, Request $request, Response $response): void
    {
        [$controllerName, $method] = explode('@', $handler, 2);

        // Resolve controller file — check subdirectories (admin/, api/) first
        $controllerFile = null;
        $searchPaths = [
            BASE_DIR . '/controllers/' . $controllerName . '.php',
            BASE_DIR . '/controllers/admin/' . $controllerName . '.php',
            BASE_DIR . '/controllers/api/' . $controllerName . '.php',
        ];
        foreach ($searchPaths as $path) {
            if (file_exists($path)) {
                $controllerFile = $path;
                break;
            }
        }
        if ($controllerFile === null) {
            $this->sendError($response, 500, "Controller not found: {$controllerName}");
            return;
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            $this->sendError($response, 500, "Class not found: {$controllerName}");
            return;
        }

        $controller = new $controllerName($request, $response);

        if (!method_exists($controller, $method)) {
            $this->sendError($response, 500, "Method not found: {$controllerName}@{$method}");
            return;
        }

        $controller->$method();
    }

    // ──────────────────────────────────────────────────────────────
    // Error helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Render the 404 page.
     */
    private function sendNotFound(Request $request, Response $response): void
    {
        $response->status(404);

        if ($request->isAjax() || strpos($request->path(), '/api/') === 0) {
            $response->json(['error' => 'Not Found'], 404);
            return;
        }

        $view = new View();
        $content = $view->render('pages/404', [], 'main');
        $response->html($content, 404);
    }

    /**
     * Render a generic error page (used for server-side dispatch failures).
     */
    private function sendError(Response $response, int $code, string $message): void
    {
        error_log("App dispatch error: {$message}");

        if (defined('APP_DEBUG') && APP_DEBUG) {
            $response->html('<h1>Error ' . $code . '</h1><pre>' . htmlspecialchars($message) . '</pre>', $code);
            return;
        }

        $view = new View();
        $content = $view->render('pages/500', [], 'main');
        $response->html($content, $code);
    }

    // ──────────────────────────────────────────────────────────────
    // Filesystem
    // ──────────────────────────────────────────────────────────────

    /**
     * Create required data directories if they do not exist.
     */
    private function ensureDirectories(): void
    {
        $dirs = [
            DATA_DIR,
            BLOG_DIR,
            MESSAGES_DIR,
            CACHE_DIR,
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────────────────────────

    /**
     * Return the router instance (useful for testing / inspection).
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
}
