<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'project_id', 'category_id', 'created_by',
        'expense_date', 'voucher_no', 'description',
        'recipient_name', 'recipient_ntn', 'recipient_cnic',
        'gross_amount', 'tax_rate', 'tax_amount', 'net_amount',
        'attachment_path', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'gross_amount' => 'decimal:2',
            'tax_rate'     => 'decimal:2',
            'tax_amount'   => 'decimal:2',
            'net_amount'   => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function project()  { return $this->belongsTo(Project::class); }
    public function category() { return $this->belongsTo(FinancialCategory::class, 'category_id'); }
    public function creator()  { return $this->belongsTo(User::class, 'created_by'); }

    public function ledgerEntry()
    {
        return $this->morphOne(FinancialLedger::class, 'source');
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getGrossFormattedAttribute(): string
    {
        return 'PKR ' . number_format($this->gross_amount, 2);
    }

    public function getTaxFormattedAttribute(): string
    {
        return 'PKR ' . number_format($this->tax_amount, 2);
    }

    public function getNetFormattedAttribute(): string
    {
        return 'PKR ' . number_format($this->net_amount, 2);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Compute and fill tax_amount and net_amount from gross_amount and tax_rate.
     */
    public static function computeTaxFields(float $gross, float $rate): array
    {
        $tax = round($gross * $rate / 100, 2);
        return [
            'tax_amount' => $tax,
            'net_amount' => round($gross - $tax, 2),
        ];
    }
}
