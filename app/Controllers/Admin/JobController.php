<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Core\Database;
use App\Models\Job;
use App\Models\Project;

class JobController
{
    public function __construct()
    {
        Auth::requireRole('super_admin', 'admin');
    }

    public function index(Request $request, array $params = []): void
    {
        $db = Database::getInstance();
        $jobs = $db->fetchAll(
            'SELECT j.*, p.name as project_name 
             FROM jobs j 
             JOIN projects p ON p.id = j.project_id 
             ORDER BY j.id DESC'
        );

        View::render('admin/jobs/index', ['pageTitle' => 'Manage Jobs', 'jobs' => $jobs], 'admin');
    }

    public function create(Request $request, array $params = []): void
    {
        $projects = Project::where(['status' => 'draft']); // mostly add to draft open projects
        $allProjects = Project::all();
        
        View::render('admin/jobs/create', [
            'pageTitle' => 'Add New Job',
            'projects'  => $allProjects
        ], 'admin');
    }

    public function store(Request $request, array $params = []): void
    {
        CSRF::check();

        $data = $request->only('project_id', 'title', 'department', 'bps_grade', 'total_seats', 'min_qualification', 'age_min', 'age_max', 'domicile_required', 'fee');

        $v = Validator::make($data, [
            'project_id' => 'required|numeric',
            'title'      => 'required|max:150',
            'fee'        => 'required|numeric',
            'total_seats'=> 'required|numeric',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('old', $data);
            Response::redirect('/admin/jobs/create');
        }

        $data['age_min'] = $data['age_min'] ?: null;
        $data['age_max'] = $data['age_max'] ?: null;
        $data['domicile_required'] = $data['domicile_required'] ?: null;

        Job::create($data);
        Session::flash('success', 'Job added successfully.');
        Response::redirect('/admin/jobs');
    }

    public function delete(Request $request, array $params = []): void
    {
        CSRF::check();
        Job::deleteWhere(['id' => (int)$params['id']]);
        Session::flash('success', 'Job deleted.');
        Response::redirect('/admin/jobs');
    }
}
