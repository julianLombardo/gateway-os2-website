<?php
/**
 * GatewayOS2 Website - Authentication Service
 *
 * Core authentication logic extracted from auth.php.
 * Handles registration, login, logout, email verification,
 * and password reset flows.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/services/UserRepository.php';
require_once BASE_DIR . '/lib/services/TokenRepository.php';
require_once BASE_DIR . '/lib/services/SessionManager.php';

class AuthService
{
    /**
     * Register a new user account.
     *
     * Validates input, checks for duplicates, creates the user with a
     * hashed password and email verification token.
     *
     * @param string $username    Desired username.
     * @param string $email       Email address.
     * @param string $password    Plain-text password.
     * @param string $displayName Display name (falls back to username).
     * @return array ['success' => bool, 'errors' => [...], 'user' => [...], 'verification_token' => '...']
     */
    public static function register(string $username, string $email, string $password, string $displayName = ''): array
    {
        $errors = [];

        // Validate username
        $username = trim($username);
        if (strlen($username) < 3 || strlen($username) > 30) {
            $errors[] = 'Username must be 3-30 characters.';
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores.';
        }
        if (UserRepository::findByUsername($username)) {
            $errors[] = 'Username is already taken.';
        }

        // Validate email
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (UserRepository::findByEmail($email)) {
            $errors[] = 'An account with this email already exists.';
        }

        // Validate password
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Create user
        $verificationToken = bin2hex(random_bytes(32));

        $user = UserRepository::create([
            'username'           => $username,
            'email'              => $email,
            'display_name'       => trim($displayName) ?: $username,
            'password_hash'      => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            'email_verified'     => false,
            'verification_token' => hash('sha256', $verificationToken),
        ]);

        return [
            'success'            => true,
            'user'               => $user,
            'verification_token' => $verificationToken,
        ];
    }

    /**
     * Log a user in with username/email and password.
     *
     * On success, sets session data and optionally creates a remember-me token.
     *
     * @param string $username Username or email address.
     * @param string $password Plain-text password.
     * @param bool   $remember Whether to set a persistent remember-me cookie.
     * @return array ['success' => bool, 'errors' => [...], 'user' => [...]]
     */
    public static function login(string $username, string $password, bool $remember = false): array
    {
        // Try username first, then email
        $user = UserRepository::findByUsername($username);
        if (!$user) {
            $user = UserRepository::findByEmail($username);
        }

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'errors' => ['Invalid username or password.']];
        }

        // Set session
        SessionManager::setUser($user);

        // Remember me
        if ($remember) {
            TokenRepository::setRememberToken($user['id']);
        }

        return ['success' => true, 'user' => $user];
    }

    /**
     * Log the current user out.
     *
     * Clears remember-me tokens, destroys session, and removes cookies.
     */
    public static function logout(): void
    {
        $userId = SessionManager::get('user_id');

        if ($userId) {
            TokenRepository::clearUserTokens($userId);
        }

        TokenRepository::clearRememberToken();
        SessionManager::clearUser();
    }

    /**
     * Verify a user's email address using their verification token.
     *
     * @param string $token The raw verification token (will be hashed for comparison).
     * @return bool True if the token matched and email was verified.
     */
    public static function verifyEmail(string $token): bool
    {
        $tokenHash = hash('sha256', $token);
        $users = UserRepository::all();

        foreach ($users as $user) {
            if (isset($user['verification_token']) &&
                hash_equals($user['verification_token'], $tokenHash)) {
                UserRepository::update($user['id'], [
                    'email_verified'     => true,
                    'verification_token' => null,
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a 6-digit password reset code for an email address.
     *
     * The code is bcrypt-hashed before storage. Expires in 15 minutes.
     * To prevent user enumeration, callers should show the same UI
     * regardless of whether the email exists.
     *
     * @param string $email The user's email address.
     * @return array ['success' => bool, 'code' => '...', 'user' => [...]]
     *               code is only set if user exists.
     */
    public static function requestPasswordReset(string $email): array
    {
        $user = UserRepository::findByEmail($email);

        if (!$user) {
            // Don't reveal whether the email exists
            return ['success' => true, 'code' => null, 'user' => null];
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $codes = TokenRepository::loadResetCodes();
        // Remove existing codes for this user
        $codes = array_values(array_filter($codes, function ($c) use ($user) {
            return $c['user_id'] !== $user['id'];
        }));

        $codes[] = [
            'code_hash' => password_hash($code, PASSWORD_BCRYPT),
            'user_id'   => $user['id'],
            'email'     => $email,
            'expires'   => time() + 900, // 15 minutes
            'attempts'  => 0,
        ];

        TokenRepository::saveResetCodes($codes);

        return ['success' => true, 'code' => $code, 'user' => $user];
    }

    /**
     * Verify a password reset code.
     *
     * On success, generates a one-time reset token stored in the session.
     *
     * @param string $email The email the code was sent to.
     * @param string $code  The 6-digit code the user entered.
     * @return array ['success' => bool, 'error' => '...', 'token' => '...']
     */
    public static function verifyResetCode(string $email, string $code): array
    {
        $codes = TokenRepository::loadResetCodes();

        foreach ($codes as &$entry) {
            if ($entry['email'] !== $email || $entry['expires'] <= time()) {
                continue;
            }

            $entry['attempts']++;

            if ($entry['attempts'] > 5) {
                $entry['expires'] = 0; // Invalidate
                TokenRepository::saveResetCodes($codes);
                return [
                    'success' => false,
                    'error'   => 'Too many attempts. Please request a new code.',
                ];
            }

            if (password_verify($code, $entry['code_hash'])) {
                // Code verified — generate a session reset token
                $resetToken = bin2hex(random_bytes(32));
                SessionManager::set('reset_token', $resetToken);
                SessionManager::set('reset_user_id', $entry['user_id']);

                // Invalidate the code
                $entry['expires'] = 0;
                TokenRepository::saveResetCodes($codes);

                return ['success' => true, 'token' => $resetToken];
            }

            TokenRepository::saveResetCodes($codes);
            return ['success' => false, 'error' => 'Invalid or expired code.'];
        }
        unset($entry);

        return ['success' => false, 'error' => 'Invalid or expired code.'];
    }

    /**
     * Reset a user's password using a session-stored reset token.
     *
     * @param string $token       The reset token from the URL/form.
     * @param string $newPassword The new plain-text password.
     * @return array ['success' => bool, 'error' => '...']
     */
    public static function resetPassword(string $token, string $newPassword): array
    {
        $sessionToken = SessionManager::get('reset_token');
        $userId       = SessionManager::get('reset_user_id');

        if (!$sessionToken || !$userId || !hash_equals($sessionToken, $token)) {
            return ['success' => false, 'error' => 'Invalid or expired reset link.'];
        }

        if (strlen($newPassword) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters.'];
        }

        UserRepository::update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]),
        ]);

        // Clean up session
        SessionManager::remove('reset_token');
        SessionManager::remove('reset_user_id');
        SessionManager::remove('reset_step');
        SessionManager::remove('reset_email');

        return ['success' => true];
    }
}
