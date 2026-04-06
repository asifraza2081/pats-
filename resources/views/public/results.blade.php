@extends('layouts.public')
@section('title', 'Check Results — Prime Assessment & Testing Services')
@section('header-title', 'Official Results')

@section('content')
<div class="container-xl py-6">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-6 animate__animated animate__fadeIn">
                <div class="avatar avatar-xl bg-grad-accent text-white rounded-circle shadow-teal-30 mb-4 mx-auto">
                    <i class="ti ti-chart-bar fs-0"></i>
                </div>
                <h2 class="display-4 fw-black text-dark mb-2">Check Your Result</h2>
                <p class="text-muted fs-3 opacity-80">Enter both your Roll Number and CNIC to access your secure result report.</p>
            </div>
            
            @if(session('error'))
            <div class="alert alert-danger rounded-4 mb-4 shadow-sm animate__animated animate__shakeX">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-triangle me-3 fs-2"></i>
                    <div class="fw-bold">{{ session('error') }}</div>
                </div>
            </div>
            @endif

            <div class="card glass-panel border-0 shadow-lg mb-6 rounded-5 overflow-hidden animate__animated animate__zoomIn">
                <div class="card-body p-5 p-md-7">
                    <form method="GET" action="{{ route('results.search') }}">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-black text-dark opacity-60 small uppercase tracking-widest mb-3">Roll Number</label>
                                <div class="input-group input-group-flat shadow-none rounded-4 overflow-hidden border border-teal border-opacity-20">
                                    <span class="input-group-text bg-white border-0 ps-3">
                                        <i class="ti ti-id-badge text-teal fs-3"></i>
                                    </span>
                                    <input type="text" name="roll_no" class="form-control form-control-lg border-0 bg-white fs-3 py-3 shadow-none" placeholder="e.g. 102030" value="{{ request('roll_no') }}" autofocus required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-black text-dark opacity-60 small uppercase tracking-widest mb-3">CNIC Number</label>
                                <div class="input-group input-group-flat shadow-none rounded-4 overflow-hidden border border-teal border-opacity-20">
                                    <span class="input-group-text bg-white border-0 ps-3">
                                        <i class="ti ti-mask text-teal fs-3"></i>
                                    </span>
                                    <input type="text" name="cnic" id="cnic_mask" class="form-control form-control-lg border-0 bg-white fs-3 py-3 shadow-none" placeholder="XXXXX-XXXXXXX-X" value="{{ request('cnic') }}" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-teal w-100 py-4 fs-2 fw-black rounded-pill border-0 shadow-teal-30">
                            <i class="ti ti-search me-3"></i> FETCH OFFICIAL RESULT
                        </button>
                    </form>
                </div>
                <div class="bg-light py-3 px-5 border-top border-dark border-opacity-5">
                    <div class="d-flex align-items-center justify-content-center gap-4 small fw-bold text-muted opacity-60">
                        <span><i class="ti ti-lock-check me-1"></i> SECURE ACCESS</span>
                        <span><i class="ti ti-shield-check me-1"></i> TWO-FACTOR VERIFICATION</span>
                    </div>
                </div>
            </div>

            @if(isset($results))
                @if($results->isNotEmpty())
                    @foreach($results as $result)
                    <div class="card border-0 shadow-2xl mb-5 rounded-5 overflow-hidden animate__animated animate__fadeInUp" style="border-top: 8px solid var(--pats-teal) !important;">
                        <div class="card-header bg-white border-0 pt-5 pb-0 text-center d-block">
                            @php
                                $statusColors = [
                                    'pass' => 'bg-green text-white',
                                    'fail' => 'bg-red text-white',
                                    'absent' => 'bg-secondary text-white',
                                ];
                                $statusColor = $statusColors[$result->result_status] ?? 'bg-warning text-dark';
                            @endphp
                            <div class="badge {{ $statusColor }} fs-3 px-5 py-3 rounded-pill fw-black uppercase tracking-widest shadow-sm">
                                RESULT: {{ $result->result_status }}
                            </div>
                        </div>
                        
                        <div class="card-body p-5">
                            <div class="table-responsive">
                                <table class="table table-vcenter table-borderless mb-0">
                                    <tbody class="fs-3">
                                        <tr class="border-bottom border-dark border-opacity-5">
                                            <td class="text-muted fw-bold small uppercase tracking-wider py-4">Full Name</td>
                                            <td class="fw-black text-dark py-4 text-end">{{ $result->application->candidate->user->full_name }}</td>
                                        </tr>
                                        <tr class="border-bottom border-dark border-opacity-5">
                                            <td class="text-muted fw-bold small uppercase tracking-wider py-4">Position Applied</td>
                                            <td class="fw-bold text-dark py-4 text-end">{{ $result->application->job->title }}</td>
                                        </tr>
                                        <tr class="border-bottom border-dark border-opacity-5">
                                            <td class="text-muted fw-bold small uppercase tracking-wider py-4">Roll Number</td>
                                            <td class="fw-black text-teal py-4 text-end">{{ $result->roll_no }}</td>
                                        </tr>
                                        <tr class="border-bottom border-dark border-opacity-5">
                                            <td class="text-muted fw-bold small uppercase tracking-wider py-4">Score Obtained</td>
                                            <td class="fw-black py-4 text-end">
                                                <span class="display-6 text-dark">{{ $result->score }}</span>
                                                <span class="fs-4 text-muted fw-bold opacity-40">/ {{ $result->total_marks }}</span>
                                            </td>
                                        </tr>
                                        @if($result->percentile)
                                        <tr>
                                            <td class="text-muted fw-bold small uppercase tracking-wider py-4">Merit Percentile</td>
                                            <td class="fw-black text-indigo py-4 text-end fs-1">{{ number_format($result->percentile, 2) }}%</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-5 p-4 bg-teal-lt rounded-4 d-flex align-items-center">
                                <i class="ti ti-info-circle-filled text-teal me-3 fs-1"></i>
                                <div class="small fw-bold text-teal">This is a system-generated result. For official verification, please refer to the stamped result card or contact PATS HQ.</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="card border-0 bg-red-lt text-red p-5 rounded-5 animate__animated animate__shakeX shadow-sm text-center">
                        <div class="mb-3">
                            <i class="ti ti-alert-triangle-filled fs-0"></i>
                        </div>
                        <h3 class="fw-black h2 mb-2">Result Not Found</h3>
                        <p class="fs-4 fw-bold opacity-80">No valid record exists for Roll No: <strong>{{ request('roll_no') }}</strong> and CNIC: <strong>{{ request('cnic') }}</strong>. Please ensure the credentials are correct.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cnicInput = document.getElementById('cnic_mask');
    if (cnicInput) {
        cnicInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,7})(\d{0,1})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    }
});
</script>
@endpush
@endsection
