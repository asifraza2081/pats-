<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'father_name'           => 'required|string|max:120',
            'dob'                   => 'required|date_format:Y-m-d|before:today',
            'gender'                => 'required|in:Male,Female',
            'marital_status'        => 'required|in:Single,Married,Divorced,Widowed',
            'religion'              => 'required|in:Islam,Christianity,Hinduism,Sikhism,Other',
            'blood_group'           => 'nullable|string|max:5',
            'current_occupation'    => 'nullable|string|max:120',
            'disability'            => 'nullable|boolean',
            'disability_type'       => 'nullable|string|max:120',
            'domicile_city_id'      => 'nullable|exists:cities,id',
            'address_city_id'       => 'nullable|exists:cities,id',
            'permanent_address'     => 'required|string',
            'postal_address'        => 'required_without:same_postal_address|nullable|string',
            'same_postal_address'   => 'nullable|boolean',
            'alternate_phone'       => 'nullable|string|max:15',
            'cnic'                  => ['nullable', 'regex:/^\d{5}-\d{7}-\d{1}$/', 'unique:users,cnic,' . auth()->id()],
            'photo'                 => 'nullable|image|max:5120',
            'cnic_copy'             => 'nullable|image|max:5120',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'disability'          => $this->boolean('disability'),
            'same_postal_address' => $this->boolean('same_postal_address'),
        ]);

        $value = $this->input('dob');
        if ($value !== null && $value !== '') {
            try {
                $this->merge(['dob' => \Carbon\Carbon::parse($value)->toDateString()]);
            } catch (\Exception $e) {}
        }
    }
}
