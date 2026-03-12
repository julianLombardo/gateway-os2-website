<?php
/**
 * GatewayOS2 Website - Demo Controller
 *
 * Renders the interactive demo page.
 */

require_once BASE_DIR . '/lib/core/Controller.php';

class DemoController extends Controller
{
    /**
     * Display the demo page.
     */
    public function index(): void
    {
        $this->view('pages/demo', [
            'title' => 'Demo - GatewayOS2',
        ]);
    }
}
