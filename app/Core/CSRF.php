<?php

declare(strict_types=1);

namespace App\Core;

class CSRF
{
    private const TOKEN_KEY = '_csrf_token';

    public static function generate(): string
    {
        Session::start();
        if (!Session::has(self::TOKEN_KEY)) {
            Session::set(self::TOKEN_KEY, bin2hex(random_bytes(32)));
        }
        return Session::get(self::TOKEN_KEY);
    }

    public static function token(): string
    {
        return static::generate();
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(static::token()) . '">';
    }

    public static function verify(string $token): bool
    {
        $stored = Session::get(self::TOKEN_KEY);
        if (!$stored) return false;
        // Rotate token after verification
        Session::remove(self::TOKEN_KEY);
        return hash_equals($stored, $token);
    }

    public static function check(): void
    {
        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!static::verify($token)) {
            http_response_code(403);
            die('Invalid or expired CSRF token. Please go back and try again.');
        }
    }
}
