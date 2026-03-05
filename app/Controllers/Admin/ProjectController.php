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
use App\Models\Project;

class ProjectController
{
    public function __construct()
    {
        Auth::requireRole('super_admin', 'admin');
    }

    public function index(Request $request, array $params = []): void
    {
        $projects = Project::all();
        View::render('admin/projects/index', ['pageTitle' => 'Manage Projects', 'projects' => $projects], 'admin');
    }

    public function create(Request $request, array $params = []): void
    {
        View::render('admin/projects/create', ['pageTitle' => 'Create Project'], 'admin');
    }

    public function store(Request $request, array $params = []): void
    {
        CSRF::check();

        $data = $request->only('name', 'org_name', 'description', 'open_date', 'close_date', 'test_date', 'status');
        
        $v = Validator::make($data, [
            'name'       => 'required|max:200',
            'org_name'   => 'required|max:200',
            'open_date'  => 'required|date',
            'close_date' => 'required|date',
            'status'     => 'required|in:draft,open,closed,result_declared',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('old', $data);
            Response::redirect('/admin/projects/create');
        }

        $data['created_by']  = Auth::id();
        $data['description'] = $data['description'] ?: null;
        $data['test_date']   = $data['test_date'] ?: null;

        Project::create($data);
        Session::flash('success', 'Project created successfully.');
        Response::redirect('/admin/projects');
    }

    public function edit(Request $request, array $params = []): void
    {
        $project = Project::find((int) $params['id']);
        if (!$project) Response::abort(404);

        View::render('admin/projects/edit', ['pageTitle' => 'Edit Project', 'project' => $project], 'admin');
    }

    public function update(Request $request, array $params = []): void
    {
        CSRF::check();
        
        $id = (int) $params['id'];
        $project = Project::find($id);
        if (!$project) Response::abort(404);

        $data = $request->only('name', 'org_name', 'description', 'open_date', 'close_date', 'test_date', 'status');
        
        $v = Validator::make($data, [
            'name'       => 'required|max:200',
            'org_name'   => 'required|max:200',
            'open_date'  => 'required|date',
            'close_date' => 'required|date',
            'status'     => 'required|in:draft,open,closed,result_declared',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Response::redirect("/admin/projects/{$id}/edit");
        }

        $data['description'] = $data['description'] ?: null;
        $data['test_date']   = $data['test_date'] ?: null;

        Project::updateWhere($data, ['id' => $id]);
        Session::flash('success', 'Project updated successfully.');
        Response::redirect('/admin/projects');
    }

    public function delete(Request $request, array $params = []): void
    {
        CSRF::check();
        Project::deleteWhere(['id' => (int)$params['id']]);
        Session::flash('success', 'Project deleted.');
        Response::redirect('/admin/projects');
    }
}
