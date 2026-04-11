<?php

namespace App\Enums;

enum ResultStatus: string
{
    case PASS = 'pass';
    case FAIL = 'fail';
    case ABSENT = 'absent';
    case WITHHELD = 'withheld';

    public function label(): string
    {
        return match($this) {
            self::PASS => 'Pass',
            self::FAIL => 'Fail',
            self::ABSENT => 'Absent',
            self::WITHHELD => 'Withheld',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PASS => 'success',
            self::FAIL => 'danger',
            self::ABSENT => 'warning',
            self::WITHHELD => 'info',
        };
    }
}
