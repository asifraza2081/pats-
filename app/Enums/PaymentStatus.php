<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::UNPAID => 'Unpaid',
            self::PAID => 'Paid',
            self::REJECTED => 'Rejected',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::UNPAID => 'warning',
            self::PAID => 'success',
            self::REJECTED => 'danger',
        };
    }
}
