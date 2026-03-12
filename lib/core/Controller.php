<?php
/**
 * GatewayOS2 Website - Base Controller
 *
 * All controllers extend this class to inherit the request/response
 * objects and convenient helper methods for rendering views, sending
 * JSON, and issuing redirects.
 */

class Controller
{
    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var View */
    protected $view;

    // ──────────────────────────────────────────────────────────────
    // Constructor
    // ──────────────────────────────────────────────────────────────

    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
        $this->view     = new View();

        // Share common data with all templates
        $this->view->share('currentPath', $request->path());
        $this->view->share('currentUser', $this->currentUser());
        $this->view->share('csrfToken', $_SESSION['csrf_token'] ?? '');
    }

    // ──────────────────────────────────────────────────────────────
    // Response helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Render a view template inside a layout and send it as an HTML response.
     *
     * @param string      $template  Template path, e.g. "pages/home"
     * @param array       $data      Variables for the template
     * @param string|null $layout    Layout name (default "main"), null to skip
     * @param int         $status    HTTP status code
     */
    protected function view(string $template, array $data = [], ?string $layout = 'main', int $status = 200): void
    {
        $html = $this->view->render($template, $data, $layout);
        $this->response->html($html, $status);
    }

    /**
     * Send a JSON response.
     *
     * @param mixed $data
     * @param int   $status
     */
    protected function json($data, int $status = 200): void
    {
        $this->response->json($data, $status);
    }

    /**
     * Send a redirect response.
     *
     * @param string $url
     * @param int    $status
     */
    protected function redirect(string $url, int $status = 302): void
    {
        $this->response->redirect($url, $status);
    }

    // ──────────────────────────────────────────────────────────────
    // Session / flash helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Set a flash message that persists for exactly one subsequent request.
     *
     * @param string $type    Message type: 'success', 'error', 'warning', 'info'
     * @param string $message Human-readable message
     */
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message,
        ];
    }

    /**
     * Retrieve and clear the current flash message (if any).
     *
     * @return array|null ['type' => ..., 'message' => ...]
     */
    protected function getFlash(): ?array
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    // ──────────────────────────────────────────────────────────────
    // Auth helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Return the currently authenticated user array, or null.
     *
     * @return array|null
     */
    protected function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Check if a user is logged in.
     */
    protected function isAuthenticated(): bool
    {
        return $this->currentUser() !== null;
    }

    /**
     * Check if the current user has admin privileges.
     */
    protected function isAdmin(): bool
    {
        $user = $this->currentUser();
        return $user !== null && ($user['role'] ?? '') === 'admin';
    }

    /**
     * Require authentication; redirect to login if not authenticated.
     * Returns true if authenticated, false (after redirect) otherwise.
     */
    protected function requireAuth(): bool
    {
        if (!$this->isAuthenticated()) {
            $this->flash('error', 'Please log in to continue.');
            $this->redirect('/login?redirect=' . urlencode($this->request->path()));
            return false;
        }
        return true;
    }

    /**
     * Require admin role; redirect to home if not admin.
     * Returns true if admin, false (after redirect) otherwise.
     */
    protected function requireAdmin(): bool
    {
        if (!$this->requireAuth()) {
            return false;
        }
        if (!$this->isAdmin()) {
            $this->flash('error', 'Access denied.');
            $this->redirect('/');
            return false;
        }
        return true;
    }

    // ──────────────────────────────────────────────────────────────
    // Input validation helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Validate that required POST fields are present and non-empty.
     *
     * @param  string[] $fields
     * @return array    ['valid' => bool, 'missing' => string[]]
     */
    protected function validateRequired(array $fields): array
    {
        $missing = [];
        foreach ($fields as $field) {
            $value = $this->request->post($field);
            if ($value === null || trim($value) === '') {
                $missing[] = $field;
            }
        }
        return [
            'valid'   => empty($missing),
            'missing' => $missing,
        ];
    }
}
