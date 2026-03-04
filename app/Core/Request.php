<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    public function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public function only(string ...$keys): array
    {
        $all = $this->all();
        return array_intersect_key($all, array_flip($keys));
    }

    public function file(string $key): ?array
    {
        $file = $_FILES[$key] ?? null;
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
        return $file;
    }

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }

    public function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    public function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        // Strip base path prefix for sub-directory installs
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        return '/' . ltrim(strtok($uri, '?'), '/');
    }

    public function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Sanitize a string input.
     */
    public function sanitize(string $key, mixed $default = null): ?string
    {
        $val = $this->input($key, $default);
        return is_string($val) ? htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8') : $val;
    }
}
