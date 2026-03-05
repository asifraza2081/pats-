<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Models\Project;

class ProjectController
{
    public function home(Request $request, array $params = []): void
    {
        $projects = Project::getOpen();
        
        View::render('home', [
            'pageTitle' => 'Welcome to PATS',
            'projects'  => $projects
        ]);
    }

    public function index(Request $request, array $params = []): void
    {
        $projects = Project::getOpen();
        
        View::render('projects/index', [
            'pageTitle' => 'Open Projects',
            'projects'  => $projects
        ]);
    }

    public function show(Request $request, array $params = []): void
    {
        $project = Project::find((int) $params['id']);
        
        if (!$project || $project['status'] !== 'open') {
            \App\Core\Session::flash('error', 'Project not found or closed.');
            \App\Core\Response::redirect('/projects');
        }

        $jobs = Project::getJobs((int) $project['id']);

        View::render('projects/show', [
            'pageTitle' => $project['name'],
            'project'   => $project,
            'jobs'      => $jobs
        ]);
    }
}
