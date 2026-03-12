@extends('layouts.dashboard')
@section('title', 'Manage Projects')
@section('page-title', 'Project Directory')

@section('page-actions')
<a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-2 fs-2"></i> Create New Project
</a>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-1 pt-3">
        <h3 class="card-title fw-bold text-primary">All Projects</h3>
    </div>
    
    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable table-hover">
            <thead>
                <tr>
                    <th>Project Name & Details</th>
                    <th>Posts</th>
                    <th>Status Phase</th>
                    <th>Timeline</th>
                    <th class="w-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                <tr>
                    <td>
                        <div class="d-flex py-1 align-items-center">
                            <span class="avatar me-2 bg-blue-lt text-uppercase">{{ substr($project->name, 0, 2) }}</span>
                            <div class="flex-fill">
                                <div class="font-weight-medium text-body d-block fw-bold">{{ $project->name }}</div>
                                <div class="text-secondary small">{{ $project->org_name }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-blue-lt text-blue py-1 px-2 fs-5"><i class="ti ti-briefcase me-1"></i> {{ $project->jobs_count }}</span>
                    </td>
                    <td>
                        @php 
                            $sc = [
                                'draft'=>'secondary',
                                'open'=>'success',
                                'closed'=>'dark',
                                'result_declared'=>'info'
                            ]; 
                        @endphp
                        <span class="badge bg-{{ $sc[$project->status] ?? 'secondary' }} text-{{ $sc[$project->status] ?? 'secondary' }}-fg text-capitalize">
                            {{ str_replace('_', ' ', $project->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="small">
                            <div><span class="text-secondary">Opens:</span> <span class="fw-semibold">{{ $project->open_date?->format('d M Y') ?? '—' }}</span></div>
                            <div class="mt-1"><span class="text-secondary">Closes:</span> <span class="fw-semibold">{{ $project->close_date?->format('d M Y') ?? '—' }}</span></div>
                        </div>
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-icon btn-outline-primary" data-bs-toggle="tooltip" title="View Details">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-icon btn-outline-secondary" data-bs-toggle="tooltip" title="Edit Project">
                                <i class="ti ti-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('WARNING: Are you sure you want to delete this project permanently? This action cannot be undone.')" class="d-inline-block">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-outline-danger" data-bs-toggle="tooltip" title="Delete Project">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-0">
                        <x-empty-state 
                            icon="ti ti-folder-off" 
                            title="No projects organized yet" 
                            subtitle="Organize your first recruitment project to start accepting applications."
                            actionUrl="{{ route('admin.projects.create') }}"
                            actionLabel="Create Your First Project"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($projects->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $projects->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
