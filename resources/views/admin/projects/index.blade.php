@extends('layouts.admin')
@section('title', 'Projects')
@section('page-title', 'Projects')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-pats">
        <i class="bi bi-plus-circle me-1"></i>New Project
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-muted">
                    <th class="px-4 py-3">Project</th>
                    <th>Organisation</th>
                    <th>Jobs</th>
                    <th>Open Date</th>
                    <th>Close Date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                <tr>
                    <td class="px-4 fw-semibold">{{ $project->name }}</td>
                    <td class="text-muted small">{{ $project->org_name }}</td>
                    <td><span class="badge bg-info text-dark">{{ $project->jobs_count }}</span></td>
                    <td class="small">{{ $project->open_date?->format('d M Y') ?? '—' }}</td>
                    <td class="small">{{ $project->close_date?->format('d M Y') ?? '—' }}</td>
                    <td>
                        @php $sc=['draft'=>'secondary','open'=>'success','closed'=>'dark','result_declared'=>'primary']; @endphp
                        <span class="badge bg-{{ $sc[$project->status] ?? 'secondary' }} text-capitalize">{{ str_replace('_',' ',$project->status) }}</span>
                    </td>
                    <td class="pe-4">
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.projects.show',$project) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.projects.edit',$project) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('admin.projects.destroy',$project) }}" onsubmit="return confirm('Delete this project?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No projects yet. <a href="{{ route('admin.projects.create') }}">Create one</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($projects->hasPages())
    <div class="card-footer bg-white border-0 pt-0 px-4 pb-3">{{ $projects->links() }}</div>
    @endif
</div>
@endsection
