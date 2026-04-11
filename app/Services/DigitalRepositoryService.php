<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\ExamRollno;
use App\Models\Result;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DigitalRepositoryService
{
    /**
     * Get the base directory for a project with standardized naming.
     * Format: "Project Name - YYYY-MM-DD"
     */
    public function getProjectBaseDir(Batch|ExamRollno|Result $model): string
    {
        if ($model instanceof Batch) {
            $project = $model->project;
            $date = $model->test_date;
        } elseif ($model instanceof Result) {
            $project = $model->application->project;
            $date = $model->published_at ?? ($model->application->project->close_date ?? now());
        } else { // ExamRollno
            $project = $model->application->project;
            $date = $model->batch->test_date ?? ($model->application->project->close_date ?? now());
        }
        
        $dateStr = $date instanceof \Carbon\Carbon ? $date->toDateString() : (string) $date;
        
        // Hardening: Slugify project name to prevent filesystem issues with special characters
        $projectName = Str::slug($project->name, '-', 'en');
        
        return "projects/{$projectName}-{$dateStr}";
    }

    /**
     * Get the job directory.
     */
    public function getJobDir($model): string
    {
        if ($model instanceof Batch) {
            // Hardening: Use Center name instead of hardcoded string to prevent folder collisions (§10.6)
            $jobTitle = Str::slug($model->center->name, '-', 'en');
        } else {
            $jobTitle = Str::slug($model->application->job->title, '-', 'en');
        }
        
        return $this->getProjectBaseDir($model) . '/' . $jobTitle;
    }

    /**
     * Get the candidate specific folder.
     */
    public function getCandidateDir(ExamRollno|Result $model): string
    {
        $rollNo = $model->roll_no;
        return $this->getJobDir($model) . "/Roll Numbers & Results/RollNo_{$rollNo}";
    }

    /**
     * Save an Attendance Sheet for a batch.
     */
    public function saveAttendanceSheet(Batch $batch, $pdfOutput): string
    {
        $dir = $this->getJobDir($batch) . "/Attendance Sheets";
        $filename = "Attendance_{$batch->center->name}_Batch_{$batch->batch_number}";
        $path = "{$dir}/" . Str::slug($filename, '_', 'en') . ".pdf";
        
        Storage::disk('public')->put($path, $pdfOutput);
        return $path;
    }

    /**
     * Save a Roll Number Slip for a candidate.
     */
    public function saveRollNumberSlip(ExamRollno $roll, $pdfOutput): string
    {
        $dir = $this->getCandidateDir($roll);
        $path = "{$dir}/RollNoSlip.pdf";
        
        Storage::disk('public')->put($path, $pdfOutput);
        return $path;
    }

    /**
     * Save a Result Card for a candidate.
     */
    public function saveResultCard(Result $result, $pdfOutput): string
    {
        $dir = $this->getCandidateDir($result);
        $path = "{$dir}/ResultCard.pdf";
        
        Storage::disk('public')->put($path, $pdfOutput);
        return $path;
    }

    /**
     * Save a Summary report.
     */
    public function saveSummary($model, $type, $pdfOutput): string
    {
        $dir = $this->getJobDir($model) . "/Summary";
        $filename = Str::slug("{$type}_Summary", '_', 'en') . ".pdf";
        $path = "{$dir}/{$filename}";
        
        Storage::disk('public')->put($path, $pdfOutput);
        return $path;
    }
}
