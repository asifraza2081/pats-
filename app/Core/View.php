<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    /**
     * Render a view file inside a layout.
     *
     * @param string $view   e.g. 'candidate/dashboard'
     * @param array  $data   Variables to extract into view scope
     * @param string $layout e.g. 'app' or 'admin' (maps to views/layouts/{layout}.php)
     */
    public static function render(string $view, array $data = [], string $layout = 'app'): void
    {
        extract($data, EXTR_SKIP);

        // Capture the view content
        ob_start();
        $viewFile = BASE_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }
        include $viewFile;
        $content = ob_get_clean();

        // Render inside layout
        $layoutFile = BASE_PATH . '/views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            throw new \RuntimeException("Layout not found: {$layoutFile}");
        }
        include $layoutFile;
    }

    /**
     * Render a view without any layout (for partials, AJAX responses, PDFs).
     */
    public static function partial(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = BASE_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }
        include $viewFile;
    }

    /**
     * Escape a value for safe HTML output.
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
