@extends('layouts.dashboard')
@section('page-title', 'Financial Settings')
@section('page-actions')
    <a href="{{ route('admin.financials.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i> Back to Dashboard
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.financials.settings.save') }}">
            @csrf

            {{-- General Settings --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary py-3">
                    <h3 class="card-title text-white m-0 fw-bold"><i class="ti ti-adjustments me-2"></i>General Financial Settings</h3>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label required fw-semibold">Organisation Legal Name</label>
                            <input type="text" name="org_name_for_tax" class="form-control @error('org_name_for_tax') is-invalid @enderror"
                                   value="{{ old('org_name_for_tax', $settings['org_name_for_tax']?->value ?? '') }}" required>
                            <div class="form-text">Used on all printed tax documents</div>
                            @error('org_name_for_tax') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Organisation Address</label>
                            <input type="text" name="org_address_for_tax" class="form-control"
                                   value="{{ old('org_address_for_tax', $settings['org_address_for_tax']?->value ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required fw-semibold">Default GST / WHT Rate (%)</label>
                            <div class="input-group">
                                <input type="number" name="default_gst_rate" step="0.01" min="0" max="50"
                                       class="form-control @error('default_gst_rate') is-invalid @enderror"
                                       value="{{ old('default_gst_rate', $settings['default_gst_rate']?->value ?? 15) }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">Pre-fills each new expense. Can be overridden per line item.</div>
                            @error('default_gst_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fiscal Year Start Month</label>
                            <input type="text" class="form-control bg-light" value="July (Month 7) — Pakistan Standard" readonly>
                            <div class="form-text text-success"><i class="ti ti-lock me-1"></i>Fixed to July per Pakistan FBR requirement.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FBR Advanced Mode --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #1B4F72, #2980B9);">
                    <h3 class="card-title text-white m-0 fw-bold"><i class="ti ti-receipt-tax me-2"></i>FBR Pakistan Tax Compliance Mode</h3>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="fbr_mode_enabled" value="1"
                                   {{ old('fbr_mode_enabled', $settings['fbr_mode_enabled']?->value ?? '0') == '1' ? 'checked' : '' }}
                                   id="fbrToggle">
                            <span class="form-check-label fw-bold">Enable FBR Mode</span>
                        </label>
                        <div class="form-text mt-1">
                            When enabled, unlocks: Annex-A WHT register, NTN/CNIC tracking on expenses,
                            Income & Expenditure statement, and CSV export compatible with FBR IRIS.
                        </div>
                    </div>

                    <div id="fbrFields" class="{{ ($settings['fbr_mode_enabled']?->value ?? '0') == '1' ? '' : 'd-none' }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Organisation NTN</label>
                                <input type="text" name="org_ntn" class="form-control @error('org_ntn') is-invalid @enderror"
                                       placeholder="e.g. 1234567-8"
                                       value="{{ old('org_ntn', $settings['org_ntn']?->value ?? '') }}">
                                <div class="form-text">National Tax Number — required for FBR tax returns</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">STRN (Sales Tax Registration)</label>
                                <input type="text" name="org_strn" class="form-control @error('org_strn') is-invalid @enderror"
                                       placeholder="e.g. 0312345678901"
                                       value="{{ old('org_strn', $settings['org_strn']?->value ?? '') }}">
                                <div class="form-text">Required for GST input tax claim purposes</div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            <div class="d-flex">
                                <i class="ti ti-info-circle me-2 mt-1 text-azure"></i>
                                <div>
                                    <strong>FBR Documents Available:</strong>
                                    <ul class="mb-0 mt-1 small">
                                        <li><strong>Annex-A (Section 153)</strong>: Withholding tax register for payments to contractors/services</li>
                                        <li><strong>Income & Expenditure Statement</strong>: Annual I&amp;E account for NPO/Trust tax returns</li>
                                        <li><strong>IRIS-compatible CSV</strong>: For direct upload to FBR's IRIS portal</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ti ti-device-floppy me-1"></i> Save Settings
                </button>
                <a href="{{ route('admin.financials.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Info panel --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h4 class="fw-bold mb-3"><i class="ti ti-help-circle me-2 text-primary"></i>How Tax Calculation Works</h4>
                <div class="d-flex gap-2 mb-2">
                    <span class="badge bg-primary-lt text-primary">1</span>
                    <div class="small">Enter the <strong>gross amount</strong> (total payment before tax deduction)</div>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <span class="badge bg-primary-lt text-primary">2</span>
                    <div class="small">Set the <strong>WHT rate</strong> (default {{ $settings['default_gst_rate']?->value ?? 15 }}%). Can change per expense.</div>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <span class="badge bg-primary-lt text-primary">3</span>
                    <div class="small">System auto-calculates <strong>Tax Amount = Gross × Rate ÷ 100</strong></div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary-lt text-primary">4</span>
                    <div class="small"><strong>Net Payable = Gross − Tax</strong> — the actual amount paid to vendor</div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h4 class="fw-bold mb-3"><i class="ti ti-shield me-2 text-green"></i>Audit Trail</h4>
                <p class="small text-muted mb-0">All financial ledger entries are <strong>immutable</strong>. Even if an expense is deleted, its ledger entry is preserved permanently for audit compliance.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('fbrToggle').addEventListener('change', function() {
    document.getElementById('fbrFields').classList.toggle('d-none', !this.checked);
});
</script>
@endpush
