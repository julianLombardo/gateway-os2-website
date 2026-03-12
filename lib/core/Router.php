<?php
/**
 * GatewayOS2 Website - URL Router
 *
 * Registers route patterns with :param placeholders and matches
 * incoming requests against them using generated regular expressions.
 */

class Router
{
    /**
     * Registered routes grouped by HTTP method.
     *
     * Structure:
     *   [METHOD => [[regex, handler, paramNames, middlewareGroup], ...]]
     *
     * @var array<string, array>
     */
    private $routes = [];

    // ──────────────────────────────────────────────────────────────
    // Registration
    // ──────────────────────────────────────────────────────────────

    /**
     * Register a route.
     *
     * @param string $method          HTTP method (GET, POST, etc.)
     * @param string $pattern         URL pattern, e.g. /blog/:slug
     * @param string $handler         Controller@method string
     * @param string $middlewareGroup Middleware group name
     */
    public function add(string $method, string $pattern, string $handler, string $middlewareGroup = 'web'): void
    {
        $method = strtoupper($method);

        [$regex, $paramNames] = $this->compilePattern($pattern);

        $this->routes[$method][] = [
            'regex'           => $regex,
            'handler'         => $handler,
            'paramNames'      => $paramNames,
            'middlewareGroup' => $middlewareGroup,
            'pattern'         => $pattern, // kept for debugging / listing
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // Matching
    // ──────────────────────────────────────────────────────────────

    /**
     * Attempt to match a request method + URI path to a registered route.
     *
     * @param  string $method HTTP method
     * @param  string $uri    Request path (no query string)
     * @return array|null     [handler, params, middlewareGroup] or null
     */
    public function match(string $method, string $uri): ?array
    {
        $method = strtoupper($method);

        // Normalize: strip trailing slash (keep root "/")
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['regex'], $uri, $matches)) {
                // Extract named parameters
                $params = [];
                foreach ($route['paramNames'] as $name) {
                    $params[$name] = $matches[$name] ?? null;
                }

                return [
                    $route['handler'],
                    $params,
                    $route['middlewareGroup'],
                ];
            }
        }

        return null;
    }

    // ──────────────────────────────────────────────────────────────
    // Pattern compilation
    // ──────────────────────────────────────────────────────────────

    /**
     * Convert a URL pattern with :param placeholders into a regex.
     *
     * Examples:
     *   /blog/:slug        => #^/blog/(?P<slug>[^/]+)$#
     *   /admin/blog/edit/:id => #^/admin/blog/edit/(?P<id>[^/]+)$#
     *   /                  => #^/$#
     *
     * @param  string $pattern
     * @return array  [regex, paramNames[]]
     */
    private function compilePattern(string $pattern): array
    {
        $paramNames = [];

        // Find all :param segments
        $regex = preg_replace_callback('#:([a-zA-Z_][a-zA-Z0-9_]*)#', function ($m) use (&$paramNames) {
            $paramNames[] = $m[1];
            return '(?P<' . $m[1] . '>[^/]+)';
        }, $pattern);

        // Escape forward slashes for the regex delimiter and anchor
        $regex = '#^' . $regex . '$#';

        return [$regex, $paramNames];
    }

    // ──────────────────────────────────────────────────────────────
    // Introspection
    // ──────────────────────────────────────────────────────────────

    /**
     * Return all registered routes (useful for debugging or generating a sitemap).
     *
     * @return array<string, array>
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
