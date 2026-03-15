<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCenterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $centerId = $this->route('center')->id;
        return [
            'tcid'             => "required|string|max:10|unique:test_centers,tcid,{$centerId}",
            'name'             => 'required|string|max:150',
            'city_id'          => 'required|exists:cities,id',
            'seating_capacity' => 'required|integer|min:1',
            'address'          => 'required|string',
            'map_url'          => 'nullable|url',
            'is_active'        => 'boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
