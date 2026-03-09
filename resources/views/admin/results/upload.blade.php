@extends('layouts.admin')
@section('title', 'Upload Results')
@section('page-title', 'Upload Results')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card border-0 shadow-sm rounded-4">
<div class="card-body p-4">
    <div class="alert alert-info small">
        <strong>Accepted file format (CSV or Excel):</strong><br>
        Columns: <code>roll_number, score, total_marks, status</code><br>
        Status values: <code>pass</code>, <code>fail</code>, <code>absent</code>, <code>withheld</code>
    </div>
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('admin.results.upload.post') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Project *</label>
            <select name="project_id" class="form-select" required>
                <option value="">Select project…</option>
                @foreach($projects as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Results File * (CSV or Excel)</label>
            <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
        </div>
        <button type="submit" class="btn btn-pats px-4"><i class="bi bi-upload me-1"></i>Parse &amp; Preview</button>
    </form>
</div>
</div>
</div>
</div>
@endsection
