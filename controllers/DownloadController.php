<?php
/**
 * GatewayOS2 Website - Download Controller
 *
 * Renders the download page with the latest GitHub release information.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/GitHubService.php';

class DownloadController extends Controller
{
    /**
     * Display the download page with latest release data from GitHub.
     */
    public function index(): void
    {
        $github  = new GitHubService();
        $release = $github->getLatestRelease();

        $this->view('pages/download', [
            'title'   => 'Download - GatewayOS2',
            'release' => $release,
        ]);
    }
}
