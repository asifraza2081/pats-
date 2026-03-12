<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'project_id',
        'center_id',
        'batch_number',
        'test_date',
        'reporting_time',
        'start_time',
        'total_seats',
        'booked_seats',
        'envelope_size',
        'is_ready',
    ];

    protected $casts = [
        'test_date' => 'date',
        'is_ready'  => 'boolean',
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
}
