<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'candidate_id', 'job_id', 'batch_id',
        'test_city_priority_1', 'test_city_priority_2',
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
    public function job()        { return $this->belongsTo(PatsJob::class, 'job_id'); }
    public function batch()      { return $this->belongsTo(Batch::class); }
    public function payment()    { return $this->hasOne(Payment::class); }
    public function rollNumber() { return $this->hasOne(RollNumber::class); }
    public function result()     { return $this->hasOne(Result::class); }
}
