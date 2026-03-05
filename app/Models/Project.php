<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Project extends Model
{
    protected static string $table = 'projects';

    /**
     * Get open active projects.
     */
    public static function getOpen(): array
    {
        return static::where(['status' => 'open'], 'created_at DESC');
    }

    /**
     * Get all jobs under a specific project.
     */
    public static function getJobs(int $projectId): array
    {
        return Job::where(['project_id' => $projectId], 'title ASC');
    }
}
