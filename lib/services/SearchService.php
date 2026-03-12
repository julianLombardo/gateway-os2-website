<?php
/**
 * GatewayOS2 Website - Search Service
 *
 * Full-text search across static pages and blog posts.
 * Uses the same search logic as public/search.php.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/services/BlogRepository.php';

class SearchService
{
    /**
     * Static page definitions for search indexing.
     *
     * @return array List of page records with title, url, file, and desc.
     */
    private static function pages(): array
    {
        $publicDir = BASE_DIR . '/public';

        return [
            [
                'title' => 'Home',
                'url'   => '/',
                'file'  => $publicDir . '/index.php',
                'desc'  => 'GatewayOS2 landing page — overview, features, and quick start.',
            ],
            [
                'title' => 'Features',
                'url'   => '/features',
                'file'  => $publicDir . '/features.php',
                'desc'  => 'Graphics, networking, storage, PE32 loader, and advanced capabilities.',
            ],
            [
                'title' => 'Applications',
                'url'   => '/apps',
                'file'  => $publicDir . '/apps.php',
                'desc'  => '50+ built-in apps: productivity, games, security, dev tools.',
            ],
            [
                'title' => 'How It Works',
                'url'   => '/guide',
                'file'  => $publicDir . '/guide.php',
                'desc'  => 'Boot sequence, architecture, subsystem deep dives, file structure.',
            ],
            [
                'title' => 'Code Explorer',
                'url'   => '/code',
                'file'  => $publicDir . '/code.php',
                'desc'  => 'Annotated source code walkthroughs: bootloader, kernel, graphics, networking.',
            ],
            [
                'title' => 'About',
                'url'   => '/about',
                'file'  => $publicDir . '/about.php',
                'desc'  => 'GateWay Software, Julian Lombardo, project timeline.',
            ],
            [
                'title' => 'Download',
                'url'   => '/download',
                'file'  => $publicDir . '/download.php',
                'desc'  => 'Download GatewayOS2, build instructions, release info.',
            ],
            [
                'title' => 'Contact',
                'url'   => '/contact',
                'file'  => $publicDir . '/contact.php',
                'desc'  => 'Get in touch with GateWay Software.',
            ],
        ];
    }

    /**
     * Search pages and blog posts for a query string.
     *
     * Requires at least 2 characters. Returns an array of results,
     * each with type, title, url, desc, and optionally date.
     *
     * @param string $query Search query.
     * @return array List of matching results.
     */
    public static function search(string $query): array
    {
        $query = trim($query);
        $results = [];

        if (strlen($query) < 2) {
            return $results;
        }

        $qLower = strtolower($query);

        // Search static pages
        foreach (self::pages() as $page) {
            $content = @file_get_contents($page['file']);
            $contentLower = strtolower(strip_tags($content ?: ''));

            if (strpos($contentLower, $qLower) !== false ||
                strpos(strtolower($page['title']), $qLower) !== false ||
                strpos(strtolower($page['desc']), $qLower) !== false) {
                $results[] = [
                    'type'  => 'page',
                    'title' => $page['title'],
                    'url'   => $page['url'],
                    'desc'  => $page['desc'],
                ];
            }
        }

        // Search blog posts
        $posts = BlogRepository::all();
        foreach ($posts as $post) {
            $searchable = strtolower(
                ($post['title'] ?? '') . ' ' .
                ($post['excerpt'] ?? '') . ' ' .
                ($post['content'] ?? '') . ' ' .
                implode(' ', $post['tags'] ?? [])
            );

            if (strpos($searchable, $qLower) !== false) {
                $results[] = [
                    'type'  => 'blog',
                    'title' => $post['title'] ?? '',
                    'url'   => '/blog/' . urlencode($post['id'] ?? ''),
                    'desc'  => $post['excerpt'] ?? '',
                    'date'  => $post['date'] ?? '',
                ];
            }
        }

        return $results;
    }
}
