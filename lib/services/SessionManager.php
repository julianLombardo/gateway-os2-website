<?php
/**
 * GatewayOS2 Website - Session Manager
 *
 * Centralized session handling extracted from auth.php.
 * Manages session lifecycle, flash messages, CSRF tokens,
 * and user session state.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

class SessionManager
{
    /** @var bool Whether the session has been started by this class. */
    private static bool $started = false;

    /**
     * Start a session with secure cookie parameters.
     *
     * Safe to call multiple times; only starts once.
     */
    public static function startSession(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly'  => true,
            'samesite'  => 'Lax',
        ]);

        if (defined('SESSION_NAME')) {
            session_name(SESSION_NAME);
        }

        session_start();
        self::$started = true;
    }

    /**
     * Set a session value.
     *
     * @param string $key   Session key.
     * @param mixed  $value Value to store.
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value.
     *
     * @param string $key     Session key.
     * @param mixed  $default Fallback if key is not set.
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check whether a session key exists.
     *
     * @param string $key Session key.
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session key.
     *
     * @param string $key Session key.
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    // ── Flash Messages ────────────────────────────────────────────

    /**
     * Set a flash message (available on the next request only).
     *
     * @param string $type    Message type: 'success', 'error', 'info', 'warning'.
     * @param string $message The message text.
     */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Get and clear the current flash message.
     *
     * @return array|null ['type' => ..., 'message' => ...] or null.
     */
    public static function getFlash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    // ── CSRF Protection ───────────────────────────────────────────

    /**
     * Generate or return the current CSRF token.
     *
     * @return string 64-character hex token.
     */
    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Return an HTML hidden input field containing the CSRF token.
     *
     * @return string HTML <input> element.
     */
    public static function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::csrfToken()) . '">';
    }

    /**
     * Verify that the submitted CSRF token matches the session token.
     *
     * @return bool True if tokens match.
     */
    public static function verifyCsrf(): bool
    {
        if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    // ── User Session ──────────────────────────────────────────────

    /**
     * Check if a user is currently logged in.
     *
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Store user data in the session after successful login.
     *
     * @param array $user User record with 'id', 'username', 'display_name'.
     */
    public static function setUser(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['display_name'] = $user['display_name'] ?? $user['username'];
        if (isset($user['role'])) {
            $_SESSION['role'] = $user['role'];
        }
    }

    /**
     * Clear all user session data and destroy the session.
     */
    public static function clearUser(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires'  => time() - 42000,
                    'path'     => $params['path'],
                    'domain'   => $params['domain'],
                    'secure'   => $params['secure'],
                    'httponly'  => $params['httponly'],
                    'samesite' => $params['samesite'] ?? 'Lax',
                ]
            );
        }

        session_destroy();
        self::$started = false;
    }
}
