<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialLedger extends Model
{
    // NO SoftDeletes — immutable for audit integrity
    protected $table = 'financial_ledger';
    
    protected static function booted()
    {
        static::updating(function ($ledger) {
            throw new \LogicException('Financial ledger entries are immutable and cannot be updated.');
        });

        static::deleting(function ($ledger) {
            throw new \LogicException('Financial ledger entries are immutable and cannot be deleted.');
        });
    }

    protected $fillable = [
        'type', 'source_type', 'source_id', 'project_id',
        'category', 'description', 'amount', 'tax_amount', 'net_amount',
        'ledger_date', 'fiscal_year', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'ledger_date' => 'date',
            'amount'      => 'decimal:2',
            'tax_amount'  => 'decimal:2',
            'net_amount'  => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function project() { return $this->belongsTo(Project::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function source()
    {
        return $this->morphTo('source');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeRevenue($query)  { return $query->where('type', 'revenue'); }
    public function scopeExpense($query)  { return $query->where('type', 'expense'); }

    public function scopeForFiscalYear($query, string $fy)
    {
        return $query->where('fiscal_year', $fy);
    }

    public function scopeForProject($query, int $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeDateRange($query, ?string $from, ?string $to)
    {
        if ($from) $query->where('ledger_date', '>=', $from);
        if ($to)   $query->where('ledger_date', '<=', $to);
        return $query;
    }

    // ── Formatted Accessors ──────────────────────────────────────────────────

    public function getAmountFormattedAttribute(): string
    {
        return 'PKR ' . number_format($this->amount, 2);
    }

    // ── Static Helpers ───────────────────────────────────────────────────────

    /**
     * Compute fiscal year label from a date.
     * Pakistan FY: July 1 – June 30.
     */
    public static function fiscalYearFor(\Carbon\Carbon $date, int $startMonth = 7): string
    {
        if ($date->month >= $startMonth) {
            return $date->year . '-' . substr($date->year + 1, -2);
        }
        return ($date->year - 1) . '-' . substr($date->year, -2);
    }
}
