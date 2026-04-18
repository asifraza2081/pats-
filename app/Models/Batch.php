<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory, Auditable, SoftDeletes;

    protected $fillable = [
        'project_id', 'center_id', 'batch_number', 'test_date',
        'reporting_time', 'start_time', 'duration_minutes', 'total_seats', 
        'booked_seats', 'envelope_size', 'is_ready', 'results_published', 'created_by',
    ];

    protected $casts = [
        'test_date'          => 'date',
        'duration_minutes'   => 'integer',
        'is_ready'           => 'boolean',
        'results_published'  => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function center()
    {
        return $this->belongsTo(TestCenter::class, 'center_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'batch_id');
    }

    public function examRollnos()
    {
        return $this->hasMany(ExamRollno::class);
    }

    public function scans()
    {
        return $this->hasMany(AttendanceScan::class);
    }

    public function getAvailableSeatsAttribute()
    {
        return max(0, $this->total_seats - $this->booked_seats);
    }

    // ── Query Scopes ────────────────────────────────────
    public function scopeReady($query)    { return $query->where('is_ready', true); }
    public function scopeDraft($query)    { return $query->where('is_ready', false); }
    public function scopePublished($query){ return $query->where('results_published', true); }
}
