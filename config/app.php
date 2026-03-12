<?php
/**
 * GatewayOS2 Website - Application Configuration
 *
 * Central configuration constants for the entire application.
 * All paths resolve relative to BASE_DIR (defined in public/index.php).
 *
 * Environment overrides: create data/env.json with any keys below to override.
 * Example: {"SITE_URL":"https://mydomain.com","TURNSTILE_SITE_KEY":"0x..."}
 */

// Load environment overrides (not committed to repo)
$_ENV_FILE = BASE_DIR . '/data/env.json';
$_ENV_OVERRIDES = [];
if (file_exists($_ENV_FILE)) {
    $_ENV_OVERRIDES = json_decode(file_get_contents($_ENV_FILE), true) ?: [];
}

function env(string $key, $default = '') {
    global $_ENV_OVERRIDES;
    return $_ENV_OVERRIDES[$key] ?? getenv($key) ?: $default;
}

// Environment mode
define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', env('APP_DEBUG', false));

// Site identity
define('SITE_URL', env('SITE_URL', 'https://gatewayos2.com'));
define('GITHUB_REPO', env('GITHUB_REPO', 'julianLombardo/GatewayOS2'));

// Cloudflare Turnstile (anti-bot)
define('TURNSTILE_SITE_KEY', env('TURNSTILE_SITE_KEY'));
define('TURNSTILE_SECRET_KEY', env('TURNSTILE_SECRET_KEY'));

// Umami analytics
define('UMAMI_ID', env('UMAMI_ID'));

// Data storage paths
define('DATA_DIR', BASE_DIR . '/data');
define('USERS_FILE', DATA_DIR . '/users.json');
define('TOKENS_FILE', DATA_DIR . '/tokens.json');
define('ANALYTICS_FILE', DATA_DIR . '/analytics.json');
define('MAIL_CONFIG_FILE', DATA_DIR . '/mail_config.json');
define('RESET_CODES_FILE', DATA_DIR . '/reset_codes.json');

// Content directories
define('BLOG_DIR', DATA_DIR . '/blog');
define('MESSAGES_DIR', DATA_DIR . '/messages');
define('CACHE_DIR', DATA_DIR . '/cache');

// Authentication
define('REMEMBER_COOKIE', 'gw_remember');
define('REMEMBER_DAYS', 30);

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'gw_session');

// Rate limiting
define('RATE_LIMIT_WINDOW', 60);    // seconds
define('RATE_LIMIT_MAX_REQUESTS', 60); // per window

// Production error handling
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', DATA_DIR . '/error.log');
}
