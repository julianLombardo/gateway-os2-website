<?php
/**
 * GatewayOS2 Website - Public Route Definitions
 *
 * Maps URL patterns to controller@method pairs.
 * Route parameters use :param syntax (e.g. /blog/:slug).
 *
 * Format: [HTTP_METHOD, URL_PATTERN, 'ControllerName@method', middleware_group]
 *
 * Middleware groups: 'web' (default), 'auth', 'api'
 */

return [
    // ── Public pages ──────────────────────────────────────────────
    ['GET',  '/',                'HomeController@index',         'web'],
    ['GET',  '/features',       'FeaturesController@index',     'web'],
    ['GET',  '/apps',           'AppsController@index',         'web'],
    ['GET',  '/guide',          'GuideController@index',        'web'],
    ['GET',  '/code',           'CodeController@index',         'web'],
    ['GET',  '/about',          'AboutController@index',        'web'],
    ['GET',  '/download',       'DownloadController@index',     'web'],

    // ── Blog ──────────────────────────────────────────────────────
    ['GET',  '/blog',           'BlogController@index',         'web'],
    ['GET',  '/blog/:slug',     'BlogController@show',          'web'],

    // ── Contact ───────────────────────────────────────────────────
    ['GET',  '/contact',        'ContactController@form',       'web'],
    ['POST', '/contact',        'ContactController@submit',     'web'],

    // ── Search ────────────────────────────────────────────────────
    ['GET',  '/search',         'SearchController@index',       'web'],

    // ── Authentication ────────────────────────────────────────────
    ['GET',  '/login',          'AuthController@loginForm',     'web'],
    ['POST', '/login',          'AuthController@login',         'web'],
    ['GET',  '/register',       'AuthController@registerForm',  'web'],
    ['POST', '/register',       'AuthController@register',      'web'],
    ['GET',  '/logout',         'AuthController@logout',        'web'],
    ['GET',  '/forgot-password','AuthController@forgotForm',    'web'],
    ['POST', '/forgot-password','AuthController@forgot',        'web'],
    ['GET',  '/reset-password', 'AuthController@resetForm',     'web'],
    ['POST', '/reset-password', 'AuthController@reset',         'web'],
    ['GET',  '/verify-email',   'AuthController@verify',        'web'],

    // ── Authenticated user ────────────────────────────────────────
    ['GET',  '/dashboard',      'DashboardController@index',    'auth'],
    ['POST', '/dashboard',      'DashboardController@update',   'auth'],

    // ── Demo ──────────────────────────────────────────────────────
    ['GET',  '/demo',           'DemoController@index',         'web'],

    // ── Public API endpoints ──────────────────────────────────────
    ['GET',  '/api/stats',      'ApiStatsController@index',     'api'],
    ['GET',  '/api/blog',       'ApiBlogController@index',      'api'],
    ['GET',  '/api/search',     'ApiSearchController@index',    'api'],
    ['POST', '/api/contact',    'ApiContactController@submit',  'api'],
];
