<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class User extends Model
{
    protected static string $table = 'users';

    /**
     * Find user by CNIC.
     */
    public static function findByCnic(string $cnic): ?array
    {
        return static::findBy('cnic', $cnic);
    }

    /**
     * Find user by email.
     */
    public static function findByEmail(string $email): ?array
    {
        return static::findBy('email', $email);
    }

    /**
     * Find user by phone.
     */
    public static function findByPhone(string $phone): ?array
    {
        return static::findBy('phone', $phone);
    }

    /**
     * Register a new candidate user.
     */
    public static function register(array $data): int
    {
        return static::create([
            'cnic'     => preg_replace('/[-\s]/', '', $data['cnic']),
            'name'     => $data['name'],
            'email'    => strtolower($data['email']),
            'phone'    => $data['phone'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'     => 'candidate',
        ]);
    }

    /**
     * Verify password and return user or null.
     */
    public static function attempt(string $cnic, string $password): ?array
    {
        $cnic = preg_replace('/[-\s]/', '', $cnic);
        $user = static::findByCnic($cnic);
        if (!$user) return null;
        if (!password_verify($password, $user['password'])) return null;
        return $user;
    }

    /**
     * Set OTP for phone/password reset.
     */
    public static function setOtp(int $userId, string $otp): void
    {
        static::updateWhere(
            [
                'otp'            => $otp,
                'otp_expires_at' => date('Y-m-d H:i:s', strtotime('+10 minutes')),
            ],
            ['id' => $userId]
        );
    }

    /**
     * Verify OTP and return true if valid and not expired.
     */
    public static function verifyOtp(int $userId, string $otp): bool
    {
        $user = static::find($userId);
        if (!$user) return false;
        if ($user['otp'] !== $otp) return false;
        if (strtotime($user['otp_expires_at']) < time()) return false;
        // Clear OTP
        static::updateWhere(['otp' => null, 'otp_expires_at' => null], ['id' => $userId]);
        return true;
    }

    /**
     * Mark user as verified.
     */
    public static function markVerified(int $userId): void
    {
        static::updateWhere(['is_verified' => 1], ['id' => $userId]);
    }

    /**
     * Update password.
     */
    public static function updatePassword(int $userId, string $newPassword): void
    {
        static::updateWhere(
            ['password' => password_hash($newPassword, PASSWORD_BCRYPT)],
            ['id' => $userId]
        );
    }
}
