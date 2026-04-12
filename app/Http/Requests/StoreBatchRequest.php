<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Middleware handles auth/roles
    }

    public function rules(): array
    {
        return [
            'project_id'       => 'required|exists:projects,id',
            'job_ids'          => 'required|array|min:1',
            'job_ids.*'        => 'required|exists:pats_jobs,id',
            'center_ids'       => 'required|array|min:1',
            'center_ids.*'     => 'required|exists:test_centers,id',
            'batch_number'     => 'required|integer|min:1',
            'test_date'        => 'required|date_format:Y-m-d|after_or_equal:today|before:2100-01-01',
            'reporting_time'   => 'required',
            'start_time'       => 'required|after:reporting_time',
            'duration_minutes' => 'required|integer|min:30|max:1440',
            'count_to_allocate'=> 'required|integer|min:1',
            'envelope_size'    => 'required|integer|min:10|max:100',
        ];
    }
}
