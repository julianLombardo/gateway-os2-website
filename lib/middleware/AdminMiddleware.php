<?php
/**
 * GatewayOS2 Website - Admin Middleware
 *
 * Ensures the authenticated user has the 'admin' role.
 * Must be used after AuthMiddleware in the stack.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/services/SessionManager.php';
require_once BASE_DIR . '/lib/services/UserRepository.php';

class AdminMiddleware
{
    /**
     * Check if the logged-in user has admin privileges.
     *
     * If the user's role is not 'admin', redirects to /dashboard
     * with a flash error message.
     *
     * @param array    $request The request context array.
     * @param callable $next    The next middleware or controller.
     * @return mixed
     */
    public function handle(Request $request, Response $response, callable $next): void
    {
        $role = SessionManager::get('role');

        if ($role !== 'admin') {
            $userId = SessionManager::get('user_id');
            if ($userId) {
                $user = UserRepository::find($userId);
                if ($user && ($user['role'] ?? '') === 'admin') {
                    SessionManager::set('role', 'admin');
                    $next();
                    return;
                }
            }

            SessionManager::flash('error', 'You do not have permission to access this area.');
            header('Location: /dashboard');
            exit;
        }

        $next();
    }
}
