<?php
/**
 * GatewayOS2 Website - Auth Middleware
 *
 * Ensures the user is authenticated before proceeding.
 * Checks both session state and remember-me tokens.
 * Redirects unauthenticated users to /login with a redirect param.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/services/SessionManager.php';
require_once BASE_DIR . '/lib/services/TokenRepository.php';
require_once BASE_DIR . '/lib/services/UserRepository.php';

class AuthMiddleware
{
    /**
     * Check if the user is logged in.
     *
     * If not, redirect to /login with a redirect query parameter
     * so the user can be sent back after logging in.
     *
     * @param array    $request The request context array.
     * @param callable $next    The next middleware or controller.
     * @return mixed
     */
    public function handle(Request $request, Response $response, callable $next): void
    {
        if (SessionManager::isLoggedIn()) {
            $next();
            return;
        }

        $userId = TokenRepository::checkRememberToken();
        if ($userId !== null) {
            $user = UserRepository::find($userId);
            if ($user) {
                SessionManager::setUser($user);
                $next();
                return;
            }
        }

        $currentPath = $request->path();
        $redirectUrl = '/login?redirect=' . urlencode($currentPath);

        SessionManager::flash('error', 'Please log in to access this page.');
        header('Location: ' . $redirectUrl);
        exit;
    }
}
