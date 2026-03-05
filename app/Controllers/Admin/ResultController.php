<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\Project;
use App\Models\Result;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ResultController
{
    public function __construct()
    {
        Auth::requireRole('super_admin', 'admin', 'data_entry');
    }

    public function index(Request $request, array $params = []): void
    {
        $db = Database::getInstance();
        $results = $db->fetchAll(
            'SELECT r.*, u.name as candidate_name, u.cnic, j.title as job_title, p.name as project_name
             FROM results r
             JOIN applications a ON a.id = r.application_id
             JOIN candidates c ON c.id = a.candidate_id
             JOIN users u ON u.id = c.user_id
             JOIN jobs j ON j.id = a.job_id
             JOIN projects p ON p.id = j.project_id
             ORDER BY r.uploaded_at DESC LIMIT 500'
        );

        View::render('admin/results/index', ['pageTitle' => 'Exam Results', 'results' => $results], 'admin');
    }

    public function uploadForm(Request $request, array $params = []): void
    {
        $projects = Project::where(['status' => 'open']);
        // Included 'result_declared' and 'closed' if you allow late uploads
        $allProjects = Project::all();
        
        View::render('admin/results/upload', ['pageTitle' => 'Upload Results', 'projects' => $allProjects], 'admin');
    }

    public function upload(Request $request, array $params = []): void
    {
        CSRF::check();
        
        $projectId = (int) $request->post('project_id');
        $file = $request->file('result_file');

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Please select a valid CSV/Excel file.');
            Response::redirect('/admin/results/upload');
        }

        try {
            $spreadsheet = IOFactory::load($file['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Remove header
            array_shift($rows);

            $parsedData = [];
            $db = Database::getInstance();

            foreach ($rows as $row) {
                // Expected order: RollNumber | Score | Percentage | Status | Remarks
                $rollNo = trim((string)$row[0]);
                if (!$rollNo) continue;

                $score   = isset($row[1]) ? (float)$row[1] : 0;
                $pct     = isset($row[2]) ? (float)$row[2] : 0;
                $status  = isset($row[3]) ? strtolower(trim((string)$row[3])) : 'pass';
                $remarks = isset($row[4]) ? trim((string)$row[4]) : '';

                if (!in_array($status, ['pass', 'fail', 'absent', 'withheld'])) {
                    $status = 'pass';
                }

                // Verify Roll Number belongs to the selected project
                $appData = $db->fetchOne(
                    'SELECT rn.application_id, a.job_id 
                     FROM roll_numbers rn 
                     JOIN applications a ON a.id = rn.application_id 
                     JOIN jobs j ON j.id = a.job_id 
                     WHERE rn.roll_number = ? AND j.project_id = ?',
                    [$rollNo, $projectId]
                );

                if ($appData) {
                    $parsedData[] = [
                        'application_id' => $appData['application_id'],
                        'roll_number'    => $rollNo,
                        'score'          => $score,
                        'percentage'     => $pct,
                        'status'         => $status,
                        'remarks'        => $remarks
                    ];
                }
            }

            // Save to session for review
            Session::set('pending_results', $parsedData);
            Session::set('pending_project_id', $projectId);
            
            Session::flash('success', count($parsedData) . ' valid records found. Please review and commit.');
            Response::redirect('/admin/results/upload');

        } catch (\Exception $e) {
            Session::flash('error', 'Error parsing file: ' . $e->getMessage());
            Response::redirect('/admin/results/upload');
        }
    }

    public function commit(Request $request, array $params = []): void
    {
        CSRF::check();
        
        $pending = Session::get('pending_results');
        $projectId = Session::get('pending_project_id');

        if (!$pending) {
            Session::flash('error', 'No pending results found.');
            Response::redirect('/admin/results/upload');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $count = 0;
            foreach ($pending as $row) {
                // Check if already exists
                $exists = Result::findBy('application_id', $row['application_id']);
                if ($exists) {
                    Result::updateWhere([
                        'score'       => $row['score'],
                        'percentage'  => $row['percentage'],
                        'status'      => $row['status'],
                        'remarks'     => $row['remarks'],
                        'uploaded_by' => Auth::id()
                    ], ['id' => $exists['id']]);
                } else {
                    Result::create([
                        'application_id' => $row['application_id'],
                        'roll_number'    => $row['roll_number'],
                        'score'          => $row['score'],
                        'percentage'     => $row['percentage'],
                        'status'         => $row['status'],
                        'remarks'        => $row['remarks'],
                        'uploaded_by'    => Auth::id()
                    ]);
                }
                
                // Update Application status
                Application::updateWhere(['status' => 'result_declared'], ['id' => $row['application_id']]);
                $count++;
            }

            // Mark project as Result Declared if requested
            if ($request->post('declare_project')) {
                Project::updateWhere(['status' => 'result_declared'], ['id' => $projectId]);
            }

            $db->commit();
            
            Session::remove('pending_results');
            Session::remove('pending_project_id');
            Session::flash('success', "{$count} results committed to database successfully.");
            Response::redirect('/admin/results');

        } catch (\Exception $e) {
            $db->rollBack();
            Session::flash('error', 'Database Error: ' . $e->getMessage());
            Response::redirect('/admin/results/upload');
        }
    }
}
