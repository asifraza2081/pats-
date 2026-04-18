<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\ExamRollno;
use App\Models\Result;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DigitalRepositoryService
{
    /**
     * Build the center-level storage path.
     * Required structure: {date}/{project-slug}/{city-slug}/{center-slug}/
     */
    public function buildCenterPath(Batch $batch): string
    {
        $batch->loadMissing(['project', 'center.city']);
        
        $date = $batch->test_date instanceof Carbon ? $batch->test_date->toDateString() : (string) $batch->test_date;
        $projectSlug = Str::slug($batch->project->name ?? 'Project', '-', 'en');
        $citySlug = Str::slug($batch->center->city->name ?? 'City', '-', 'en');
        $centerSlug = Str::slug($batch->center->name ?? 'Center', '-', 'en');
        
        return "{$date}/{$projectSlug}/{$citySlug}/{$centerSlug}";
    }

    /**
     * Build the candidate-level storage path.
     * Required structure: {date}/{project-slug}/{city-slug}/{center-slug}/{roll-no}/
     */
    public function buildCandidatePath(ExamRollno $roll): string
    {
        $roll->loadMissing(['batch.project', 'batch.center.city']);
        
        $batch = $roll->batch;
        if (!$batch) {
            // Fallback if no batch is linked (e.g., tests without batches, though PATS uses batches)
            $date = date('Y-m-d');
            $projectSlug = Str::slug($roll->application->project->name ?? 'Project', '-', 'en');
            $citySlug = Str::slug($roll->city->name ?? 'City', '-', 'en');
            $centerSlug = Str::slug($roll->center->name ?? 'Center', '-', 'en');
            return "{$date}/{$projectSlug}/{$citySlug}/{$centerSlug}/{$roll->roll_no}";
        }
        
        return $this->buildCenterPath($batch) . '/' . $roll->roll_no;
    }

    /**
     * Save an Attendance Sheet for a batch.
     */
    public function saveAttendanceSheet(Batch $batch, $pdfOutput): string
    {
        $dir = $this->buildCenterPath($batch);
        $filename = "attendance-sheet-batch-{$batch->batch_number}.pdf";
        $path = "{$dir}/{$filename}";
        
        Storage::disk('public')->put($path, $pdfOutput);
        return $path;
    }

    /**
     * Save a Roll Number Slip for a candidate.
     */
    public function saveRollNumberSlip(ExamRollno $roll, $pdfOutput): string
    {
        $dir = $this->buildCandidatePath($roll);
        $path = "{$dir}/roll-slip.pdf";
        
        Storage::disk('public')->put($path, $pdfOutput);
        return $path;
    }

    /**
     * Save a Fee Challan for a candidate.
     */
    public function saveChallan(ExamRollno $roll, $pdfOutput): string
    {
        $dir = $this->buildCandidatePath($roll);
        $path = "{$dir}/challan.pdf";
        
        Storage::disk('public')->put($path, $pdfOutput);
        return $path;
    }

    /**
     * Save a Result Card for a candidate.
     */
    public function saveResultCard(Result $result, $pdfOutput): string
    {
        $result->loadMissing(['application.examRollno']);
        $roll = $result->application->examRollno;
        
        if ($roll) {
            $dir = $this->buildCandidatePath($roll);
        } else {
            // Fallback for results without roll numbers
            $date = $result->published_at ? $result->published_at->toDateString() : date('Y-m-d');
            $projectSlug = Str::slug($result->application->project->name ?? 'Project', '-', 'en');
            $dir = "{$date}/{$projectSlug}/Results/App_{$result->application_id}";
        }
        
        $path = "{$dir}/result-card.pdf";
        Storage::disk('public')->put($path, $pdfOutput);
        return $path;
    }

    /**
     * Save OMR sheets for a test center.
     */
    public function saveOmrSheets(Batch $batch, $pdfOutput): string
    {
        $dir = $this->buildCenterPath($batch);
        $filename = "omr-sheets-batch-{$batch->batch_number}.pdf";
        $path = "{$dir}/{$filename}";
        
        Storage::disk('public')->put($path, $pdfOutput);
        return $path;
    }

    /**
     * Save bulk roll number slips for a test center.
     */
    public function saveBulkSlips(Batch $batch, $pdfOutput): string
    {
        $dir = $this->buildCenterPath($batch);
        $filename = "bulk-slips-batch-{$batch->batch_number}.pdf";
        $path = "{$dir}/{$filename}";
        
        Storage::disk('public')->put($path, $pdfOutput);
        return $path;
    }

    /**
     * Save a generic Summary report.
     * Retained for compatibility with existing code.
     */
    public function saveSummary($model, $type, $pdfOutput): string
    {
        if ($model instanceof Batch) {
            if ($type === 'AnswerSheets') {
                return $this->saveOmrSheets($model, $pdfOutput);
            }
            if ($type === 'BulkSlips') {
                return $this->saveBulkSlips($model, $pdfOutput);
            }
            $dir = $this->buildCenterPath($model);
        } else {
            $dir = "Misc_Summaries";
        }
        
        $filename = Str::slug("{$type}_Summary", '-', 'en') . ".pdf";
        $path = "{$dir}/{$filename}";
        
        Storage::disk('public')->put($path, $pdfOutput);
        return $path;
    }
}
