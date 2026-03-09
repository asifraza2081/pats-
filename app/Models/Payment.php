<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
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
        do {
            $ref = 'PATS-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (static::where('challan_ref', $ref)->exists());

        return $ref;
    }
}
