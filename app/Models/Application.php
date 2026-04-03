<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory, Auditable, SoftDeletes;

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
            'status'               => ApplicationStatus::class,
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

    public function scopeFeePaid($query)
    {
        return $query->where('applications.status', 'fee_paid')
            ->where(function($q) {
                // If job has fee, must have 'paid' status in payments table
                // If job has NO fee, it's implicitly cleared
                $q->whereHas('job', function($jq) {
                    $jq->where('fee', '<=', 0);
                })
                ->orWhereHas('payment', function($pq) {
                    $pq->where('status', \App\Enums\PaymentStatus::PAID);
                });
            });
    }

    public function scopeUnallocated($query)
    {
        return $query->whereDoesntHave('examRollno');
    }

    public function scopeEligible($query, Project $project)
    {
        $closeDate = $project->close_date
            ? Carbon::parse($project->close_date)->toDateString()
            : Carbon::now()->toDateString();
        
        $closeYear = $project->close_date
            ? Carbon::parse($project->close_date)->year
            : Carbon::now()->year;

        $query = $query->join('candidates', 'applications.candidate_id', '=', 'candidates.id')
            ->join('pats_jobs', 'applications.job_id', '=', 'pats_jobs.id')
            ->whereRaw('(pats_jobs.min_degree_level IS NULL OR pats_jobs.min_degree_level <= (
                SELECT MAX(degree_level) FROM education_history 
                WHERE candidate_id = candidates.id 
                AND passing_year <= ?
            ))', [$closeYear]);

        // Portable Age Calculation [FIXED]
        // Instead of MySQL TIMESTAMPDIFF, we calculate the date boundaries in PHP
        $carbonClose = Carbon::parse($closeDate);
        
        return $query->where(function($q) use ($carbonClose) {
            $q->where(function($sq) use ($carbonClose) {
                // age_min check: dob must be before or on (closeDate - age_min years)
                $sq->whereNull('pats_jobs.age_min')
                   ->orWhere('candidates.dob', '<=', function($sub) use ($carbonClose) {
                       $sub->selectRaw('DATE_SUB(?, INTERVAL pats_jobs.age_min YEAR)', [$carbonClose]);
                   });
            })->where(function($sq) use ($carbonClose) {
                // age_max check: dob must be after or on (closeDate - (age_max + 1) years + 1 day)
                // Roughly: dob >= (closeDate - age_max years)
                $sq->whereNull('pats_jobs.age_max')
                   ->orWhere('candidates.dob', '>=', function($sub) use ($carbonClose) {
                       $sub->selectRaw('DATE_SUB(?, INTERVAL (pats_jobs.age_max + 1) YEAR)', [$carbonClose]);
                   });
            });
        });
    }
}
