<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationHistory extends Model
{
    protected $table = 'education_history';

    protected $fillable = [
        'candidate_id', 'degree_level', 'degree_name', 'subject_major',
        'institution', 'passing_year', 'marks_type',
        'obtained_marks', 'total_marks', 'certificate_path',
    ];

    protected function casts(): array
    {
        return [
            'degree_level'   => 'integer',
            'obtained_marks' => 'float',
            'total_marks'    => 'float',
        ];
    }

    public static array $levelLabels = [
        1 => 'SSC / Matric',
        2 => 'HSSC / Inter',
        3 => 'Bachelor (14 Years)',
        4 => 'Bachelor (16 Years)',
        5 => 'Master (18 Years)',
        6 => 'PHD (21 Years)',
    ];

    public function candidate() { return $this->belongsTo(Candidate::class); }

    public function getLevelLabelAttribute(): string
    {
        return self::$levelLabels[$this->degree_level] ?? 'Unknown';
    }

    public function getPercentageAttribute(): ?float
    {
        if ($this->marks_type === 'Marks' && $this->total_marks > 0) {
            return round(($this->obtained_marks / $this->total_marks) * 100, 2);
        }
        return null;
    }

    public function getPercentageDisplayAttribute(): string
    {
        if ($this->marks_type === 'CGPA') {
            return $this->obtained_marks ? number_format($this->obtained_marks, 2) . ' CGPA' : '—';
        }
        if ($this->obtained_marks && $this->total_marks) {
            return $this->obtained_marks . '/' . $this->total_marks . ' (' . $this->percentage . '%)';
        }
        return '—';
    }
}
