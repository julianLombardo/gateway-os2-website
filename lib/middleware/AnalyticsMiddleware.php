<?php
/**
 * GatewayOS2 Website - Analytics Middleware
 *
 * Tracks page views on every request using the AnalyticsService.
 * Runs as part of the global middleware stack.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/services/AnalyticsService.php';

class AnalyticsMiddleware
{
    /**
     * Track the current page view and pass the request along.
     *
     * Skips tracking for static asset requests (CSS, JS, images, fonts).
     *
     * @param array    $request The request context array.
     * @param callable $next    The next middleware or controller.
     * @return mixed
     */
    public function handle(Request $request, Response $response, callable $next): void
    {
        $path = $request->path();

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'map'];

        if (!in_array(strtolower($ext), $staticExtensions, true)) {
            AnalyticsService::track();
        }

        $next();
    }
}
