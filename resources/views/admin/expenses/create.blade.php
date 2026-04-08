@extends('layouts.dashboard')
@section('page-title', 'Record New Expense')
@section('page-actions')
    <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.expenses.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary py-3">
                    <h3 class="card-title text-white m-0 fw-bold"><i class="ti ti-receipt me-2"></i>Expense Details</h3>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">Category</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">— Select Category —</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Project (Optional)</label>
                            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror">
                                <option value="">— Not project-specific —</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required fw-semibold">Expense Date</label>
                            <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror"
                                   value="{{ old('expense_date', date('Y-m-d')) }}" required>
                            @error('expense_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Voucher / PO No.</label>
                            <input type="text" name="voucher_no" class="form-control" placeholder="e.g. PO-2024-001"
                                   value="{{ old('voucher_no') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label required fw-semibold">Description</label>
                            <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Describe what this expense is for..." required>{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tax Calculator --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #1B4F72, #2980B9);">
                    <h3 class="card-title text-white m-0 fw-bold"><i class="ti ti-calculator me-2"></i>Amount & Tax Calculator</h3>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label required fw-semibold">Gross Amount (PKR)</label>
                            <div class="input-group">
                                <span class="input-group-text">PKR</span>
                                <input type="number" name="gross_amount" id="gross_amount" step="0.01" min="0"
                                       class="form-control @error('gross_amount') is-invalid @enderror"
                                       value="{{ old('gross_amount') }}" placeholder="0.00" required>
                            </div>
                            <div class="form-text">Total amount before tax deduction</div>
                            @error('gross_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label required fw-semibold">WHT Rate (%)</label>
                            <div class="input-group">
                                <input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="50"
                                       class="form-control @error('tax_rate') is-invalid @enderror"
                                       value="{{ old('tax_rate', $defaultGst) }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">Default: {{ $defaultGst }}%</div>
                        </div>
                        <div class="col-md-5">
                            <div class="bg-light rounded p-3 border mt-1">
                                <div class="row text-center">
                                    <div class="col-6 border-end">
                                        <div class="small text-muted mb-1">Tax Withheld</div>
                                        <div class="h4 fw-black text-warning mb-0" id="tax_display">PKR 0.00</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="small text-muted mb-1">Net Payable</div>
                                        <div class="h4 fw-black text-success mb-0" id="net_display">PKR 0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recipient Info (FBR fields shown when FBR mode is on) --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3">
                    <h3 class="card-title m-0 fw-bold"><i class="ti ti-user me-2"></i>Recipient / Payee Information</h3>
                    @if(!$fbrMode)<small class="text-muted ms-2">(Optional — enable FBR Mode in Settings to make NTN required)</small>@endif
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold {{ $fbrMode ? 'required' : '' }}">Recipient Name</label>
                            <input type="text" name="recipient_name" class="form-control"
                                   placeholder="Name of payee / vendor"
                                   value="{{ old('recipient_name') }}">
                        </div>
                        @if($fbrMode)
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Recipient NTN</label>
                            <input type="text" name="recipient_ntn" class="form-control"
                                   placeholder="e.g. 1234567-8"
                                   value="{{ old('recipient_ntn') }}">
                            <div class="form-text">Required for Annex-A filing</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Recipient CNIC</label>
                            <input type="text" name="recipient_cnic" class="form-control"
                                   placeholder="XXXXX-XXXXXXX-X"
                                   value="{{ old('recipient_cnic') }}">
                            <div class="form-text">If no NTN available</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Attachment & Notes --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><i class="ti ti-paperclip me-1"></i>Attach Voucher / Receipt</label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF or image, max 5MB. Strongly recommended for audit compliance.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Notes (Internal)</label>
                            <textarea name="notes" rows="3" class="form-control" placeholder="Internal notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ti ti-device-floppy me-1"></i> Save & Post to Ledger
                </button>
                <a href="{{ route('admin.expenses.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Sidebar quick reference --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-body">
                <h4 class="fw-bold"><i class="ti ti-bulb me-2 text-yellow"></i>Quick Reference</h4>
                <hr>
                <p class="small text-muted">Per FBR Section 153, withholding tax must be deducted at source when:</p>
                <ul class="small">
                    <li>Payment to a <strong>Company</strong>: deduct <strong>3%</strong> (Filer) or <strong>6%</strong> (Non-Filer)</li>
                    <li>Payment to <strong>Individual/AOP</strong>: <strong>3.5%</strong> (Filer) or <strong>7%</strong> (Non-Filer)</li>
                    <li>Threshold: payments above <strong>Rs. 30,000</strong></li>
                </ul>
                <p class="small text-muted mt-2">Always verify with your tax advisor for current rates.</p>
            </div>
        </div>
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
updateTaxCalc();
</script>
@endpush
