@extends('layouts.dashboard')
@section('title', 'Import Test Results')
@section('page-title', 'Results Upload')

@section('page-actions')
<a href="{{ route('admin.results.index') }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Results
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">Upload Score Sheet</h3>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger bg-red-lt border-0 mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="alert alert-info bg-blue-lt border-0 mb-4">
                    <div class="d-flex">
                        <div><i class="ti ti-info-circle fs-2 me-2"></i></div>
                        <div>
                            <div class="fw-bold">CSV/Excel Requirements</div>
                            <p class="text-secondary small mb-2">The file must contain the following headers (case-insensitive):</p>
                            <code class="d-block bg-white p-2 border rounded text-dark fw-bold mb-0">roll_number, score, total_marks, status</code>
                            <p class="text-secondary small mt-2 mb-0">Status options: `pass`, `fail`, `absent`, `withheld`</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.results.upload.post') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label required">Target Project</label>
                        <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                            <option value="">Select project...</option>
                            @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Scanned Answer Sheets (Optional)</label>
                        <input type="file" name="scans[]" class="form-control" multiple accept="image/*,.pdf">
                        <div class="form-hint">Tip: Name files as roll_number (e.g., 764420.jpg) for auto-matching.</div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="ti ti-upload me-2"></i> Analyze & Preview
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4 card bg-light-lt border-0 border-start border-yellow border-4">
           <div class="card-body py-3">
                <h4 class="fw-bold mb-2"><i class="ti ti-alert-triangle me-2"></i> Important Note</h4>
                <p class="text-secondary small mb-0">Uploading a file will NOT immediately notify candidates. You will have a chance to preview the matched records and resolve warnings before publishing.</p>
           </div>
        </div>
    </div>
</div>
@endsection
