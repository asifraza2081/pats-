<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class RollNumber extends Model
{
    protected static string $table = 'roll_numbers';

    /**
     * Get roll number by application id
     */
    public static function getForApplication(int $appId): ?array
    {
        return static::findBy('application_id', $appId);
    }

    /**
     * Check if roll number already exists string
     */
    public static function existsForRollNo(string $rollNum): bool
    {
        return static::exists(['roll_number' => $rollNum]);
    }
}
