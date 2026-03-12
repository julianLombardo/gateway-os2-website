<?php
/**
 * GatewayOS2 Website - API Stats Controller
 *
 * Returns project statistics as JSON, including both
 * static project data and live GitHub repository metrics.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/GitHubService.php';
require_once BASE_DIR . '/lib/helpers/JsonStore.php';

class ApiStatsController extends Controller
{
    /**
     * Return JSON project statistics.
     *
     * Combines static project stats with live GitHub data
     * (stars, forks, open issues, latest release).
     */
    public function index(): void
    {
        $github     = new GitHubService();
        $githubData = $github->getRepoStats();

        // Static project information
        $stats = [
            'project'    => 'GatewayOS2',
            'version'    => $githubData['latest_release'] ?? 'dev',
            'repository' => 'https://github.com/' . GITHUB_REPO,
            'github'     => [
                'stars'       => $githubData['stars'] ?? 0,
                'forks'       => $githubData['forks'] ?? 0,
                'open_issues' => $githubData['open_issues'] ?? 0,
                'watchers'    => $githubData['watchers'] ?? 0,
            ],
            'community'  => [
                'contributors' => $githubData['contributors'] ?? 0,
                'commits'      => $githubData['commits'] ?? 0,
            ],
            'generated_at' => date('c'),
        ];

        $this->json($stats);
    }
}
