@extends('layouts.public')
@section('title', 'Procurement')
@section('header-title', 'TENDERS & PROCUREMENT')

@section('content')
<div class="card shadow-sm border-0 mb-5">
    <div class="card-body">
        <h2 class="section-header">ACTIVE TENDERS</h2>
        <p class="text-secondary">PATS invites sealed bids from reputable firms/suppliers for the following requirements. Bidding is conducted under PPRA Rules.</p>
        
        <div class="table-responsive mt-4">
            <table class="table table-vcenter card-table table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>Tender No.</th>
                        <th>Description of Requirement</th>
                        <th>Closing Date</th>
                        <th class="w-1">Documents</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PATS/TEN/24/08</td>
                        <td class="fw-bold">Security Printing of Optical Answer Sheets (OMR)</td>
                        <td>25 Mar, 2024</td>
                        <td><a href="#" class="btn btn-sm btn-outline-danger"><i class="ti ti-file-type-pdf me-1"></i> RFP</a></td>
                    </tr>
                    <tr>
                        <td>PATS/TEN/24/09</td>
                        <td class="fw-bold">Procurement of IT Equipment (Laptops & Servers)</td>
                        <td>02 Apr, 2024</td>
                        <td><a href="#" class="btn btn-sm btn-outline-danger"><i class="ti ti-file-type-pdf me-1"></i> RFP</a></td>
                    </tr>
                    <tr>
                        <td>PATS/ADM/24/01</td>
                        <td class="fw-bold">Provision of Security Guard Services (Headquarters)</td>
                        <td>10 Apr, 2024</td>
                        <td><a href="#" class="btn btn-sm btn-outline-danger"><i class="ti ti-file-type-pdf me-1"></i> RFP</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 bg-dark text-white p-5">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-3">Become a Registered Vendor</h2>
            <p class="opacity-75 mb-0">We maintain a pool of pre-qualified vendors for recurring requirements. If you are a specialized service provider in logistics, printing, or technology, submit your profile for evaluation.</p>
        </div>
        <div class="col-lg-4 text-center text-lg-end mt-4 mt-lg-0">
            <a href="{{ route('contact') }}" class="btn btn-light btn-lg px-5 fw-bold text-dark">VENDOR REGISTRATION</a>
        </div>
    </div>
</div>
@endsection
