<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class TestCenter extends Model
{
    protected static string $table = 'test_centers';

    /**
     * Map a center to a specific project.
     */
    public static function assignToProject(int $projectId, int $centerId): void
    {
        $db = Database::getInstance();
        $exists = $db->fetchOne('SELECT 1 FROM project_centers WHERE project_id = ? AND center_id = ?', [$projectId, $centerId]);
        if (!$exists) {
            $db->insert('project_centers', ['project_id' => $projectId, 'center_id' => $centerId]);
        }
    }

    /**
     * Get active centers assigned to a project.
     */
    public static function getForProject(int $projectId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            'SELECT tc.* FROM test_centers tc
             JOIN project_centers pc ON pc.center_id = tc.id
             WHERE pc.project_id = ? AND tc.is_active = 1
             ORDER BY tc.city ASC, tc.name ASC',
            [$projectId]
        );
    }
}
