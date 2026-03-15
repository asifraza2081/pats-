<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                 => 'required|string|max:150',
            'department'            => 'nullable|string|max:120',
            'bps_grade'             => 'nullable|string|max:20',
            'total_seats'           => 'required|integer|min:1',
            'quota_notes'           => 'nullable|string',
            'fee'                   => 'required|numeric|min:0',
            'min_degree_level'      => 'nullable|integer|min:1|max:6',
            'min_qualification_name'=> 'nullable|string|max:100',
            'required_subject'      => 'nullable|string|max:100',
            'min_experience_years'  => 'nullable|numeric|min:0',
            'experience_sector'     => 'required|in:Any,Public,Private',
            'age_min'               => 'nullable|integer|min:16',
            'age_max'               => 'nullable|integer|max:70|gte:age_min',
            'domicile_required'     => 'nullable|string|max:80',
        ];
    }
}
