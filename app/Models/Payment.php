<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'application_id', 'challan_ref', 'amount', 'status',
        'bank_name', 'branch_code', 'transaction_id', 'deposit_date',
        'verified_by', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'deposit_date' => 'date',
            'verified_at'  => 'datetime',
            'amount'       => 'decimal:2',
            'status'       => PaymentStatus::class,
        ];
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Generate a unique challan reference number.
     */
    public static function generateRef(): string
    {
        $attempts = 0;
        $maxAttempts = 10;

        do {
            if ($attempts >= $maxAttempts) {
                throw new \RuntimeException("Failed to generate a unique challan reference after {$maxAttempts} attempts.");
            }
            $ref = 'PATS-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            $attempts++;
        } while (static::where('challan_ref', $ref)->exists());

        return $ref;
    }
}
