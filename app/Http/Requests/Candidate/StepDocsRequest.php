<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Foundation\Http\FormRequest;

class StepDocsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo'     => 'nullable|image|max:5120',
            'cnic_copy' => 'nullable|image|max:5120',
        ];
    }
}
