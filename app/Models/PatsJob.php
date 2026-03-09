<?php

namespace App\Models;

use App\Services\EligibilityService;
use Illuminate\Database\Eloquent\Model;

class PatsJob extends Model
{
    protected $table = 'pats_jobs';

    protected $fillable = [
        'project_id', 'job_code', 'title', 'department', 'bps_grade',
        'total_seats', 'quota_notes', 'fee',
        'min_degree_level', 'min_qualification_name', 'required_subject',
        'min_experience_years', 'experience_sector',
        'age_min', 'age_max', 'domicile_required',
    ];

    protected function casts(): array
    {
        return [
            'min_experience_years' => 'float',
            'fee'                  => 'float',
            'job_code'             => 'integer',
        ];
    }

    public function project()      { return $this->belongsTo(Project::class, 'project_id'); }
    public function applications() { return $this->hasMany(Application::class, 'job_id'); }

    /**
     * Check a candidate's eligibility for this job.
     * Returns ['passed' => bool, 'warnings' => [...]]
     */
    public function checkEligibility(Candidate $candidate): array
    {
        return app(EligibilityService::class)->check($candidate, $this);
    }

    /**
     * Next sequential job_code for a project.
     */
    public static function nextJobCode(int $projectId): int
    {
        return (int) (static::where('project_id', $projectId)->max('job_code') ?? 0) + 1;
    }
}
