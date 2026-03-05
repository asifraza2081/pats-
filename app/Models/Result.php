<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Result extends Model
{
    protected static string $table = 'results';

    /**
     * Search result by Roll Number or CNIC
     */
    public static function search(string $query): ?array
    {
        $db = \App\Core\Database::getInstance();
        $cleanQuery = preg_replace('/[-\s]/', '', $query); // for cnic matching

        return $db->fetchOne(
            'SELECT r.*, a.job_id, j.title as job_title, p.name as project_name, u.name as candidate_name, u.cnic
             FROM results r
             JOIN applications a ON a.id = r.application_id
             JOIN jobs j ON j.id = a.job_id
             JOIN projects p ON p.id = j.project_id
             JOIN candidates c ON c.id = a.candidate_id
             JOIN users u ON u.id = c.user_id
             WHERE (r.roll_number = ? OR u.cnic = ?) AND p.status = ?',
            [$query, $cleanQuery, 'result_declared']
        );
    }
}
