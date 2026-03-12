<?php
/**
 * GatewayOS2 Website - Guide Controller
 *
 * Renders the user guide / getting started page.
 */

require_once BASE_DIR . '/lib/core/Controller.php';

class GuideController extends Controller
{
    /**
     * Display the guide page.
     */
    public function index(): void
    {
        $this->view('pages/guide', [
            'title' => 'Guide - GatewayOS2',
        ]);
    }
}
