<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id'     => 'nullable|exists:projects,id',
            'category_id'    => 'required|exists:financial_categories,id',
            'expense_date'   => 'required|date',
            'voucher_no'     => 'nullable|string|max:50',
            'description'    => 'required|string|max:500',
            'recipient_name' => 'nullable|string|max:150',
            'recipient_ntn'  => 'nullable|string|max:20',
            'recipient_cnic' => 'nullable|string|max:20',
            'gross_amount'   => 'required|numeric|min:0',
            'tax_rate'       => 'required|numeric|min:0|max:50',
            'attachment'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes'          => 'nullable|string|max:1000',
        ];
    }
}
