<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\View;

class ReportController
{
    public function __construct()
    {
        Auth::requireRole('super_admin', 'admin');
    }

    public function index(Request $request, array $params = []): void
    {
        $db = Database::getInstance();
        $projects = $db->fetchAll('SELECT id, name, status FROM projects ORDER BY id DESC');
        View::render('admin/reports/index', ['pageTitle' => 'Export Data & Reports', 'projects' => $projects], 'admin');
    }

    public function exportApplications(Request $request, array $params = []): void
    {
        $projectId = (int)$request->get('project_id');

        $db = Database::getInstance();
        $query = '
            SELECT a.id as AppID, u.name as Name, u.cnic as CNIC, u.phone as Phone,
                   j.title as PostApplied, tc.city as PreferredCity,
                   a.status as AppStatus, pay.status as FeeStatus, 
                   rn.roll_number as RollNo, a.applied_at as AppliedAt
            FROM applications a
            JOIN candidates c ON c.id = a.candidate_id
            JOIN users u ON u.id = c.user_id
            JOIN jobs j ON j.id = a.job_id
            JOIN center_slots cs ON cs.id = a.slot_id
            JOIN test_centers tc ON tc.id = cs.center_id
            LEFT JOIN payments pay ON pay.application_id = a.id
            LEFT JOIN roll_numbers rn ON rn.application_id = a.id
        ';

        if ($projectId) {
            $query .= " WHERE j.project_id = " . $projectId;
        }
        
        $query .= " ORDER BY a.id ASC";

        $data = $db->fetchAll($query);

        if (empty($data)) {
            \App\Core\Session::flash('error', 'No records found for export.');
            \App\Core\Response::redirect('/admin/reports');
        }

        $filename = "Export_Applications_" . date('Ymd_Hi') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // Write Headers
        fputcsv($output, array_keys($data[0]));

        // Write Rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
