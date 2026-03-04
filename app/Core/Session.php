<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (!static::$started && session_status() === PHP_SESSION_NONE) {
            session_name('PATS_SESSION');
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
            ]);
            static::$started = true;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        static::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        static::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        static::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        static::start();
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        static::start();
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        static::start();
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function destroy(): void
    {
        static::start();
        $_SESSION = [];
        session_destroy();
        static::$started = false;
    }

    public static function regenerate(): void
    {
        static::start();
        session_regenerate_id(true);
    }
}
