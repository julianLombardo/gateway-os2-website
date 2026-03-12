<?php
/**
 * GatewayOS2 Website - Features Controller
 *
 * Renders the features overview page.
 */

require_once BASE_DIR . '/lib/core/Controller.php';

class FeaturesController extends Controller
{
    /**
     * Display the features page.
     */
    public function index(): void
    {
        $this->view('pages/features', [
            'title' => 'Features - GatewayOS2',
        ]);
    }
}
