<?php
/**
 * GatewayOS2 Website - About Controller
 *
 * Renders the about / project information page.
 */

require_once BASE_DIR . '/lib/core/Controller.php';

class AboutController extends Controller
{
    /**
     * Display the about page.
     */
    public function index(): void
    {
        $this->view('pages/about', [
            'title' => 'About - GatewayOS2',
        ]);
    }
}
