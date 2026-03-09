<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::with(['candidate.user', 'job.project', 'batch.center', 'payment', 'rollNumber'])
            ->latest('applied_at')
            ->paginate(25);
            
        return view('admin.applications.index', compact('applications'));
    }

    public function show(Application $app)
    {
        $app->load([
            'candidate.user', 
            'candidate.education', 
            'candidate.experience', 
            'job.project', 
            'batch.center', 
            'payment', 
            'rollNumber', 
            'result'
        ]);
        
        return view('admin.applications.show', compact('app'));
    }
}
