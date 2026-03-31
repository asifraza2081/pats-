@extends('layouts.public')
@section('title', 'Downloads — Prime Assessment & Testing Services')
@section('header-title', 'Resource Center')

@section('content')
<div class="card glass-panel border-0 mb-6 rounded-5 shadow-lg animate__animated animate__fadeIn">
    <div class="card-body p-5 p-md-7">
        <div class="badge bg-teal-lt text-teal px-4 py-2 mb-4 rounded-pill fw-black">DOCUMENT REPOSITORY</div>
        <h2 class="display-5 fw-black text-dark mb-4">Resources & Official Forms</h2>
        <p class="text-secondary fs-3 opacity-80 mb-5">Access standard forms, sample papers, and candidate instructions from our secure digital repository.</p>
        
        <div class="list-group list-group-flush rounded-4 overflow-hidden border border-teal border-opacity-10 mb-5">
            <a href="{{ URL::temporarySignedRoute('public.download.signed', now()->addMinutes(30), ['file' => 'form.pdf']) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center py-4 px-5 border-0">
                <div class="me-4">
                    <div class="bg-primary text-white p-3 rounded-4 shadow-sm">
                        <i class="ti ti-file-text fs-2"></i>
                    </div>
                </div>
                <div>
                    <div class="fw-black text-dark fs-3 mb-1">Generic Application Form</div>
                    <div class="text-muted small fw-black uppercase tracking-widest opacity-60">PDF Document • 1.2 MB</div>
                </div>
                <div class="ms-auto">
                    <i class="ti ti-download text-teal fs-2"></i>
                </div>
            </a>
            <a href="{{ URL::temporarySignedRoute('public.download.signed', now()->addMinutes(30), ['file' => 'instructions.pdf']) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center py-4 px-5 border-0 bg-light">
                <div class="me-4">
                    <div class="bg-indigo text-white p-3 rounded-4 shadow-sm">
                        <i class="ti ti-info-circle fs-2"></i>
                    </div>
                </div>
                <div>
                    <div class="fw-black text-dark fs-3 mb-1">Testing Instructions & Guidelines</div>
                    <div class="text-muted small fw-black uppercase tracking-widest opacity-60">PDF Document • 0.8 MB</div>
                </div>
                <div class="ms-auto">
                    <i class="ti ti-download text-teal fs-2"></i>
                </div>
            </a>
        </div>

        <div class="table-responsive rounded-4 border border-teal border-opacity-10">
            <table class="table table-vcenter table-nowrap mb-0">
                <thead class="bg-teal text-white">
                    <tr>
                        <th class="py-4 ps-5 fw-black uppercase tracking-widest small">Document Title</th>
                        <th class="py-4 fw-black uppercase tracking-widest small">Category</th>
                        <th class="py-4 fw-black uppercase tracking-widest small">Format</th>
                        <th class="py-4 pe-5 w-1 fw-black uppercase tracking-widest small text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="fs-4">
                    <tr class="hover-lift transition-all">
                        <td class="ps-5 py-4 fw-bold text-dark">General Instructions for Candidate</td>
                        <td class="text-muted fw-bold">Guidelines</td>
                        <td><span class="badge bg-red-lt text-red border-0 rounded-pill px-3 fw-black uppercase">PDF</span></td>
                        <td class="pe-5 text-center"><a href="{{ URL::temporarySignedRoute('public.download.signed', now()->addMinutes(30), ['file' => 'syllabi.pdf']) }}" class="btn btn-teal btn-sm rounded-pill px-4 fw-black">DOWNLOAD <i class="ti ti-download ms-1"></i></a></td>
                    </tr>
                    <tr class="hover-lift transition-all bg-light bg-opacity-30">
                        <td class="ps-5 py-4 fw-bold text-dark">Sample Paper - Data Entry Operator</td>
                        <td class="text-muted fw-bold">Study Material</td>
                        <td><span class="badge bg-red-lt text-red border-0 rounded-pill px-3 fw-black uppercase">PDF</span></td>
                        <td class="pe-5 text-center"><a href="{{ URL::temporarySignedRoute('public.download.signed', now()->addMinutes(30), ['file' => 'sample_papers.pdf']) }}" class="btn btn-teal btn-sm rounded-pill px-4 fw-black">DOWNLOAD <i class="ti ti-download ms-1"></i></a></td>
                    </tr>
                    <tr class="hover-lift transition-all">
                        <td class="ps-5 py-4 fw-bold text-dark">Sample Paper - Assistant Manager (Admin)</td>
                        <td class="text-muted fw-bold">Study Material</td>
                        <td><span class="badge bg-red-lt text-red border-0 rounded-pill px-3 fw-black uppercase">PDF</span></td>
                        <td class="pe-5 text-center"><a href="{{ URL::temporarySignedRoute('public.download.signed', now()->addMinutes(30), ['file' => 'sample_papers.pdf']) }}" class="btn btn-teal btn-sm rounded-pill px-4 fw-black">DOWNLOAD <i class="ti ti-download ms-1"></i></a></td>
                    </tr>
                    <tr class="hover-lift transition-all bg-light bg-opacity-30">
                        <td class="ps-5 py-4 fw-bold text-dark">Undertaking Form for Errors/Omissions</td>
                        <td class="text-muted fw-bold">Forms</td>
                        <td><span class="badge bg-blue-lt text-blue border-0 rounded-pill px-3 fw-black uppercase">DOCX</span></td>
                        <td class="pe-5 text-center"><a href="{{ URL::temporarySignedRoute('public.download.signed', now()->addMinutes(30), ['file' => 'undertaking.docx']) }}" class="btn btn-teal btn-sm rounded-pill px-4 fw-black">DOWNLOAD <i class="ti ti-download ms-1"></i></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row g-5">
    <div class="col-md-6">
        <div class="card glass-panel border-0 p-5 h-100 rounded-5 hover-lift shadow-sm">
            <div class="d-flex align-items-center mb-4">
                <div class="avatar avatar-lg bg-teal text-white rounded-4 shadow-teal-30 me-4"><i class="ti ti-device-laptop fs-1"></i></div>
                <h3 class="mb-0 fw-black text-dark display-6" style="font-size: 1.5rem;">Online Helpdesk</h3>
            </div>
            <p class="fs-4 text-secondary mb-5 opacity-80 fw-medium">Experiencing issues with downloads? Contact our technical team for assistance or alternative delivery.</p>
            <a href="{{ route('contact') }}" class="btn btn-teal btn-lg text-white w-100 mt-auto rounded-pill fw-black shadow-teal-30 border-0 py-3" style="background: var(--pats-teal)">GET TECHNICAL SUPPORT</a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card glass-panel border-0 p-5 h-100 rounded-5 hover-lift shadow-sm">
            <div class="d-flex align-items-center mb-4">
                <div class="avatar avatar-lg bg-indigo text-white rounded-4 shadow-sm me-4"><i class="ti ti-bell fs-1"></i></div>
                <h3 class="mb-0 fw-black text-dark display-6" style="font-size: 1.5rem;">Updates via SMS</h3>
            </div>
            <p class="fs-4 text-secondary mb-5 opacity-80 fw-medium">Ensure your profile is complete with a functional mobile number to receive instant download alerts for roll number slips.</p>
            <a href="{{ route('login') }}" class="btn btn-indigo btn-lg w-100 mt-auto rounded-pill fw-black shadow-sm border-0 py-3">LOGIN TO DASHBOARD</a>
        </div>
    </div>
</div>
@endsection
