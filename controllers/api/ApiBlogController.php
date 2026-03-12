<?php
/**
 * GatewayOS2 Website - API Blog Controller
 *
 * Returns blog posts as JSON with optional tag filtering.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/BlogRepository.php';

class ApiBlogController extends Controller
{
    /**
     * Return a JSON list of published blog posts.
     *
     * Query params:
     *   ?tag=X - Filter posts by tag
     */
    public function index(): void
    {
        $repo = new BlogRepository();
        $tag  = $this->request->query('tag');

        $posts = $repo->all();

        // Filter by tag if specified
        if ($tag !== null && $tag !== '') {
            $tag   = trim($tag);
            $posts = array_filter($posts, function ($post) use ($tag) {
                $tags = $post['tags'] ?? [];
                return in_array($tag, $tags, true);
            });
            $posts = array_values($posts);
        }

        // Strip content body for the listing to reduce payload size
        $posts = array_map(function ($post) {
            return [
                'id'         => $post['id'] ?? null,
                'title'      => $post['title'] ?? '',
                'slug'       => $post['slug'] ?? '',
                'excerpt'    => $post['excerpt'] ?? '',
                'author'     => $post['author'] ?? '',
                'tags'       => $post['tags'] ?? [],
                'created_at' => $post['created_at'] ?? '',
                'updated_at' => $post['updated_at'] ?? '',
            ];
        }, $posts);

        $this->json([
            'posts' => $posts,
            'count' => count($posts),
            'tag'   => $tag,
        ]);
    }
}
