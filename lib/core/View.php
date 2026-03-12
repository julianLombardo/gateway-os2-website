<?php
/**
 * GatewayOS2 Website - Template Renderer
 *
 * Renders PHP template files with variable extraction and layout wrapping.
 *
 * Directory conventions:
 *   templates/layouts/   - Layout files (e.g. main.php)
 *   templates/pages/     - Full page templates
 *   templates/partials/  - Reusable partial snippets
 *   templates/components/- UI components
 */

class View
{
    /** @var string Base path for all template files */
    private $templateDir;

    /** @var array Global data available to every template */
    private $globals = [];

    // ──────────────────────────────────────────────────────────────
    // Constructor
    // ──────────────────────────────────────────────────────────────

    public function __construct()
    {
        $this->templateDir = BASE_DIR . '/templates';
    }

    // ──────────────────────────────────────────────────────────────
    // Global data
    // ──────────────────────────────────────────────────────────────

    /**
     * Share a variable with all templates rendered by this View instance.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return $this
     */
    public function share(string $key, $value): self
    {
        $this->globals[$key] = $value;
        return $this;
    }

    // ──────────────────────────────────────────────────────────────
    // Rendering
    // ──────────────────────────────────────────────────────────────

    /**
     * Render a template file wrapped in a layout.
     *
     * @param string      $template  Dot-or-slash path relative to templates/, e.g. "pages/home"
     * @param array       $data      Variables to extract into the template scope
     * @param string|null $layout    Layout name (null to skip layout wrapping)
     * @return string     Fully rendered HTML
     */
    public function render(string $template, array $data = [], ?string $layout = 'main'): string
    {
        // Render the page content
        $content = $this->renderFile($template, $data);

        // Wrap in layout if specified
        if ($layout !== null) {
            $layoutData = array_merge($this->globals, $data, ['content' => $content]);
            $content = $this->renderFile('layouts/' . $layout, $layoutData);
        }

        return $content;
    }

    /**
     * Render a partial template (no layout wrapping).
     *
     * @param string $name  Partial name, e.g. "navbar" resolves to templates/partials/navbar.php
     * @param array  $data
     * @return string
     */
    public function partial(string $name, array $data = []): string
    {
        return $this->renderFile('partials/' . $name, $data);
    }

    /**
     * Render a component template (no layout wrapping).
     *
     * @param string $name  Component name, e.g. "card" resolves to templates/components/card.php
     * @param array  $data
     * @return string
     */
    public function component(string $name, array $data = []): string
    {
        return $this->renderFile('components/' . $name, $data);
    }

    // ──────────────────────────────────────────────────────────────
    // Static helpers (usable inside templates)
    // ──────────────────────────────────────────────────────────────

    /**
     * Escape a string for safe HTML output.
     *
     * @param  string|null $value
     * @return string
     */
    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Generate a full asset URL with cache-busting query string.
     *
     * @param  string $path  Path relative to public/, e.g. "css/style.css"
     * @return string
     */
    public static function asset(string $path): string
    {
        $filePath = BASE_DIR . '/public/' . ltrim($path, '/');
        $version  = file_exists($filePath) ? filemtime($filePath) : '1';
        return '/' . ltrim($path, '/') . '?v=' . $version;
    }

    // ──────────────────────────────────────────────────────────────
    // Internal
    // ──────────────────────────────────────────────────────────────

    /**
     * Render a single template file using output buffering.
     *
     * @param string $relativePath  Path relative to templates/ (no .php extension)
     * @param array  $data          Variables to extract
     * @return string
     */
    private function renderFile(string $relativePath, array $data = []): string
    {
        $filePath = $this->templateDir . '/' . $relativePath . '.php';

        if (!file_exists($filePath)) {
            error_log("View: template not found: {$filePath}");
            return '<!-- template not found: ' . self::e($relativePath) . ' -->';
        }

        // Merge globals with local data (local wins)
        $allData = array_merge($this->globals, $data);

        // Make the View instance available inside templates as $view
        $allData['view'] = $this;

        // Extract variables into the template scope
        extract($allData, EXTR_SKIP);

        ob_start();
        include $filePath;
        return ob_get_clean();
    }
}
