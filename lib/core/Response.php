<?php
/**
 * GatewayOS2 Website - HTTP Response
 *
 * Fluent interface for building and sending HTTP responses
 * (HTML pages, JSON payloads, redirects).
 */

class Response
{
    /** @var int HTTP status code */
    private $statusCode = 200;

    /** @var array<string, string> Response headers */
    private $headers = [];

    /** @var bool Whether headers/body have already been sent by this instance */
    private $sent = false;

    // ──────────────────────────────────────────────────────────────
    // Status
    // ──────────────────────────────────────────────────────────────

    /**
     * Set the HTTP status code.
     *
     * @param  int $code
     * @return $this
     */
    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Get the current status code.
     */
    public function getStatus(): int
    {
        return $this->statusCode;
    }

    // ──────────────────────────────────────────────────────────────
    // Headers
    // ──────────────────────────────────────────────────────────────

    /**
     * Set a response header.
     *
     * @param  string $name
     * @param  string $value
     * @return $this
     */
    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    // ──────────────────────────────────────────────────────────────
    // Response types
    // ──────────────────────────────────────────────────────────────

    /**
     * Send an HTML response.
     *
     * @param string $content HTML body
     * @param int    $status  HTTP status code
     */
    public function html(string $content, int $status = 200): void
    {
        $this->statusCode = $status;
        $this->header('Content-Type', 'text/html; charset=UTF-8');
        $this->sendHeaders();
        echo $content;
        $this->sent = true;
    }

    /**
     * Send a JSON response.
     *
     * @param mixed $data   Data to encode
     * @param int   $status HTTP status code
     */
    public function json($data, int $status = 200): void
    {
        $this->statusCode = $status;
        $this->header('Content-Type', 'application/json; charset=UTF-8');
        $this->sendHeaders();
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->sent = true;
    }

    /**
     * Send a redirect response and terminate.
     *
     * @param string $url    Target URL
     * @param int    $status HTTP status code (302 = temporary, 301 = permanent)
     */
    public function redirect(string $url, int $status = 302): void
    {
        $this->statusCode = $status;
        $this->header('Location', $url);
        $this->sendHeaders();
        $this->sent = true;
        exit; // Redirects must halt execution
    }

    /**
     * Send a plain-text response.
     *
     * @param string $text
     * @param int    $status
     */
    public function text(string $text, int $status = 200): void
    {
        $this->statusCode = $status;
        $this->header('Content-Type', 'text/plain; charset=UTF-8');
        $this->sendHeaders();
        echo $text;
        $this->sent = true;
    }

    /**
     * Send a "no content" response (204).
     */
    public function noContent(): void
    {
        $this->statusCode = 204;
        $this->sendHeaders();
        $this->sent = true;
    }

    // ──────────────────────────────────────────────────────────────
    // Cookie helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Set a cookie.
     *
     * @param string $name
     * @param string $value
     * @param int    $days     Lifetime in days (0 = session cookie)
     * @param string $path
     * @param bool   $httpOnly
     * @param bool   $secure
     * @param string $sameSite 'Lax', 'Strict', or 'None'
     */
    public function cookie(
        string $name,
        string $value,
        int    $days = 0,
        string $path = '/',
        bool   $httpOnly = true,
        bool   $secure = true,
        string $sameSite = 'Lax'
    ): self {
        $expires = $days > 0 ? time() + ($days * 86400) : 0;

        setcookie($name, $value, [
            'expires'  => $expires,
            'path'     => $path,
            'httponly'  => $httpOnly,
            'secure'   => $secure,
            'samesite' => $sameSite,
        ]);

        return $this;
    }

    /**
     * Delete a cookie by setting its expiry in the past.
     */
    public function clearCookie(string $name, string $path = '/'): self
    {
        setcookie($name, '', [
            'expires'  => time() - 86400,
            'path'     => $path,
            'httponly'  => true,
            'secure'   => true,
            'samesite' => 'Lax',
        ]);

        return $this;
    }

    // ──────────────────────────────────────────────────────────────
    // Internal
    // ──────────────────────────────────────────────────────────────

    /**
     * Flush status line and headers to the client.
     */
    private function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        http_response_code($this->statusCode);

        // Security headers (always applied)
        $defaults = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options'        => 'DENY',
            'Referrer-Policy'        => 'strict-origin-when-cross-origin',
        ];

        foreach ($defaults as $name => $value) {
            if (!isset($this->headers[$name])) {
                $this->headers[$name] = $value;
            }
        }

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
    }

    /**
     * Whether this response has already been sent.
     */
    public function isSent(): bool
    {
        return $this->sent;
    }
}
