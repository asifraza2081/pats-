<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'degree_level'   => 'required|integer|min:1|max:6',
            'degree_name'    => 'required|string|max:100',
            'subject_major'  => 'nullable|string|max:100',
            'institution'    => 'nullable|string|max:150',
            'passing_year'   => 'nullable|integer|min:1970|max:' . date('Y'),
            'marks_type'     => 'nullable|in:Marks,CGPA',
            'obtained_marks' => 'nullable|numeric|min:0',
            'total_marks'    => 'nullable|numeric|min:0',
        ];
    }
}
