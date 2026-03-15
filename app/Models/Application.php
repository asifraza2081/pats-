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
        return $query->where('applications.status', 'fee_paid');
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

        return $query->join('candidates', 'applications.candidate_id', '=', 'candidates.id')
            ->join('pats_jobs', 'applications.job_id', '=', 'pats_jobs.id')
            ->whereRaw('(pats_jobs.min_degree_level IS NULL OR pats_jobs.min_degree_level <= (
                SELECT MAX(degree_level) FROM education_history 
                WHERE candidate_id = candidates.id 
                AND passing_year <= ?
            ))', [$closeYear])
            ->whereRaw('(pats_jobs.age_min IS NULL OR (TIMESTAMPDIFF(YEAR, candidates.dob, ?) >= pats_jobs.age_min AND (pats_jobs.age_max IS NULL OR TIMESTAMPDIFF(YEAR, candidates.dob, ?) <= pats_jobs.age_max)))', [$closeDate, $closeDate]);
    }
}
