<?php
/**
 * GatewayOS2 Website - GitHub API Service
 *
 * Fetches repository statistics from the GitHub API with
 * file-based caching (1-hour TTL) to avoid rate limits.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/helpers/JsonStore.php';

class GitHubService
{
    /** @var string GitHub API base URL */
    private const API_BASE = 'https://api.github.com';

    /** @var int Cache TTL in seconds (1 hour) */
    private const CACHE_TTL = 3600;

    /**
     * Get the repository identifier.
     *
     * @return string Owner/repo string.
     */
    private static function repo(): string
    {
        return defined('GITHUB_REPO') ? GITHUB_REPO : 'julianLombardo/GatewayOS2';
    }

    /**
     * Get the cache file path.
     *
     * @return string Absolute path to the cache JSON file.
     */
    private static function cacheFile(): string
    {
        return (defined('CACHE_DIR') ? CACHE_DIR : BASE_DIR . '/data/cache') . '/github.json';
    }

    /**
     * Fetch basic repository statistics.
     *
     * @return array ['stars' => int, 'forks' => int, 'watchers' => int, 'open_issues' => int]
     */
    public static function getRepoStats(): array
    {
        $cached = self::getCached('repo_stats');
        if ($cached !== null) {
            return $cached;
        }

        $data = self::apiGet('/repos/' . self::repo());

        if ($data === null) {
            return ['stars' => 0, 'forks' => 0, 'watchers' => 0, 'open_issues' => 0];
        }

        $stats = [
            'stars'       => $data['stargazers_count'] ?? 0,
            'forks'       => $data['forks_count'] ?? 0,
            'watchers'    => $data['subscribers_count'] ?? 0,
            'open_issues' => $data['open_issues_count'] ?? 0,
        ];

        self::setCache('repo_stats', $stats);
        return $stats;
    }

    /**
     * Fetch the latest release tag name and publish date.
     *
     * @return array ['tag_name' => string, 'published_at' => string] or empty values.
     */
    public static function getLatestRelease(): array
    {
        $cached = self::getCached('latest_release');
        if ($cached !== null) {
            return $cached;
        }

        $data = self::apiGet('/repos/' . self::repo() . '/releases/latest');

        if ($data === null) {
            return ['tag_name' => '', 'published_at' => ''];
        }

        $release = [
            'tag_name'     => $data['tag_name'] ?? '',
            'published_at' => $data['published_at'] ?? '',
        ];

        self::setCache('latest_release', $release);
        return $release;
    }

    /**
     * Fetch the total commit count across all contributors.
     *
     * Uses the /contributors endpoint and sums the 'contributions' field.
     *
     * @return int Total number of commits.
     */
    public static function getCommitCount(): int
    {
        $cached = self::getCached('commit_count');
        if ($cached !== null) {
            return $cached;
        }

        $data = self::apiGet('/repos/' . self::repo() . '/contributors');

        if ($data === null || !is_array($data)) {
            return 0;
        }

        $total = 0;
        foreach ($data as $contributor) {
            $total += $contributor['contributions'] ?? 0;
        }

        self::setCache('commit_count', $total);
        return $total;
    }

    // ── HTTP Client ───────────────────────────────────────────────

    /**
     * Make a GET request to the GitHub API.
     *
     * @param string $endpoint API path (e.g. /repos/owner/repo).
     * @return array|null Decoded JSON response or null on failure.
     */
    private static function apiGet(string $endpoint): ?array
    {
        $url = self::API_BASE . $endpoint;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/vnd.github.v3+json',
                'User-Agent: GatewayOS2-Website/1.0',
            ],
        ]);

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $status !== 200) {
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }

    // ── File Cache ────────────────────────────────────────────────

    /**
     * Retrieve a cached value if it has not expired.
     *
     * @param string $key Cache key.
     * @return mixed Cached value or null if expired/missing.
     */
    private static function getCached(string $key): mixed
    {
        $cache = JsonStore::read(self::cacheFile());

        if (!isset($cache[$key]) || !isset($cache[$key]['expires'])) {
            return null;
        }

        if ($cache[$key]['expires'] < time()) {
            return null;
        }

        return $cache[$key]['data'] ?? null;
    }

    /**
     * Store a value in the cache with a TTL.
     *
     * @param string $key   Cache key.
     * @param mixed  $value Data to cache.
     */
    private static function setCache(string $key, mixed $value): void
    {
        $cache = JsonStore::read(self::cacheFile());

        $cache[$key] = [
            'data'    => $value,
            'expires' => time() + self::CACHE_TTL,
        ];

        JsonStore::write(self::cacheFile(), $cache);
    }
}
