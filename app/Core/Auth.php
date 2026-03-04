<?php

declare(strict_types=1);

namespace App\Core;

class Auth
{
    public static function login(array $user): void
    {
        Session::regenerate();
        Session::set('user', [
            'id'          => $user['id'],
            'name'        => $user['name'],
            'email'       => $user['email'],
            'role'        => $user['role'],
            'is_verified' => $user['is_verified'],
        ]);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::has('user');
    }

    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function id(): ?int
    {
        $user = static::user();
        return $user ? (int) $user['id'] : null;
    }

    public static function role(): ?string
    {
        $user = static::user();
        return $user['role'] ?? null;
    }

    public static function hasRole(string ...$roles): bool
    {
        return in_array(static::role(), $roles, true);
    }

    /**
     * Require a specific role or redirect.
     */
    public static function requireRole(string ...$roles): void
    {
        if (!static::check()) {
            Response::redirect('/login');
        }
        if (!static::hasRole(...$roles)) {
            Response::redirect('/unauthorized');
        }
    }

    /**
     * Require candidate to be logged in.
     */
    public static function requireAuth(): void
    {
        if (!static::check()) {
            Session::flash('error', 'Please log in to continue.');
            Response::redirect('/login');
        }
    }

    /**
     * Redirect logged-in users away from guest-only pages.
     */
    public static function requireGuest(): void
    {
        if (static::check()) {
            $role = static::role();
            Response::redirect(match($role) {
                'candidate' => '/dashboard',
                default     => '/admin/dashboard',
            });
        }
    }

    public static function isAdmin(): bool
    {
        return static::hasRole('super_admin', 'admin', 'data_entry');
    }

    public static function isCandidate(): bool
    {
        return static::hasRole('candidate');
    }
}
