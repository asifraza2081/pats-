<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $projects = \App\Models\Project::where('status', 'open')->latest()->get();
        return view('welcome', compact('projects'));
    }
}
