<?php
/**
 * GatewayOS2 Website - Home Controller
 *
 * Renders the landing page with live GitHub repository statistics.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/GitHubService.php';

class HomeController extends Controller
{
    /**
     * Display the home page with cached GitHub stats.
     */
    public function index(): void
    {
        $github = new GitHubService();
        $stats  = $github->getRepoStats();

        $this->view('pages/home', [
            'title' => 'GatewayOS2 - Modern Gateway Operating System',
            'stats' => $stats,
        ]);
    }
}
