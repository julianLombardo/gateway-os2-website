<?php
/**
 * Router for PHP built-in server.
 * Delegates all non-static requests to index.php (front controller).
 */
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$file = __DIR__ . $path;

// Serve static files directly (CSS, JS, images, etc.)
if ($path !== '/' && is_file($file)) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if ($ext !== 'php') {
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'xml' => 'application/xml',
        ];
        if (isset($mime_types[$ext])) {
            header('Content-Type: ' . $mime_types[$ext]);
            readfile($file);
            return true;
        }
        return false;
    }
}

// Everything else goes through the front controller
require __DIR__ . '/index.php';
return true;
