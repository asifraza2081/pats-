<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamRollno extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'application_id',
        'project_id',
        'job_id',
        'city_id',
        'center_id',
        'batch_id',
        'roll_no',
        'barcode',
        'batch_no',
        'roll_sequence',
        'verify_token',
        'slip_ready',
    ];

    protected $casts = [
        'slip_ready' => 'boolean',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function job()
    {
        return $this->belongsTo(PatsJob::class, 'job_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function center()
    {
        return $this->belongsTo(TestCenter::class, 'center_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    // ── Accessors ─────────────────────────────────────────

    /**
     * Human-readable roll number: "2605-13-001" from raw "260513001"
     * Format: YYMM-PJ-SSS
     */
    public function getFormattedRollNoAttribute(): string
    {
        $r = $this->roll_no;
        if (strlen($r) === 9) {
            return substr($r, 0, 4) . '-' . substr($r, 4, 2) . '-' . substr($r, 6);
        }
        return $r; // Fallback for legacy roll numbers
    }

    // ── Query Scopes ────────────────────────────────────
    public function scopeReady($query) { return $query->where('slip_ready', true); }
}
