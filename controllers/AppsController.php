<?php
/**
 * GatewayOS2 Website - Apps Controller
 *
 * Renders the applications showcase page.
 */

require_once BASE_DIR . '/lib/core/Controller.php';

class AppsController extends Controller
{
    /**
     * Display the apps page.
     */
    public function index(): void
    {
        $this->view('pages/apps', [
            'title' => 'Apps - GatewayOS2',
        ]);
    }
}
