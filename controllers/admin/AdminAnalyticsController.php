<?php
/**
 * GatewayOS2 Website - Admin Analytics Controller
 *
 * Displays site analytics: summary metrics, daily visitor stats,
 * and top-viewed pages.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/AnalyticsService.php';

class AdminAnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function index(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $analytics = new AnalyticsService();

        $summary    = $analytics->summary();
        $dailyStats = $analytics->dailyStats();
        $topPages   = $analytics->topPages();

        $this->view('admin/analytics/index', [
            'title'      => 'Analytics - GatewayOS2',
            'summary'    => $summary,
            'dailyStats' => $dailyStats,
            'topPages'   => $topPages,
        ], 'admin');
    }
}
