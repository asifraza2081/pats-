@extends('layouts.dashboard')
@section('page-title', 'Edit Expense')
@section('page-actions')
    <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back
    </a>
    <a href="{{ route('admin.expenses.print-voucher', $expense) }}" target="_blank" class="btn btn-outline-primary btn-sm">
        <i class="ti ti-printer me-1"></i> Print Voucher
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.expenses.update', $expense) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary py-3">
                    <h3 class="card-title text-white m-0 fw-bold"><i class="ti ti-receipt me-2"></i>Expense #{{ $expense->id }}</h3>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">Category</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $expense->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Project</label>
                            <select name="project_id" class="form-select">
                                <option value="">— Not project-specific —</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $expense->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required fw-semibold">Expense Date</label>
                            <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Voucher / PO No.</label>
                            <input type="text" name="voucher_no" class="form-control" value="{{ old('voucher_no', $expense->voucher_no) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label required fw-semibold">Description</label>
                            <textarea name="description" rows="2" class="form-control" required>{{ old('description', $expense->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #1B4F72, #2980B9);">
                    <h3 class="card-title text-white m-0 fw-bold"><i class="ti ti-calculator me-2"></i>Amount & Tax</h3>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label required fw-semibold">Gross Amount (PKR)</label>
                            <div class="input-group">
                                <span class="input-group-text">PKR</span>
                                <input type="number" name="gross_amount" id="gross_amount" step="0.01" min="0"
                                       class="form-control" value="{{ old('gross_amount', $expense->gross_amount) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label required fw-semibold">WHT Rate (%)</label>
                            <div class="input-group">
                                <input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="50"
                                       class="form-control" value="{{ old('tax_rate', $expense->tax_rate) }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="bg-light rounded p-3 border mt-1">
                                <div class="row text-center">
                                    <div class="col-6 border-end">
                                        <div class="small text-muted mb-1">Tax Withheld</div>
                                        <div class="h4 fw-black text-warning mb-0" id="tax_display">PKR {{ number_format($expense->tax_amount, 2) }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="small text-muted mb-1">Net Payable</div>
                                        <div class="h4 fw-black text-success mb-0" id="net_display">PKR {{ number_format($expense->net_amount, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Recipient Name</label>
                            <input type="text" name="recipient_name" class="form-control" value="{{ old('recipient_name', $expense->recipient_name) }}">
                        </div>
                        @if($fbrMode)
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Recipient NTN</label>
                            <input type="text" name="recipient_ntn" class="form-control" value="{{ old('recipient_ntn', $expense->recipient_ntn) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Recipient CNIC</label>
                            <input type="text" name="recipient_cnic" class="form-control" value="{{ old('recipient_cnic', $expense->recipient_cnic) }}">
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Replace Attachment</label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            @if($expense->attachment_path)
                            <div class="form-text">Current: <a href="{{ Storage::url($expense->attachment_path) }}" target="_blank">View current</a></div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" rows="3" class="form-control">{{ old('notes', $expense->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4"><i class="ti ti-device-floppy me-1"></i> Update Expense</button>
                <a href="{{ route('admin.expenses.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateTaxCalc() {
    var gross = parseFloat(document.getElementById('gross_amount').value) || 0;
    var rate  = parseFloat(document.getElementById('tax_rate').value) || 0;
    var tax   = Math.round(gross * rate / 100 * 100) / 100;
    var net   = Math.round((gross - tax) * 100) / 100;
    document.getElementById('tax_display').textContent = 'PKR ' + tax.toLocaleString('en', {minimumFractionDigits: 2});
    document.getElementById('net_display').textContent  = 'PKR ' + net.toLocaleString('en', {minimumFractionDigits: 2});
}
document.getElementById('gross_amount').addEventListener('input', updateTaxCalc);
document.getElementById('tax_rate').addEventListener('input', updateTaxCalc);
</script>
@endpush
