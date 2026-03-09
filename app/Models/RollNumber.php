<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class RollNumber extends Model
{
    protected static string $table = 'roll_numbers';

    /**
     * Get roll number by application id
     */
    public static function getForApplication(int $appId): ?array
    {
        return static::findBy('application_id', $appId);
    }

    /**
     * Check if roll number already exists.
     */
    public static function existsForRollNo(string $rollNum): bool
    {
        return static::exists(['roll_number' => $rollNum]);
    }

    /**
     * Generate a roll number in the format:
     * [CenterID 3-digits][ProjID 3-digits][JobID 3-digits][Serial 3-digits]
     *
     * e.g., for Center #2, Project #1, Job #3, candidate #7: 002001003007
     *
     * Serial is per-job, incrementing across all centers.
     */
    public static function generate(int $applicationId): string
    {
        $db = Database::getInstance();

        // Get application details (slot => center, job, project)
        $app = $db->fetchOne(
            'SELECT a.job_id, j.project_id, cs.center_id
             FROM applications a
             JOIN jobs j ON j.id = a.job_id
             JOIN center_slots cs ON cs.id = a.slot_id
             WHERE a.id = ?',
            [$applicationId]
        );

        if (!$app) {
            throw new \RuntimeException("Application {$applicationId} not found.");
        }

        $centerId  = (int)$app['center_id'];
        $projectId = (int)$app['project_id'];
        $jobId     = (int)$app['job_id'];

        // Count existing roll numbers for this job to derive serial
        $count = (int)($db->fetchOne(
            'SELECT COUNT(*) as cnt FROM roll_numbers rn
             JOIN applications a ON a.id = rn.application_id
             WHERE a.job_id = ?',
            [$jobId]
        )['cnt'] ?? 0);

        $serial = $count + 1;

        // Format: CCC PPP JJJ SSS  (3-digit each, zero-padded)
        $rollNumber = sprintf('%03d%03d%03d%03d', $centerId, $projectId, $jobId, $serial);

        // Collision guard (should be rare but protected by transaction)
        while (static::existsForRollNo($rollNumber)) {
            $serial++;
            $rollNumber = sprintf('%03d%03d%03d%03d', $centerId, $projectId, $jobId, $serial);
        }

        static::create([
            'application_id' => $applicationId,
            'roll_number'    => $rollNumber,
        ]);

        return $rollNumber;
    }
}
