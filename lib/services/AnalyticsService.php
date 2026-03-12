<?php
/**
 * GatewayOS2 Website - Analytics Service
 *
 * Simple file-based page view tracking extracted from analytics.php.
 * Stores daily view counts and per-page breakdowns in analytics.json.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/helpers/JsonStore.php';

class AnalyticsService
{
    /**
     * Get the analytics data file path.
     *
     * @return string
     */
    private static function file(): string
    {
        return defined('ANALYTICS_FILE') ? ANALYTICS_FILE : BASE_DIR . '/data/analytics.json';
    }

    /**
     * Load the analytics data.
     *
     * @return array Analytics data with 'total_views' and 'daily' keys.
     */
    private static function load(): array
    {
        $data = JsonStore::read(self::file());
        if (empty($data)) {
            return ['total_views' => 0, 'daily' => []];
        }
        return $data;
    }

    /**
     * Save analytics data.
     *
     * @param array $data Analytics data.
     */
    private static function save(array $data): void
    {
        JsonStore::write(self::file(), $data);
    }

    /**
     * Track the current page view.
     *
     * Increments total views, daily views, and per-page counts.
     * Automatically prunes data older than 90 days.
     */
    public static function track(): void
    {
        $data = self::load();
        $today = date('Y-m-d');
        $page = $_SERVER['REQUEST_URI'] ?? '/';
        $page = strtok($page, '?'); // Strip query string

        if (!isset($data['daily'][$today])) {
            $data['daily'][$today] = ['views' => 0, 'pages' => []];
        }
        $data['daily'][$today]['views']++;

        if (!isset($data['daily'][$today]['pages'][$page])) {
            $data['daily'][$today]['pages'][$page] = 0;
        }
        $data['daily'][$today]['pages'][$page]++;

        $data['total_views'] = ($data['total_views'] ?? 0) + 1;

        // Keep only last 90 days
        $cutoff = date('Y-m-d', strtotime('-90 days'));
        foreach (array_keys($data['daily']) as $date) {
            if ($date < $cutoff) {
                unset($data['daily'][$date]);
            }
        }

        self::save($data);
    }

    /**
     * Get a summary of analytics data.
     *
     * @return array ['total' => int, 'today' => int, 'week' => int]
     */
    public static function summary(): array
    {
        $data = self::load();
        $today = date('Y-m-d');
        $weekAgo = date('Y-m-d', strtotime('-7 days'));

        $weekViews = 0;
        foreach ($data['daily'] as $date => $info) {
            if ($date >= $weekAgo) {
                $weekViews += $info['views'];
            }
        }

        return [
            'total' => $data['total_views'] ?? 0,
            'today' => $data['daily'][$today]['views'] ?? 0,
            'week'  => $weekViews,
        ];
    }

    /**
     * Get daily view counts for a specified number of days.
     *
     * @param int $days Number of days to include (default 30).
     * @return array Associative array of 'YYYY-MM-DD' => view_count.
     */
    public static function dailyStats(int $days = 30): array
    {
        $data = self::load();
        $stats = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $stats[$date] = $data['daily'][$date]['views'] ?? 0;
        }

        return $stats;
    }

    /**
     * Get the most viewed pages.
     *
     * @param int $limit Maximum number of pages to return (default 10).
     * @return array Associative array of page_path => total_views, sorted descending.
     */
    public static function topPages(int $limit = 10): array
    {
        $data = self::load();
        $pageTotals = [];

        foreach ($data['daily'] as $dayData) {
            foreach ($dayData['pages'] ?? [] as $page => $views) {
                $pageTotals[$page] = ($pageTotals[$page] ?? 0) + $views;
            }
        }

        arsort($pageTotals);
        return array_slice($pageTotals, 0, $limit, true);
    }
}
