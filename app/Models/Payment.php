<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected static string $table = 'payments';

    /**
     * Get payment info by application.
     */
    public static function getForApplication(int $appId): ?array
    {
        return static::findBy('application_id', $appId);
    }
}
