<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case RESULT_DECLARED = 'result_declared';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
            self::RESULT_DECLARED => 'Result Declared',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'secondary',
            self::OPEN => 'success',
            self::CLOSED => 'danger',
            self::RESULT_DECLARED => 'info',
        };
    }
}
