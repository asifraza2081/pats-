<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Application extends Model
{
    protected static string $table = 'applications';

    /**
     * Check if a candidate has already applied for a specific job.
     */
    public static function hasApplied(int $candidateId, int $jobId): bool
    {
        return static::exists(['candidate_id' => $candidateId, 'job_id' => $jobId]);
    }

    /**
     * Get full application details with joins.
     */
    public static function getDetails(int $id): ?array
    {
        $db = Database::getInstance();
        return $db->fetchOne(
            'SELECT a.*, 
                    c.user_id, u.name as candidate_name, u.cnic, u.phone, 
                    j.title as job_title, j.fee,
                    p.name as project_name, p.org_name,
                    tc.name as center_name, tc.city as center_city,
                    cs.slot_date, cs.slot_time,
                    pay.status as payment_status, pay.transaction_id
             FROM applications a
             JOIN candidates c ON c.id = a.candidate_id
             JOIN users u ON u.id = c.user_id
             JOIN jobs j ON j.id = a.job_id
             JOIN projects p ON p.id = j.project_id
             JOIN center_slots cs ON cs.id = a.slot_id
             JOIN test_centers tc ON tc.id = cs.center_id
             LEFT JOIN payments pay ON pay.application_id = a.id
             WHERE a.id = ?',
            [$id]
        );
    }
}
