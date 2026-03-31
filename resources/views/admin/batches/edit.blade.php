@extends('layouts.dashboard')
@section('title', 'Edit Session')
@section('page-title', 'Edit Session SESSION-' . $batch->batch_number)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card border-0 shadow-sm rounded-4">
<div class="card-body p-4">
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('admin.batches.update',$batch) }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Project</label>
                <input type="text" class="form-control bg-light" value="{{ $batch->project->name }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Center *</label>
                <select name="center_id" class="form-select tom-select" required>
                    @foreach($centers as $c)
                    <option value="{{ $c->id }}" {{ old('center_id', $batch->center_id) == $c->id ? 'selected' : '' }}>
                        {{ $c->name }} ({{ $c->city?->name ?? 'Unknown City' }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><label class="form-label fw-semibold">Session No. *</label><input type="number" name="batch_number" class="form-control" value="{{ old('batch_number',$batch->batch_number) }}" min="1" required></div>
            <div class="col-md-3"><label class="form-label fw-semibold">Test Date *</label><input type="date" name="test_date" class="form-control" value="{{ old('test_date',$batch->test_date->format('Y-m-d')) }}" required min="{{ now()->toDateString() }}" max="2099-12-31"></div>
            <div class="col-md-3"><label class="form-label fw-semibold">Reporting Time *</label><input type="time" name="reporting_time" class="form-control" value="{{ old('reporting_time',substr($batch->reporting_time,0,5)) }}" required></div>
            <div class="col-md-3"><label class="form-label fw-semibold">Start Time *</label><input type="time" name="start_time" class="form-control" value="{{ old('start_time',substr($batch->start_time,0,5)) }}" required></div>
            <div class="col-md-4"><label class="form-label fw-semibold">Total Seats *</label><input type="number" name="total_seats" class="form-control" value="{{ old('total_seats',$batch->total_seats) }}" min="1" required></div>
            <div class="col-md-4"><label class="form-label fw-semibold">Envelope Size *</label><input type="number" name="envelope_size" class="form-control" value="{{ old('envelope_size',$batch->envelope_size) }}" min="10" required></div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-pats px-4">Save Changes</button>
                <a href="{{ route('admin.batches.show',$batch) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
@endsection
