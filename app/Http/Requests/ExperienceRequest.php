<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_type'          => 'required|in:Public,Private',
            'organization_name' => 'required|string|max:150',
            'designation'       => 'required|string|max:120',
            'from_date'         => 'required|date_format:Y-m-d|before_or_equal:today',
            'to_date'           => 'nullable|date_format:Y-m-d|after:from_date|before_or_equal:today',
            'is_current'        => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_current' => $this->boolean('is_current')]);

        foreach (['from_date', 'to_date'] as $field) {
            $value = $this->input($field);
            if ($value !== null && $value !== '') {
                try {
                    $this->merge([$field => \Carbon\Carbon::parse($value)->toDateString()]);
                } catch (\Exception $e) {}
            }
        }
    }
}
