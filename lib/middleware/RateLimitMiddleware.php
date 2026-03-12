<?php
/**
 * GatewayOS2 Website - Rate Limit Middleware
 *
 * Simple session-based rate limiting for API routes.
 * Tracks requests per IP address and enforces a configurable
 * maximum number of requests per time window.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/services/SessionManager.php';

class RateLimitMiddleware
{
    /**
     * Check the current request against the rate limit.
     *
     * Uses session storage keyed by client IP. When the limit is
     * exceeded, returns a 429 Too Many Requests JSON response.
     *
     * @param array    $request The request context array.
     * @param callable $next    The next middleware or controller.
     * @return mixed
     */
    public function handle(Request $request, Response $response, callable $next): void
    {
        $window     = defined('RATE_LIMIT_WINDOW') ? RATE_LIMIT_WINDOW : 60;
        $maxRequests = defined('RATE_LIMIT_MAX_REQUESTS') ? RATE_LIMIT_MAX_REQUESTS : 60;

        $ip = $request->ip();
        $key = 'rate_limit_' . md5($ip);
        $now = time();

        $data = SessionManager::get($key, ['requests' => [], 'blocked_until' => 0]);

        if ($data['blocked_until'] > $now) {
            $retryAfter = $data['blocked_until'] - $now;
            self::reject($retryAfter);
        }

        $data['requests'] = array_values(array_filter(
            $data['requests'],
            function ($timestamp) use ($now, $window) {
                return $timestamp > ($now - $window);
            }
        ));

        if (count($data['requests']) >= $maxRequests) {
            $data['blocked_until'] = $now + $window;
            SessionManager::set($key, $data);
            self::reject($window);
        }

        $data['requests'][] = $now;
        $data['blocked_until'] = 0;
        SessionManager::set($key, $data);

        $remaining = $maxRequests - count($data['requests']);
        header('X-RateLimit-Limit: ' . $maxRequests);
        header('X-RateLimit-Remaining: ' . max(0, $remaining));
        header('X-RateLimit-Reset: ' . ($now + $window));

        $next();
    }

    /**
     * Send a 429 response and exit.
     *
     * @param int $retryAfter Seconds until the client can retry.
     */
    private static function reject(int $retryAfter): never
    {
        http_response_code(429);
        header('Content-Type: application/json');
        header('Retry-After: ' . $retryAfter);

        echo json_encode([
            'error'       => 'Too many requests. Please try again later.',
            'retry_after' => $retryAfter,
        ]);

        exit;
    }
}
