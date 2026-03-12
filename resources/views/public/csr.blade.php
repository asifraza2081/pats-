@extends('layouts.public')
@section('title', 'Corporate Social Responsibility')
@section('header-title', 'CSR INITIATIVES')

@section('content')
<div class="row g-5 align-items-center mb-6">
    <div class="col-lg-6">
        <h2 class="section-header">BEYOND TESTING</h2>
        <p class="fs-3 text-secondary lh-lg">
            At PATS, we believe our responsibility extends beyond professional assessments. We are dedicated to empowering the youth of Pakistan and contributing to the national educational landscape through targeted social initiatives.
        </p>
    </div>
    <div class="col-lg-6 text-center">
        <div class="bg-teal-lt p-5 rounded-4 border">
            <i class="ti ti-heart-handshake text-teal display-3 mb-3"></i>
            <h3 class="fw-bold">Giving Back to the Nation</h3>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card card-nts h-100 text-center p-4">
            <div class="mb-3"><span class="avatar avatar-lg bg-blue-lt text-blue rounded"><i class="ti ti-school fs-1"></i></span></div>
            <h3 class="fw-bold">Scholarship Support</h3>
            <p class="small text-muted">Facilitating needs-blind admissions and scholarship screening for underprivileged students in remote regions.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-nts h-100 text-center p-4">
            <div class="mb-3"><span class="avatar avatar-lg bg-green-lt text-green rounded"><i class="ti ti-leaf fs-1"></i></span></div>
            <h3 class="fw-bold">Digital Literacy</h3>
            <p class="small text-muted">Providing free digital assessment readiness workshops in public sector colleges to bridge the digital divide.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-nts h-100 text-center p-4">
            <div class="mb-3"><span class="avatar avatar-lg bg-orange-lt text-orange rounded"><i class="ti ti-users fs-1"></i></span></div>
            <h3 class="fw-bold">Inclusion Programs</h3>
            <p class="small text-muted">Developing specialized assessment protocols and support systems for candidates with physical disabilities.</p>
        </div>
    </div>
</div>

<div class="card border-0 bg-light p-5 text-center">
    <h3 class="fw-bold mb-3">PATS Foundation</h3>
    <p class="text-secondary mx-auto" style="max-width: 700px;">Our dedicated foundation works tirelessly with partner NGOs to ensure that financial barriers never prevent a talented Pakistani from reaching their true potential.</p>
</div>
@endsection
