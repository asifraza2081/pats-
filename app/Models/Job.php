<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Job extends Model
{
    protected static string $table = 'jobs';

    /**
     * Get job with its parent project info.
     */
    public static function getWithProject(int $jobId): ?array
    {
        $db = \App\Core\Database::getInstance();
        return $db->fetchOne(
            'SELECT j.*, p.name as project_name, p.status as project_status, p.close_date
             FROM jobs j
             JOIN projects p ON p.id = j.project_id
             WHERE j.id = ?',
            [$jobId]
        );
    }
}
