@extends('layouts.admin')
@section('title', 'Results')
@section('page-title', 'Results Management')

@section('content')
<div class="d-flex gap-2 mb-3">
    <a href="{{ route('admin.results.upload') }}" class="btn btn-pats"><i class="bi bi-cloud-upload me-1"></i>Upload Results</a>
</div>
<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted">
                    <th class="px-4 py-3">Roll No.</th><th>Candidate</th><th>Post</th><th>Score</th><th>%age</th><th>Percentile</th><th>Status</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $result)
                <tr>
                    <td class="px-4 fw-bold text-primary">{{ $result->roll_number }}</td>
                    <td>
                        <div class="small fw-semibold">{{ $result->application->candidate->user->full_name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $result->application->candidate->user->cnic }}</div>
                    </td>
                    <td class="small">{{ Str::limit($result->application->job->title,25) }}</td>
                    <td class="small">{{ $result->score }}/{{ $result->total_marks }}</td>
                    <td class="small fw-semibold {{ $result->percentage >= 50 ? 'text-success':'text-danger' }}">{{ $result->percentage }}%</td>
                    <td class="small">{{ $result->percentile ? number_format($result->percentile,1).'th' : '—' }}</td>
                    <td>
                        @php $rc=['pass'=>'success','fail'=>'danger','absent'=>'secondary','withheld'=>'warning']; @endphp
                        <span class="badge bg-{{ $rc[$result->result_status] ?? 'secondary' }} text-capitalize">{{ $result->result_status }}</span>
                    </td>
                    <td><a href="{{ route('admin.results.show',$result->application) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-5">No results published yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($results->hasPages())<div class="card-footer bg-white border-0 px-4 pb-3">{{ $results->links() }}</div>@endif
</div>
@endsection
