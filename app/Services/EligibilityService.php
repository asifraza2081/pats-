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

        // 5. Domicile check (Hardened)
        if ($job->domicile_required) {
            $required = strtolower($job->domicile_required);
            $candidateProv = strtolower($candidate->province_of_domicile);
            $candidateDist = strtolower($candidate->district_of_domicile);
            
            // Check if required matches province OR district exactly
            if ($candidateProv !== $required && $candidateDist !== $required) {
                $warnings[] = "Domicile requirement: \"{$job->domicile_required}\". Your domicile: {$candidate->province_of_domicile}, {$candidate->district_of_domicile}.";
            }
        }

        return [
            'passed'   => empty($warnings),
            'warnings' => $warnings,
        ];
    }

    /**
     * Get the completion status for each step of the candidate profile.
     *
     * @return array{
     *   step1: array{success: bool, label: string, errors: string[]},
     *   step2: array{success: bool, label: string, errors: string[]},
     *   step3: array{success: bool, label: string, errors: string[]},
     *   step4: array{success: bool, label: string, errors: string[]},
     *   total_percent: int,
     *   next_step: int
     * }
     */
    public function getProfileStatus(Candidate $candidate): array
    {
        $status = [
            'step1' => ['success' => true, 'label' => 'Profile Picture', 'errors' => []],
            'step2' => ['success' => true, 'label' => 'Personal Bio', 'errors' => []],
            'step3' => ['success' => true, 'label' => 'Academic History', 'errors' => []],
            'step4' => ['success' => true, 'label' => 'Experience (Optional)', 'errors' => []],
        ];

        // 1. Documents Step
        if (empty($candidate->photo_path)) {
            $status['step1']['success'] = false;
            $status['step1']['errors'][] = "Profile photo required.";
        }
        // CNIC Front is hidden/optional now as per request
        /*
        if (empty($candidate->cnic_front_path)) {
            $status['step1']['success'] = false;
            $status['step1']['errors'][] = "CNIC Front copy required.";
        }
        */

        // 2. Bio Step
        $requiredBio = ['father_name', 'dob', 'gender', 'marital_status', 'religion', 'domicile_city_id', 'permanent_address'];
        foreach ($requiredBio as $field) {
            if (empty($candidate->$field)) {
                $status['step2']['success'] = false;
                $status['step2']['errors'][] = "Missing " . ucwords(str_replace('_', ' ', $field));
            }
        }

        // 3. Academic Step
        if (!$candidate->education()->exists()) {
            $status['step3']['success'] = false;
            $status['step3']['errors'][] = "Add at least one degree/certificate.";
        }

        // 4. Experience Step (Optional)
        // success is always true unless specific rules added

        // Calculate Percent & Next Step
        $completedSteps = 0;
        $nextStep = 1;
        foreach (['step1', 'step2', 'step3', 'step4'] as $idx => $key) {
            if ($status[$key]['success']) {
                $completedSteps++;
            } else {
                $nextStep = $idx + 1;
                break;
            }
        }

        // Handle case where all are success
        if ($completedSteps === 4) $nextStep = 4;

        return array_merge($status, [
            'total_percent' => (int) round(($completedSteps / 4) * 100),
            'next_step'     => $nextStep
        ]);
    }
}
