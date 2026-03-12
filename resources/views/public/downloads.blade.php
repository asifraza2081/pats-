@extends('layouts.public')
@section('title', 'Downloads')
@section('header-title', 'DOWNLOADS')

@section('content')
<div class="card shadow-sm border-0 mb-5">
    <div class="card-body">
        <h2 class="section-header">RESOURCES & FORMS</h2>
        <p class="text-secondary">Access standard forms, sample papers, and candidate instructions from the repository below.</p>
        
        <div class="table-responsive mt-4">
            <table class="table table-vcenter card-table table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>Document Title</th>
                        <th>Category</th>
                        <th>Format</th>
                        <th class="w-1">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>General Instructions for Candidate</td>
                        <td class="text-muted">Guidelines</td>
                        <td><span class="badge bg-red-lt">PDF</span></td>
                        <td><a href="{{ asset('storage/downloads/syllabi.pdf') }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-download me-1"></i> Download</a></td>
                    </tr>
                    <tr>
                        <td>Sample Paper - Data Entry Operator</td>
                        <td class="text-muted">Study Material</td>
                        <td><span class="badge bg-red-lt">PDF</span></td>
                        <td><a href="{{ asset('storage/downloads/sample_papers.pdf') }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-download me-1"></i> Download</a></td>
                    </tr>
                    <tr>
                        <td>Sample Paper - Assistant Manager (Admin)</td>
                        <td class="text-muted">Study Material</td>
                        <td><span class="badge bg-red-lt">PDF</span></td>
                        <td><a href="{{ asset('storage/downloads/sample_papers.pdf') }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-download me-1"></i> Download</a></td>
                    </tr>
                    <tr>
                        <td>Undertaking Form for Errors/Omissions</td>
                        <td class="text-muted">Forms</td>
                        <td><span class="badge bg-blue-lt">DOCX</span></td>
                        <td><a href="#" class="btn btn-sm btn-outline-primary"><i class="ti ti-download me-1"></i> Download</a></td>
                    </tr>
                    <tr>
                        <td>Test Center SOPs & Security Protocols</td>
                        <td class="text-muted">Institutional</td>
                        <td><span class="badge bg-red-lt">PDF</span></td>
                        <td><a href="#" class="btn btn-sm btn-outline-primary"><i class="ti ti-download me-1"></i> Download</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 bg-teal-lt p-4 h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="avatar bg-teal text-white me-3"><i class="ti ti-device-laptop"></i></div>
                <h3 class="mb-0 fw-bold">Online Helpdesk</h3>
            </div>
            <p class="small">Experiencing issues with downloads? Contact our technical team for assistance or alternative delivery.</p>
            <a href="{{ route('contact') }}" class="btn btn-teal text-white w-100 mt-auto" style="background: var(--pats-teal)">GET TECHNICAL SUPPORT</a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 bg-primary-lt p-4 h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="avatar bg-primary text-white me-3"><i class="ti ti-bell"></i></div>
                <h3 class="mb-0 fw-bold">Updates via SMS</h3>
            </div>
            <p class="small">Ensure your profile is complete with a functional mobile number to receive instant download alerts for roll number slips.</p>
            <a href="{{ route('login') }}" class="btn btn-primary w-100 mt-auto">LOGIN TO DASHBOARD</a>
        </div>
    </div>
</div>
@endsection
