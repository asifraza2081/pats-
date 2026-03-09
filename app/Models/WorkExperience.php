<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    protected $table = 'work_experience';

    protected $fillable = [
        'candidate_id', 'job_type', 'organization_name',
        'designation', 'from_date', 'to_date', 'is_current',
    ];

    protected function casts(): array
    {
        return [
            'from_date'  => 'date',
            'to_date'    => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function candidate() { return $this->belongsTo(Candidate::class); }

    public function getDurationYearsAttribute(): float
    {
        $end = $this->is_current ? now() : $this->to_date;
        return round($this->from_date->floatDiffInYears($end), 2);
    }

    public function getDurationLabelAttribute(): string
    {
        $years = $this->duration_years;
        $y = (int) $years;
        $m = (int) round(($years - $y) * 12);
        $parts = [];
        if ($y) $parts[] = "{$y} yr" . ($y !== 1 ? 's' : '');
        if ($m) $parts[] = "{$m} mo" . ($m !== 1 ? 's' : '');
        return $parts ? implode(' ', $parts) : 'Less than a month';
    }
}
