@extends('layouts.admin')
@section('title', 'Create Batch')
@section('page-title', 'Create Batch')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card border-0 shadow-sm rounded-4">
<div class="card-body p-4">
    @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('admin.batches.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Project *</label>
                <select name="project_id" class="form-select" required>
                    <option value="">Select project…</option>
                    @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ old('project_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Test Center *</label>
                <select name="center_id" class="form-select" required>
                    <option value="">Select center…</option>
                    @foreach($centers as $c)
                    <option value="{{ $c->id }}" {{ old('center_id')==$c->id?'selected':'' }}>{{ $c->name }} ({{ $c->city }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Batch No. *</label>
                <input type="number" name="batch_number" class="form-control" value="{{ old('batch_number',1) }}" min="1" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Test Date *</label>
                <input type="date" name="test_date" class="form-control" value="{{ old('test_date') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Reporting Time *</label>
                <input type="time" name="reporting_time" class="form-control" value="{{ old('reporting_time','08:00') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Test Start Time *</label>
                <input type="time" name="start_time" class="form-control" value="{{ old('start_time','09:00') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Total Seats *</label>
                <input type="number" name="total_seats" class="form-control" value="{{ old('total_seats') }}" min="1" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Envelope Size *
                    <span class="text-muted fw-normal small">(candidates per envelope)</span>
                </label>
                <input type="number" name="envelope_size" class="form-control" value="{{ old('envelope_size',25) }}" min="10" max="100" required>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-pats px-4">Create Batch</button>
                <a href="{{ route('admin.batches.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
@endsection
