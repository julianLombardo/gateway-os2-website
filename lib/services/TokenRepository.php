<?php
/**
 * GatewayOS2 Website - Token Repository
 *
 * Manages remember-me tokens and password reset codes.
 * Uses JsonStore for thread-safe file access.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/helpers/JsonStore.php';

class TokenRepository
{
    // ── Remember-Me Tokens ────────────────────────────────────────

    /**
     * Get the path to the tokens data file.
     *
     * @return string
     */
    private static function tokensFile(): string
    {
        return defined('TOKENS_FILE') ? TOKENS_FILE : BASE_DIR . '/data/tokens.json';
    }

    /**
     * Get the path to the reset codes data file.
     *
     * @return string
     */
    private static function resetCodesFile(): string
    {
        return defined('RESET_CODES_FILE') ? RESET_CODES_FILE : BASE_DIR . '/data/reset_codes.json';
    }

    /**
     * Create a remember-me token for a user.
     *
     * Generates a selector/validator pair, stores the hashed validator,
     * sets the cookie, and returns the token record.
     *
     * @param string $userId The user's ID.
     * @return array The stored token record.
     */
    public static function setRememberToken(string $userId): array
    {
        $selector  = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));
        $days      = defined('REMEMBER_DAYS') ? REMEMBER_DAYS : 30;
        $expires   = time() + ($days * 86400);

        $tokens = JsonStore::read(self::tokensFile());

        // Remove old tokens for this user
        $tokens = array_values(array_filter($tokens, function ($t) use ($userId) {
            return $t['user_id'] !== $userId;
        }));

        $record = [
            'selector'       => $selector,
            'validator_hash' => hash('sha256', $validator),
            'user_id'        => $userId,
            'expires'        => $expires,
        ];

        $tokens[] = $record;
        JsonStore::write(self::tokensFile(), $tokens);

        $cookieName = defined('REMEMBER_COOKIE') ? REMEMBER_COOKIE : 'gw_remember';
        $cookieValue = $selector . ':' . $validator;

        setcookie($cookieName, $cookieValue, [
            'expires'  => $expires,
            'path'     => '/',
            'httponly'  => true,
            'samesite'  => 'Lax',
        ]);

        return $record;
    }

    /**
     * Validate a remember-me cookie and restore the user session.
     *
     * On success, rotates the token for forward security and returns the
     * matching user ID. On failure, clears the invalid cookie.
     *
     * @return string|null The user ID if the token is valid, null otherwise.
     */
    public static function checkRememberToken(): ?string
    {
        $cookieName = defined('REMEMBER_COOKIE') ? REMEMBER_COOKIE : 'gw_remember';

        if (!isset($_COOKIE[$cookieName])) {
            return null;
        }

        $parts = explode(':', $_COOKIE[$cookieName], 2);
        if (count($parts) !== 2) {
            self::clearRememberToken();
            return null;
        }

        [$selector, $validator] = $parts;
        $tokens = JsonStore::read(self::tokensFile());

        foreach ($tokens as $i => $token) {
            if ($token['selector'] !== $selector) {
                continue;
            }

            // Expired
            if ($token['expires'] < time()) {
                unset($tokens[$i]);
                JsonStore::write(self::tokensFile(), array_values($tokens));
                self::clearRememberToken();
                return null;
            }

            // Valid
            if (hash_equals($token['validator_hash'], hash('sha256', $validator))) {
                $userId = $token['user_id'];

                // Rotate token for security
                unset($tokens[$i]);
                JsonStore::write(self::tokensFile(), array_values($tokens));
                self::setRememberToken($userId);

                return $userId;
            }
        }

        self::clearRememberToken();
        return null;
    }

    /**
     * Clear the remember-me cookie and remove the user's stored tokens.
     */
    public static function clearRememberToken(): void
    {
        $cookieName = defined('REMEMBER_COOKIE') ? REMEMBER_COOKIE : 'gw_remember';

        setcookie($cookieName, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly'  => true,
            'samesite'  => 'Lax',
        ]);
    }

    /**
     * Remove all stored tokens for a specific user.
     *
     * @param string $userId The user's ID.
     */
    public static function clearUserTokens(string $userId): void
    {
        $tokens = JsonStore::read(self::tokensFile());
        $tokens = array_values(array_filter($tokens, function ($t) use ($userId) {
            return $t['user_id'] !== $userId;
        }));
        JsonStore::write(self::tokensFile(), $tokens);
        self::clearRememberToken();
    }

    // ── Password Reset Codes ──────────────────────────────────────

    /**
     * Load all reset codes, pruning expired entries.
     *
     * @return array List of reset code records.
     */
    public static function loadResetCodes(): array
    {
        $codes = JsonStore::read(self::resetCodesFile());
        // Prune expired codes
        $codes = array_values(array_filter($codes, function ($c) {
            return $c['expires'] > time();
        }));
        return $codes;
    }

    /**
     * Save reset codes to the data file.
     *
     * @param array $codes List of reset code records.
     */
    public static function saveResetCodes(array $codes): void
    {
        // Filter out expired codes before saving
        $codes = array_values(array_filter($codes, function ($c) {
            return $c['expires'] > time();
        }));
        JsonStore::write(self::resetCodesFile(), $codes);
    }
}
