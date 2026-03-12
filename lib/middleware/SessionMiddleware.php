<?php
/**
 * GatewayOS2 Website - Session Middleware
 *
 * Starts a secure session on every request and checks for
 * remember-me tokens to restore logged-out sessions.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/services/SessionManager.php';
require_once BASE_DIR . '/lib/services/TokenRepository.php';
require_once BASE_DIR . '/lib/services/UserRepository.php';

class SessionMiddleware
{
    /**
     * Start the session and check remember-me tokens.
     *
     * @param array    $request The request context array.
     * @param callable $next    The next middleware or controller.
     * @return mixed
     */
    public function handle(Request $request, Response $response, callable $next): void
    {
        // Start session with secure cookie params
        SessionManager::startSession();

        // If user is not logged in, check for a remember-me token
        if (!SessionManager::isLoggedIn()) {
            $userId = TokenRepository::checkRememberToken();

            if ($userId !== null) {
                $user = UserRepository::find($userId);
                if ($user) {
                    SessionManager::setUser($user);
                }
            }
        }

        $next();
    }
}
