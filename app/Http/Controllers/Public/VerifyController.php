<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ExamRollno;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    /**
     * Show the verification result for a scanned document.
     */
    public function show($token)
    {
        $rollno = ExamRollno::where('verify_token', $token)
            ->with(['application.candidate', 'project', 'job', 'center', 'batch', 'application.result'])
            ->firstOrFail();

        return view('public.verify', compact('rollno'));
    }
}
