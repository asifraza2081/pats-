<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class SmsLog extends Model
{
    protected static string $table = 'sms_log';

    public static function log(array $data): int
    {
        return static::create($data);
    }
}
