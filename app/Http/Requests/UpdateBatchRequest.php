<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $value = $this->input('test_date');
        if ($value !== null && $value !== '') {
            try {
                $this->merge(['test_date' => Carbon::parse($value)->toDateString()]);
            } catch (\Exception $e) {
                // leave as-is; validation will reject it
            }
        }
    }

    public function rules(): array
    {
        return [
            'test_date'      => 'sometimes|required|date_format:Y-m-d',
            'reporting_time' => 'required',
            'start_time'     => 'required|after:reporting_time',
            'total_seats'    => 'required|integer|min:1',
            'envelope_size'  => 'required|integer|min:10|max:100',
        ];
    }
}
