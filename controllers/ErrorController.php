<?php
/**
 * GatewayOS2 Website - Error Controller
 *
 * Renders error pages for 404 (Not Found) and 500 (Internal Server Error).
 */

require_once BASE_DIR . '/lib/core/Controller.php';

class ErrorController extends Controller
{
    /**
     * Render the 404 Not Found page.
     */
    public function notFound(): void
    {
        $this->view('pages/404', [
            'title' => 'Page Not Found - GatewayOS2',
        ], 'main', 404);
    }

    /**
     * Render the 500 Internal Server Error page.
     */
    public function serverError(): void
    {
        $this->view('pages/500', [
            'title' => 'Server Error - GatewayOS2',
        ], 'main', 500);
    }
}
