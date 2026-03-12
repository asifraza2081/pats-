<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'candidate_id', 'job_id', 'project_id', 'batch_id', 'desired_test_city_id',
        'age_relaxation_type', 'age_relaxation_years',
        'status', 'eligibility_warnings', 'applied_at',
    ];

    protected function casts(): array
    {
        return [
            'eligibility_warnings' => 'array',
            'applied_at'           => 'datetime',
        ];
    }

    public function candidate()  { return $this->belongsTo(Candidate::class); }
    public function project()    { return $this->belongsTo(Project::class); }
    public function job()        { return $this->belongsTo(PatsJob::class, 'job_id'); }
    public function desiredTestCity() { return $this->belongsTo(City::class, 'desired_test_city_id'); }
    public function examRollno() { return $this->hasOne(ExamRollno::class); }
    public function payment()    { return $this->hasOne(Payment::class); }
    public function batch()      { return $this->belongsTo(Batch::class); }
    public function result()     { return $this->hasOne(Result::class); }
}
