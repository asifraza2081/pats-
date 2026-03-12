<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamRollno extends Model
{
    protected $fillable = [
        'application_id',
        'project_id',
        'job_id',
        'city_id',
        'center_id',
        'roll_no',
        'barcode',
        'batch_no',
        'test_date',
        'reporting_time',
        'start_time',
        'slip_ready',
    ];

    protected $casts = [
        'test_date' => 'date',
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

    public function testCenter()
    {
        return $this->belongsTo(TestCenter::class, 'center_id');
    }

    public function center()
    {
        return $this->belongsTo(TestCenter::class, 'center_id');
    }
}
