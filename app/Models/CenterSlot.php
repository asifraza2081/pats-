<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class CenterSlot extends Model
{
    protected static string $table = 'center_slots';

    /**
     * Get slots with future available capacity for a center/project.
     */
    public static function getAvailable(int $projectId, int $centerId): array
    {
        return \App\Core\Database::getInstance()->fetchAll(
            'SELECT * FROM center_slots 
             WHERE project_id = ? AND center_id = ? AND booked_seats < total_seats
             ORDER BY slot_date ASC, slot_time ASC',
            [$projectId, $centerId]
        );
    }
}
