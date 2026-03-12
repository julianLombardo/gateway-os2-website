<?php
/**
 * GatewayOS2 Website - CSRF Middleware
 *
 * Verifies that POST requests include a valid CSRF token matching
 * the one stored in the session. On failure, sets a flash error
 * and redirects back to the previous page.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/services/SessionManager.php';

class CsrfMiddleware
{
    /**
     * Verify CSRF token on POST requests.
     *
     * GET, HEAD, and OPTIONS requests pass through without checks.
     *
     * @param array    $request The request context array.
     * @param callable $next    The next middleware or controller.
     * @return mixed
     */
    public function handle(Request $request, Response $response, callable $next): void
    {
        if ($request->isPost()) {
            if (!SessionManager::verifyCsrf()) {
                SessionManager::flash('error', 'Invalid form submission. Please try again.');

                $referer = $_SERVER['HTTP_REFERER'] ?? '/';
                header('Location: ' . $referer);
                exit;
            }
        }

        $next();
    }
}
