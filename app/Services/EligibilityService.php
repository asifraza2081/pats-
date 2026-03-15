<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\PatsJob;

class EligibilityService
{
    /**
     * Check a candidate's eligibility against a job.
     *
     * @return array{passed: bool, warnings: string[]}
     */
    public function check(Candidate $candidate, PatsJob $job): array
    {
        $warnings = [];
        $project = $job->project;
        $asOfDate = $project->close_date ?? now();
        $asOfYear = $asOfDate->year;

        // 1. Age check
        if ($job->age_min || $job->age_max) {
            $age = $candidate->age($asOfDate);
            if ($job->age_min && $age < $job->age_min) {
                $warnings[] = "Age {$age} is below the minimum required age of {$job->age_min}.";
            }
            if ($job->age_max && $age > $job->age_max) {
                $warnings[] = "Age {$age} exceeds the maximum allowed age of {$job->age_max}.";
            }
        }

        // 2. Qualification / degree level check
        if ($job->min_degree_level) {
            $maxLevel = $candidate->maxDegreeLevel($asOfYear);
            if ($maxLevel < $job->min_degree_level) {
                $label = \App\Models\EducationHistory::$levelLabels[$job->min_degree_level] ?? "Level {$job->min_degree_level}";
                $warnings[] = "Minimum qualification required: {$label}. Your highest qualification does not meet this.";
            }
        }

        // 3. Required subject check
        if ($job->required_subject) {
            $hasSubject = $candidate->education()
                ->where('passing_year', '<=', $asOfYear)
                ->where(function($q) use ($job) {
                    $q->whereRaw('LOWER(subject_major) LIKE ?', ['%' . strtolower($job->required_subject) . '%'])
                      ->orWhereRaw('LOWER(degree_name) LIKE ?', ['%' . strtolower($job->required_subject) . '%']);
                })
                ->exists();
            if (!$hasSubject) {
                $warnings[] = "Required subject/specialization: \"{$job->required_subject}\" not found in your education history.";
            }
        }

        // 4. Experience check
        if ($job->min_experience_years > 0) {
            $sector = $job->experience_sector !== 'Any' ? $job->experience_sector : null;
            $yearsHeld = $candidate->totalExperienceYears($sector, $asOfDate);
            if ($yearsHeld < $job->min_experience_years) {
                $sectorLabel = $sector ? " ({$sector} sector)" : '';
                $warnings[] = sprintf(
                    'Minimum experience required: %.1f year(s)%s. Your total: %.1f year(s).',
                    $job->min_experience_years, $sectorLabel, $yearsHeld
                );
            }
        }

        // 5. Domicile check
        if ($job->domicile_required) {
            $candidateDomicile = strtolower($candidate->province_of_domicile . ' ' . $candidate->district_of_domicile);
            if (!str_contains($candidateDomicile, strtolower($job->domicile_required))) {
                $warnings[] = "Domicile requirement: \"{$job->domicile_required}\". Your domicile: {$candidate->province_of_domicile}, {$candidate->district_of_domicile}.";
            }
        }

        return [
            'passed'   => empty($warnings),
            'warnings' => $warnings,
        ];
    }
}
