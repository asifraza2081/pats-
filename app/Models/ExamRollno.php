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
        // Format for new secure alphanumeric roll numbers (e.g., LHR0010050001 -> LHR-001-005-0001)
        // We assume 3-4 chars for TCID, 3 for Proj, 3 for Job, 4 for Serial
        if (preg_match('/^([A-Z0-9]{3,4})(\d{3})(\d{3})(\d{4})$/', $r, $m)) {
            return "{$m[1]}-{$m[2]}-{$m[3]}-{$m[4]}";
        }
        
        // Legacy 9-digit numeric format: YYMM-PJ-SSS
        if (strlen($r) === 9 && is_numeric($r)) {
            return substr($r, 0, 4) . '-' . substr($r, 4, 2) . '-' . substr($r, 6);
        }
        
        return $r; 
    }

    // ── Query Scopes ────────────────────────────────────
    public function scopeReady($query) { return $query->where('slip_ready', true); }
}
