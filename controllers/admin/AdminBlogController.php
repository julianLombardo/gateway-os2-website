<?php
/**
 * GatewayOS2 Website - Admin Blog Controller
 *
 * Full CRUD management for blog posts: list, create, edit, update, delete.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/BlogRepository.php';
require_once BASE_DIR . '/lib/services/SessionManager.php';
require_once BASE_DIR . '/lib/helpers/Validator.php';
require_once BASE_DIR . '/lib/helpers/Sanitizer.php';

class AdminBlogController extends Controller
{
    /**
     * List all blog posts for administration.
     */
    public function index(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $repo  = new BlogRepository();
        $posts = $repo->all();

        $this->view('admin/blog/index', [
            'title' => 'Manage Blog - GatewayOS2',
            'posts' => $posts,
            'flash' => $this->getFlash(),
        ], 'admin');
    }

    /**
     * Display the blog post creation form.
     */
    public function create(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $this->view('admin/blog/editor', [
            'title'  => 'New Post - GatewayOS2',
            'post'   => null,
            'isEdit' => false,
            'flash'  => $this->getFlash(),
        ], 'admin');
    }

    /**
     * Process the blog post creation form.
     */
    public function store(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        if (!SessionManager::verifyCsrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/admin/blog/create');
            return;
        }

        $validation = Validator::validate($_POST, [
            'title'   => 'required|max:200',
            'content' => 'required',
        ]);

        if (!$validation['valid']) {
            $errors = Validator::flatten($validation['errors']);
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/admin/blog/create');
            return;
        }

        $user = $this->currentUser();
        $tags = $this->parseTags($this->request->post('tags', ''));

        $postData = [
            'title'     => Sanitizer::trim($this->request->post('title', '')),
            'slug'      => Sanitizer::slug($this->request->post('title', '')),
            'content'   => $this->request->post('content', ''),
            'excerpt'   => Sanitizer::trim($this->request->post('excerpt', '')),
            'author'    => $user['display_name'] ?? $user['username'] ?? 'Admin',
            'tags'      => $tags,
            'published' => $this->request->post('published') === '1',
        ];

        // Allow custom slug override
        $customSlug = Sanitizer::trim($this->request->post('slug', ''));
        if ($customSlug !== '') {
            $postData['slug'] = Sanitizer::slug($customSlug);
        }

        $repo = new BlogRepository();
        $repo->create($postData);

        $this->flash('success', 'Blog post created successfully.');
        $this->redirect('/admin/blog');
    }

    /**
     * Display the blog post editor for an existing post.
     *
     * Route param: :id
     */
    public function edit(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $id   = $this->request->param('id');
        $repo = new BlogRepository();
        $post = $repo->find($id);

        if ($post === null) {
            $this->flash('error', 'Post not found.');
            $this->redirect('/admin/blog');
            return;
        }

        $this->view('admin/blog/editor', [
            'title'  => 'Edit Post - GatewayOS2',
            'post'   => $post,
            'isEdit' => true,
            'flash'  => $this->getFlash(),
        ], 'admin');
    }

    /**
     * Process the blog post update form.
     *
     * Route param: :id
     */
    public function update(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $id = $this->request->param('id');

        if (!SessionManager::verifyCsrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/admin/blog/edit/' . $id);
            return;
        }

        $validation = Validator::validate($_POST, [
            'title'   => 'required|max:200',
            'content' => 'required',
        ]);

        if (!$validation['valid']) {
            $errors = Validator::flatten($validation['errors']);
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/admin/blog/edit/' . $id);
            return;
        }

        $tags = $this->parseTags($this->request->post('tags', ''));

        $postData = [
            'title'     => Sanitizer::trim($this->request->post('title', '')),
            'content'   => $this->request->post('content', ''),
            'excerpt'   => Sanitizer::trim($this->request->post('excerpt', '')),
            'tags'      => $tags,
            'published' => $this->request->post('published') === '1',
        ];

        // Allow custom slug override
        $customSlug = Sanitizer::trim($this->request->post('slug', ''));
        if ($customSlug !== '') {
            $postData['slug'] = Sanitizer::slug($customSlug);
        }

        $repo = new BlogRepository();
        $repo->update($id, $postData);

        $this->flash('success', 'Blog post updated successfully.');
        $this->redirect('/admin/blog');
    }

    /**
     * Delete a blog post.
     *
     * Route param: :id
     */
    public function delete(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $id = $this->request->param('id');

        if (!SessionManager::verifyCsrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/admin/blog');
            return;
        }

        $repo = new BlogRepository();
        $repo->delete($id);

        $this->flash('success', 'Blog post deleted.');
        $this->redirect('/admin/blog');
    }

    /**
     * Parse a comma-separated tag string into a clean array.
     *
     * @param string $tagString Comma-separated tags.
     * @return array Trimmed, non-empty tag strings.
     */
    private function parseTags(string $tagString): array
    {
        if (trim($tagString) === '') {
            return [];
        }

        $tags = explode(',', $tagString);
        $tags = array_map('trim', $tags);
        $tags = array_filter($tags, fn($t) => $t !== '');
        return array_values(array_unique($tags));
    }
}
