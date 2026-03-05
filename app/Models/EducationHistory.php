<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class EducationHistory extends Model
{
    protected static string $table = 'education_history';

    /**
     * Get all education records for a candidate.
     */
    public static function getForCandidate(int $candidateId): array
    {
        return static::where(['candidate_id' => $candidateId], 'passing_year DESC');
    }
}
