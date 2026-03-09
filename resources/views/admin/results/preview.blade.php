@extends('layouts.admin')
@section('title', 'Preview Results')
@section('page-title', 'Preview &amp; Publish Results')

@section('content')
@if(count($warnings))
<div class="alert alert-warning">
    <strong><i class="bi bi-exclamation-triangle me-1"></i>{{ count($warnings) }} Warning(s):</strong>
    <ul class="mb-0 mt-1 small">
        @foreach($warnings as $w)<li>{{ $w }}</li>@endforeach
    </ul>
</div>
@endif

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Preview — {{ count($preview) }} results parsed</h6>
        @if(count($preview))
        <form method="POST" action="{{ route('admin.results.publish', session('result_project_id')) }}" onsubmit="return confirm('Publish all results? This will notify all candidates.')">
            @csrf
            <button type="submit" class="btn btn-success px-4"><i class="bi bi-send-check me-1"></i>Publish Results</button>
        </form>
        @endif
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:.85rem">
            <thead class="table-light"><tr class="text-muted">
                <th class="px-4 py-3">Roll No.</th><th>Candidate</th><th>Post</th><th>Score</th><th>Total</th><th>Status</th>
            </tr></thead>
            <tbody>
                @forelse($preview as $row)
                <tr>
                    <td class="px-4 fw-bold text-primary">{{ $row['roll_number'] }}</td>
                    <td>
                        <div class="fw-semibold">{{ $row['application']['candidate']['user']['full_name'] ?? '—' }}</div>
                    </td>
                    <td>{{ $row['application']['job']['title'] ?? '—' }}</td>
                    <td>{{ $row['score'] }}</td>
                    <td>{{ $row['total_marks'] }}</td>
                    <td>
                        @php $rc=['pass'=>'success','fail'=>'danger','absent'=>'secondary','withheld'=>'warning']; @endphp
                        <span class="badge bg-{{ $rc[$row['result_status']] ?? 'secondary' }} text-capitalize">{{ $row['result_status'] }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No valid rows parsed. Check your file format.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<a href="{{ route('admin.results.upload') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Re-upload</a>
@endsection
