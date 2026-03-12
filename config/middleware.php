<?php
/**
 * GatewayOS2 Website - Middleware Stack Configuration
 *
 * Defines which middleware classes run for each route group.
 *
 * 'global' - Runs on every request before group-specific middleware.
 * 'web'    - Standard browser requests with CSRF protection.
 * 'auth'   - Requires an authenticated session.
 * 'admin'  - Requires authentication + admin role.
 * 'api'    - Stateless API endpoints with rate limiting.
 */

return [
    'global' => ['SessionMiddleware', 'AnalyticsMiddleware'],
    'web'    => ['CsrfMiddleware'],
    'auth'   => ['AuthMiddleware'],
    'admin'  => ['AuthMiddleware', 'AdminMiddleware'],
    'api'    => ['RateLimitMiddleware'],
];
