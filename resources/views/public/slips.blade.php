@extends('layouts.public')
@section('title', 'Download Roll Number Slip — Prime Assessment & Testing Services')
@section('header-title', 'Admit Cards')

@section('content')
<div class="container-xl py-6">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-6 animate__animated animate__fadeIn">
                <div class="avatar avatar-xl bg-grad-accent text-white rounded-circle shadow-teal-30 mb-4 mx-auto">
                    <i class="ti ti-id-badge-2 fs-0"></i>
                </div>
                <h2 class="display-4 fw-black text-dark mb-2">Download Admit Card</h2>
                <p class="text-muted fs-3 opacity-80">Enter your CNIC or Roll Number to download your official examination slip.</p>
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
                    <form method="GET" action="{{ route('public.slips.search') }}">
                        <div class="row g-4 mb-4 justify-content-center">
                            <div class="col-md-10">
                                <label class="form-label fw-black text-dark opacity-60 small uppercase tracking-widest mb-3">CNIC or Roll Number</label>
                                <div class="input-group input-group-flat shadow-none rounded-4 overflow-hidden border border-teal border-opacity-20">
                                    <span class="input-group-text bg-white border-0 ps-3">
                                        <i class="ti ti-id text-teal fs-3"></i>
                                    </span>
                                    <input type="text" name="identifier" class="form-control form-control-lg border-0 bg-white fs-3 py-3 shadow-none" placeholder="e.g. 102030 or XXXXX-XXXXXXX-X" value="{{ request('identifier') }}" autofocus required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-teal w-100 py-4 fs-2 fw-black rounded-pill border-0 shadow-teal-30">
                            <i class="ti ti-search me-3"></i> FIND EXACT SLIP
                        </button>
                    </form>
                </div>
                <div class="bg-light py-3 px-5 border-top border-dark border-opacity-5">
                    <div class="d-flex align-items-center justify-content-center gap-4 small fw-bold text-muted opacity-60">
                        <span><i class="ti ti-printer me-1"></i> PRINT READY PDF</span>
                        <span><i class="ti ti-shield-check me-1"></i> SECURE VERIFICATION</span>
                    </div>
                </div>
            </div>

            @if(isset($slips))
                @if($slips->isNotEmpty())
                    @foreach($slips as $slip)
                    <div class="card border-0 shadow-2xl mb-5 rounded-5 overflow-hidden animate__animated animate__fadeInUp" style="border-top: 8px solid var(--pats-teal) !important;">
                        <div class="card-header bg-white border-0 pt-5 pb-0 text-center d-block">
                            <div class="badge bg-green text-white fs-3 px-5 py-3 rounded-pill fw-black uppercase tracking-widest shadow-sm">
                                ADMIT CARD AVAILABLE
                            </div>
                        </div>
                        
                        <div class="card-body p-5">
                            <div class="table-responsive">
                                <table class="table table-vcenter table-borderless mb-0">
                                    <tbody class="fs-3">
                                        <tr class="border-bottom border-dark border-opacity-5">
                                            <td class="text-muted fw-bold small uppercase tracking-wider py-4">Full Name</td>
                                            <td class="fw-black text-dark py-4 text-end">{{ $slip->application->candidate->user->full_name }}</td>
                                        </tr>
                                        <tr class="border-bottom border-dark border-opacity-5">
                                            <td class="text-muted fw-bold small uppercase tracking-wider py-4">Position Applied</td>
                                            <td class="fw-bold text-dark py-4 text-end">{{ $slip->application->job->title }}</td>
                                        </tr>
                                        <tr class="border-bottom border-dark border-opacity-5">
                                            <td class="text-muted fw-bold small uppercase tracking-wider py-4">Roll Number</td>
                                            <td class="fw-black text-teal py-4 text-end">{{ $slip->roll_no }}</td>
                                        </tr>
                                        <tr class="border-bottom border-dark border-opacity-5">
                                            <td class="text-muted fw-bold small uppercase tracking-wider py-4">Test Date & Time</td>
                                            <td class="fw-black py-4 text-end">
                                                <div class="text-dark">{{ $slip->batch->test_date ? $slip->batch->test_date->format('d M Y') : 'TBD' }}</div>
                                                <div class="text-muted fs-4">{{ $slip->batch->reporting_time ? \Carbon\Carbon::parse($slip->batch->reporting_time)->format('h:i A') : 'TBD' }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-bold small uppercase tracking-wider py-4">Test Center</td>
                                            <td class="fw-bold text-dark py-4 text-end">{{ $slip->center->name ?? 'TBD' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-5 text-center">
                                <a href="{{ route('public.slips.download', $slip->roll_no) }}" class="btn btn-dark btn-lg w-100 py-3 rounded-4 fw-black fs-2">
                                    <i class="ti ti-download me-2"></i> DOWNLOAD ADMIT CARD PDF
                                </a>
                            </div>

                            <div class="mt-4 p-4 bg-yellow-lt rounded-4 d-flex align-items-center">
                                <i class="ti ti-alert-triangle-filled text-yellow me-3 fs-1"></i>
                                <div class="small fw-bold text-dark">Important: You must bring a printed copy of this slip along with your original CNIC to be allowed entry into the test center.</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
