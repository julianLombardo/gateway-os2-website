<?php
/**
 * GatewayOS2 Website - Blog Repository
 *
 * CRUD operations for blog posts stored in data/blog/posts.json.
 * Includes the markdown-lite content renderer from blog.php.
 */

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__, 2));
}

require_once BASE_DIR . '/lib/helpers/JsonStore.php';

class BlogRepository
{
    /**
     * Get the path to the blog posts data file.
     *
     * @return string
     */
    private static function file(): string
    {
        return (defined('BLOG_DIR') ? BLOG_DIR : BASE_DIR . '/data/blog') . '/posts.json';
    }

    /**
     * Return all blog posts sorted by date descending.
     *
     * @return array
     */
    public static function all(): array
    {
        $posts = JsonStore::read(self::file());

        usort($posts, function ($a, $b) {
            return strcmp($b['date'] ?? '', $a['date'] ?? '');
        });

        return $posts;
    }

    /**
     * Find a post by its ID.
     *
     * @param string $id Post ID.
     * @return array|null Post record or null.
     */
    public static function find(string $id): ?array
    {
        $posts = JsonStore::read(self::file());
        foreach ($posts as $post) {
            if ($post['id'] === $id) {
                return $post;
            }
        }
        return null;
    }

    /**
     * Find a post by slug (same as the id field in this system).
     *
     * @param string $slug Post slug/ID.
     * @return array|null Post record or null.
     */
    public static function findBySlug(string $slug): ?array
    {
        return self::find($slug);
    }

    /**
     * Find all posts that contain a given tag.
     *
     * @param string $tag Tag name to filter by.
     * @return array Matching posts sorted by date descending.
     */
    public static function findByTag(string $tag): array
    {
        $posts = self::all();
        return array_values(array_filter($posts, function ($post) use ($tag) {
            return in_array($tag, $post['tags'] ?? [], true);
        }));
    }

    /**
     * Create a new blog post.
     *
     * @param array $data Post data (title, content, excerpt, author, tags, etc.)
     * @return array The created post record.
     */
    public static function create(array $data): array
    {
        $posts = JsonStore::read(self::file());

        $post = array_merge([
            'id'         => $data['id'] ?? bin2hex(random_bytes(8)),
            'date'       => date('c'),
            'tags'       => [],
            'author'     => 'GatewayOS2',
            'excerpt'    => '',
            'content'    => '',
        ], $data);

        $posts[] = $post;
        JsonStore::write(self::file(), $posts);

        return $post;
    }

    /**
     * Update an existing blog post.
     *
     * @param string $id   Post ID.
     * @param array  $data Fields to update.
     * @return array|null Updated post record or null if not found.
     */
    public static function update(string $id, array $data): ?array
    {
        $posts = JsonStore::read(self::file());
        $updated = null;

        foreach ($posts as &$post) {
            if ($post['id'] === $id) {
                foreach ($data as $key => $value) {
                    $post[$key] = $value;
                }
                $post['updated_at'] = date('c');
                $updated = $post;
                break;
            }
        }
        unset($post);

        if ($updated !== null) {
            JsonStore::write(self::file(), $posts);
        }

        return $updated;
    }

    /**
     * Delete a blog post by ID.
     *
     * @param string $id Post ID.
     * @return bool True if the post was found and removed.
     */
    public static function delete(string $id): bool
    {
        $posts = JsonStore::read(self::file());
        $original = count($posts);

        $posts = array_values(array_filter($posts, function ($post) use ($id) {
            return $post['id'] !== $id;
        }));

        if (count($posts) < $original) {
            JsonStore::write(self::file(), $posts);
            return true;
        }

        return false;
    }

    /**
     * Render markdown-lite blog content to HTML.
     *
     * Supports: ## headings, - unordered lists, numbered lists,
     * **bold**, *italic*, `code`, [links](url), blank lines as <br>.
     *
     * This is the exact rendering logic from blog.php.
     *
     * @param string $text Raw markdown-lite text.
     * @return string Rendered HTML.
     */
    public static function renderContent(string $text): string
    {
        $lines = explode("\n", $text);
        $html = '';
        $in_list = false;

        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('/^## (.+)$/', $line, $m)) {
                if ($in_list) { $html .= '</ul>'; $in_list = false; }
                $html .= '<h3>' . htmlspecialchars($m[1]) . '</h3>';
            } elseif (preg_match('/^- (.+)$/', $line, $m)) {
                if (!$in_list) { $html .= '<ul class="guide-list" style="color: var(--smoke);">'; $in_list = true; }
                $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', htmlspecialchars($m[1]));
                $html .= '<li>' . $content . '</li>';
            } elseif (preg_match('/^\d+\. (.+)$/', $line, $m)) {
                if (!$in_list) { $html .= '<ol style="color: var(--smoke); padding-left: 1.25rem;">'; $in_list = true; }
                $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', htmlspecialchars($m[1]));
                $html .= '<li>' . $content . '</li>';
            } elseif ($line === '') {
                if ($in_list) { $html .= $in_list ? '</ul>' : '</ol>'; $in_list = false; }
                $html .= '<br>';
            } else {
                if ($in_list) { $html .= '</ul>'; $in_list = false; }
                $processed = htmlspecialchars($line);
                $processed = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $processed);
                $processed = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $processed);
                $processed = preg_replace('/`(.+?)`/', '<code>$1</code>', $processed);
                $processed = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $processed);
                $html .= '<p>' . $processed . '</p>';
            }
        }
        if ($in_list) $html .= '</ul>';
        return $html;
    }
}
