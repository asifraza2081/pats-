<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $fields = ['open_date', 'close_date', 'test_date'];
        $normalised = [];
        foreach ($fields as $field) {
            $value = $this->input($field);
            if ($value !== null && $value !== '') {
                try {
                    $normalised[$field] = \Carbon\Carbon::parse($value)->toDateString();
                } catch (\Exception $e) {
                    $normalised[$field] = $value;
                }
            }
        }
        if ($normalised) {
            $this->merge($normalised);
        }
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:200',
            'org_name'   => 'required|string|max:200',
            'description'=> 'nullable|string',
            'open_date'  => 'nullable|date_format:Y-m-d',
            'close_date' => 'nullable|date_format:Y-m-d|after_or_equal:open_date',
            'test_date'  => 'nullable|date_format:Y-m-d',
            'status'     => 'required|in:draft,open,closed,result_declared',
            'logo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
