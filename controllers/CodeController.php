<?php
/**
 * GatewayOS2 Website - Code Controller
 *
 * Renders the source code / repository information page.
 */

require_once BASE_DIR . '/lib/core/Controller.php';

class CodeController extends Controller
{
    /**
     * Display the code page.
     */
    public function index(): void
    {
        $this->view('pages/code', [
            'title' => 'Code - GatewayOS2',
        ]);
    }
}
