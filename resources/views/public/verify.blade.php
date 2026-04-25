@extends('layouts.auth')
@section('title', 'Digital Verification')

@section('content')
<div class="text-center py-5">
    <div class="mb-4 animate__animated animate__bounceIn">
        <span class="avatar avatar-xl rounded-circle bg-green-lt shadow-sm ring ring-green ring-opacity-20" style="width: 100px; height: 100px;">
            <i class="ti ti-discount-check-filled text-green" style="font-size: 4rem;"></i>
        </span>
    </div>
    
    <h1 class="display-3 fw-black text-dark mb-1">Authenticity Verified</h1>
    <p class="text-muted fs-3 mb-5">This document is a digitally signed and verified record from the PATS ecosystem.</p>

    <div class="card border-0 shadow-lg rounded-5 overflow-hidden text-start mx-auto" style="max-width: 500px;">
        <div class="card-header bg-dark text-white py-3">
            <h3 class="card-title fw-black small text-uppercase tracking-wider">Candidate & Roll Number Record</h3>
        </div>
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                <div class="avatar avatar-lg rounded me-3 shadow-sm border" style="background-image: url('{{ $rollno->application->candidate->photo_path ? asset('storage/'.$rollno->application->candidate->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($rollno->application->candidate->user->full_name) }}'); width: 60px; height: 60px;"></div>
                <div>
                    <div class="h3 fw-black mb-0">{{ $rollno->application->candidate->user->full_name }}</div>
                    <div class="text-muted small">CNIC: {{ substr($rollno->application->candidate->user->cnic, 0, 5) }}-XXXXXXX-X</div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-6">
                    <label class="text-uppercase tracking-widest small fw-bold opacity-50 mb-1">Roll Number</label>
                    <div class="h4 fw-black text-primary">{{ $rollno->roll_no }}</div>
                </div>
                <div class="col-6">
                    <label class="text-uppercase tracking-widest small fw-bold opacity-50 mb-1">Project</label>
                    <div class="h4 fw-black">{{ $rollno->project->name }}</div>
                </div>
                <div class="col-12">
                    <label class="text-uppercase tracking-widest small fw-bold opacity-50 mb-1">Post Applied For</label>
                    <div class="h4 fw-black text-dark">{{ $rollno->job->title }}</div>
                </div>
                <div class="col-12">
                    <label class="text-uppercase tracking-widest small fw-bold opacity-50 mb-1">Test Center</label>
                    <div class="h4 fw-bold text-secondary">{{ $rollno->center->name }}, {{ $rollno->center->city->name }}</div>
                </div>
            </div>

            @if($rollno->application->result && $rollno->application->result->isPublished())
                <div class="mt-4 p-3 bg-azure-lt rounded-4 border border-azure border-opacity-10 d-flex align-items-center">
                    <div class="me-3"><i class="ti ti-award fs-1 text-azure"></i></div>
                    <div>
                        <div class="small fw-bold text-azure opacity-75 text-uppercase">Final Score</div>
                        <div class="h3 fw-black mb-0 text-azure">{{ $rollno->application->result->score }} / {{ $rollno->application->result->total_marks }}</div>
                    </div>
                </div>
            @endif
        </div>
        <div class="card-footer bg-light border-0 py-3 text-center">
            <div class="small text-muted opacity-75">Verification Timestamp: {{ now()->toDayDateTimeString() }}</div>
        </div>
    </div>

    <div class="mt-5">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="ti ti-home me-2"></i> Back to Homepage
        </a>
    </div>
</div>
@endsection
