<?php
/**
 * GatewayOS2 Website - HTTP Request Wrapper
 *
 * Provides a clean, read-only interface over PHP superglobals
 * ($_GET, $_POST, $_COOKIE, $_SERVER) and route parameters.
 */

class Request
{
    /** @var array Route parameters populated by the router (e.g. ['slug' => 'hello-world']) */
    public $params = [];

    /** @var string Cached request method */
    private $method;

    /** @var string Cached request path (without query string) */
    private $path;

    /** @var string Cached full URI */
    private $uri;

    // ──────────────────────────────────────────────────────────────
    // Constructor
    // ──────────────────────────────────────────────────────────────

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri    = $_SERVER['REQUEST_URI'] ?? '/';
        $this->path   = $this->parsePath($this->uri);
    }

    // ──────────────────────────────────────────────────────────────
    // HTTP method / URL
    // ──────────────────────────────────────────────────────────────

    /**
     * HTTP method in uppercase (GET, POST, PUT, DELETE, etc.).
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Full request URI including query string.
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * Request path without query string, e.g. "/blog/hello-world".
     */
    public function path(): string
    {
        return $this->path;
    }

    // ──────────────────────────────────────────────────────────────
    // Input accessors
    // ──────────────────────────────────────────────────────────────

    /**
     * Retrieve a value from the query string ($_GET).
     *
     * @param  string      $key
     * @param  mixed       $default
     * @return mixed
     */
    public function query(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Retrieve a value from POST data ($_POST).
     *
     * @param  string      $key
     * @param  mixed       $default
     * @return mixed
     */
    public function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Retrieve a cookie value.
     *
     * @param  string      $key
     * @param  mixed       $default
     * @return mixed
     */
    public function cookie(string $key, $default = null)
    {
        return $_COOKIE[$key] ?? $default;
    }

    /**
     * Retrieve a $_SERVER value.
     *
     * @param  string      $key
     * @param  mixed       $default
     * @return mixed
     */
    public function server(string $key, $default = null)
    {
        return $_SERVER[$key] ?? $default;
    }

    /**
     * Retrieve a route parameter set by the router.
     *
     * @param  string      $key
     * @param  mixed       $default
     * @return mixed
     */
    public function param(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Retrieve all POST data as an associative array.
     *
     * @return array
     */
    public function allPost(): array
    {
        return $_POST;
    }

    /**
     * Retrieve all query data as an associative array.
     *
     * @return array
     */
    public function allQuery(): array
    {
        return $_GET;
    }

    /**
     * Read the raw request body (useful for JSON APIs).
     *
     * @return string
     */
    public function body(): string
    {
        return file_get_contents('php://input') ?: '';
    }

    /**
     * Decode a JSON request body into an associative array.
     *
     * @return array|null
     */
    public function json(): ?array
    {
        $decoded = json_decode($this->body(), true);
        return is_array($decoded) ? $decoded : null;
    }

    // ──────────────────────────────────────────────────────────────
    // Convenience checks
    // ──────────────────────────────────────────────────────────────

    /**
     * Is this a POST request?
     */
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Is this a GET request?
     */
    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    /**
     * Is this an AJAX / XMLHttpRequest?
     */
    public function isAjax(): bool
    {
        return strtolower($this->server('HTTP_X_REQUESTED_WITH', '')) === 'xmlhttprequest';
    }

    /**
     * Return the client's IP address, respecting common proxy headers.
     */
    public function ip(): string
    {
        // Cloudflare
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        // Standard proxy
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Return the User-Agent string.
     */
    public function userAgent(): string
    {
        return $this->server('HTTP_USER_AGENT', '');
    }

    // ──────────────────────────────────────────────────────────────
    // Internal
    // ──────────────────────────────────────────────────────────────

    /**
     * Extract the path component from a URI, stripping query string
     * and normalizing trailing slashes.
     */
    private function parsePath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Remove trailing slash but keep root
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
