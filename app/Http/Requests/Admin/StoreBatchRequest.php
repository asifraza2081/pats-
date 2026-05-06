<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id'         => 'required|exists:projects,id',
            'job_ids'            => 'required|array|min:1',
            'job_ids.*'          => 'exists:pats_jobs,id',
            'center_ids'         => 'required|array|min:1',
            'center_ids.*'       => 'exists:test_centers,id',
            'batch_number'       => 'required|integer|min:1',
            'test_date'          => 'required|date|after_or_equal:today',
            'reporting_time'     => 'required',
            'start_time'         => 'required|after:reporting_time',
            'duration_minutes'   => 'required|integer|min:30',
            'envelope_size'      => 'required|integer|min:10|max:100',
            'count_to_allocate'  => 'required|integer|min:1',
        ];
    }
}
