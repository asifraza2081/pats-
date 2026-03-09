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
        1 => 'Matric (10 Years)',
        2 => 'Intermediate (12 Years)',
        3 => 'Bachelor (14 Years)',
        4 => 'Master (16 Years)',
        5 => 'M.Phil',
        6 => 'PhD',
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
}
