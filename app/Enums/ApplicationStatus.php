<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case SUBMITTED = 'submitted';
    case FEE_PAID = 'fee_paid';
    case SCHEDULED = 'scheduled';
    case APPEARED = 'appeared';
    case ABSENT = 'absent';
    case REJECTED = 'rejected';
    case SHORTLISTED = 'shortlisted';
    case NOT_SHORTLISTED = 'not_shortlisted';
    case RESULT_DECLARED = 'result_declared';

    public function label(): string
    {
        return match($this) {
            self::SUBMITTED => 'Submitted',
            self::FEE_PAID => 'Fee Paid',
            self::SCHEDULED => 'Scheduled',
            self::APPEARED => 'Appeared',
            self::ABSENT => 'Absent',
            self::REJECTED => 'Rejected',
            self::SHORTLISTED => 'Shortlisted',
            self::NOT_SHORTLISTED => 'Not Shortlisted',
            self::RESULT_DECLARED => 'Result Declared',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::SUBMITTED => 'secondary',
            self::FEE_PAID => 'primary',
            self::SCHEDULED => 'info',
            self::APPEARED => 'success',
            self::ABSENT => 'danger',
            self::REJECTED => 'danger',
            self::SHORTLISTED => 'success',
            self::NOT_SHORTLISTED => 'warning',
            self::RESULT_DECLARED => 'dark',
        };
    }
}
