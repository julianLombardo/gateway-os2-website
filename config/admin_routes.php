<?php
/**
 * GatewayOS2 Website - Admin Route Definitions
 *
 * All admin routes require the 'admin' middleware group,
 * which enforces both authentication and admin role checks.
 *
 * Format: [HTTP_METHOD, URL_PATTERN, 'ControllerName@method', middleware_group]
 */

return [
    // ── Admin dashboard ───────────────────────────────────────────
    ['GET',  '/admin',                      'AdminDashboardController@index',   'admin'],

    // ── Blog management ───────────────────────────────────────────
    ['GET',  '/admin/blog',                 'AdminBlogController@index',        'admin'],
    ['GET',  '/admin/blog/create',          'AdminBlogController@create',       'admin'],
    ['POST', '/admin/blog/create',          'AdminBlogController@store',        'admin'],
    ['GET',  '/admin/blog/edit/:id',        'AdminBlogController@edit',         'admin'],
    ['POST', '/admin/blog/edit/:id',        'AdminBlogController@update',       'admin'],
    ['POST', '/admin/blog/delete/:id',      'AdminBlogController@delete',       'admin'],

    // ── Message management ────────────────────────────────────────
    ['GET',  '/admin/messages',             'AdminMessageController@index',     'admin'],
    ['GET',  '/admin/messages/:id',         'AdminMessageController@show',      'admin'],
    ['POST', '/admin/messages/:id/read',    'AdminMessageController@markRead',  'admin'],
    ['POST', '/admin/messages/:id/delete',  'AdminMessageController@delete',    'admin'],

    // ── User management ───────────────────────────────────────────
    ['GET',  '/admin/users',                'AdminUserController@index',        'admin'],
    ['POST', '/admin/users/:id/role',       'AdminUserController@updateRole',   'admin'],
    ['POST', '/admin/users/:id/delete',     'AdminUserController@delete',       'admin'],

    // ── Analytics ─────────────────────────────────────────────────
    ['GET',  '/admin/analytics',            'AdminAnalyticsController@index',   'admin'],
];
