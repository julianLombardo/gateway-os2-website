<?php
/**
 * GatewayOS2 Website - Blog Controller
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/BlogRepository.php';
require_once BASE_DIR . '/lib/helpers/Pagination.php';

class BlogController extends Controller
{
    public function index(): void
    {
        $tag  = $this->request->query('tag');
        $page = max(1, (int) $this->request->query('page', 1));

        $posts = BlogRepository::all();

        if ($tag !== null && $tag !== '') {
            $tag   = trim($tag);
            $posts = array_filter($posts, function ($post) use ($tag) {
                $tags = $post['tags'] ?? [];
                return in_array($tag, $tags, true);
            });
            $posts = array_values($posts);
        }

        $pagination = Pagination::paginate($posts, $page, 10);

        $this->view('pages/blog/index', [
            'title'      => $tag ? "Posts tagged \"{$tag}\"" : 'Blog',
            'posts'      => $pagination['items'],
            'pagination' => $pagination,
            'currentTag' => $tag,
        ]);
    }

    public function show(): void
    {
        $slug = $this->request->param('slug');
        $post = BlogRepository::findBySlug($slug);

        if ($post === null) {
            $this->view('pages/404', ['title' => 'Post Not Found'], 'main', 404);
            return;
        }

        $renderedContent = BlogRepository::renderContent($post['content'] ?? '');

        $this->view('pages/blog/show', [
            'title'           => $post['title'] ?? 'Blog Post',
            'post'            => $post,
            'renderedContent' => $renderedContent,
        ]);
    }
}
