<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Candidate extends Model
{
    protected static string $table = 'candidates';

    /**
     * Find candidate record by user_id.
     */
    public static function findByUserId(int $userId): ?array
    {
        return static::findBy('user_id', $userId);
    }

    /**
     * Create an empty candidate profile for a newly registered user.
     */
    public static function createForUser(int $userId): int
    {
        return static::create(['user_id' => $userId]);
    }

    /**
     * Get full candidate profile joined with user info.
     */
    public static function getFullProfile(int $userId): ?array
    {
        $db = \App\Core\Database::getInstance();
        return $db->fetchOne(
            'SELECT u.id AS user_id, u.cnic, u.name, u.email, u.phone, u.is_verified,
                    c.*
             FROM users u
             LEFT JOIN candidates c ON c.user_id = u.id
             WHERE u.id = ?',
            [$userId]
        );
    }

    /**
     * Calculate profile completion percentage.
     */
    public static function completionPercent(array $candidate): int
    {
        $fields = ['dob', 'gender', 'domicile', 'province', 'address', 'photo_path', 'cnic_copy_path'];
        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($candidate[$field])) $filled++;
        }
        return (int) round(($filled / count($fields)) * 100);
    }
}
