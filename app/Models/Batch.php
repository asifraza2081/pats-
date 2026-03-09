<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = 'batches';

    protected $fillable = [
        'project_id', 'center_id', 'batch_number',
        'test_date', 'reporting_time', 'start_time',
        'total_seats', 'booked_seats', 'envelope_size',
    ];

    protected function casts(): array
    {
        return [
            'test_date'     => 'date',
            'booked_seats'  => 'integer',
            'total_seats'   => 'integer',
            'envelope_size' => 'integer',
        ];
    }

    public function project()      { return $this->belongsTo(Project::class, 'project_id'); }
    public function center()       { return $this->belongsTo(TestCenter::class, 'center_id'); }
    public function applications() { return $this->hasMany(Application::class, 'batch_id'); }
    public function scans()        { return $this->hasMany(AttendanceScan::class, 'batch_id'); }

    public function hasCapacity(): bool   { return $this->booked_seats < $this->total_seats; }
    public function availableSeats(): int { return max(0, $this->total_seats - $this->booked_seats); }

    /** Whether the test has started (slip download disabled) */
    public function hasStarted(): bool
    {
        return now() >= \Carbon\Carbon::parse("{$this->test_date->format('Y-m-d')} {$this->start_time}");
    }

    /** Roll number TCID prefix (first 3 chars of tcid) */
    public function getRollPrefix(): string
    {
        return substr($this->center->tcid, 0, 3);
    }
}
