<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    public static function redirect(string $path, int $code = 302): never
    {
        $config  = require BASE_PATH . '/config/app.php';
        $baseUrl = rtrim($config['url'], '/');
        $path    = '/' . ltrim($path, '/');
        header("Location: {$baseUrl}{$path}", true, $code);
        exit;
    }

    public static function json(mixed $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function abort(int $code = 404, string $message = 'Not Found'): never
    {
        http_response_code($code);
        // Show a simple error page if view exists
        $viewFile = BASE_PATH . "/views/errors/{$code}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<h1>{$code} — {$message}</h1>";
        }
        exit;
    }

    public static function download(string $filepath, string $filename): never
    {
        if (!file_exists($filepath)) {
            static::abort(404, 'File not found');
        }
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
}
