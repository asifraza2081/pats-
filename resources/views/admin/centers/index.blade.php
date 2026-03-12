@extends('layouts.dashboard')
@section('title', 'Test Centers')
@section('page-title', 'Test Centers')

@section('page-actions')
<a href="{{ route('admin.centers.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-2"></i> Add New Center
</a>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-1 pt-3">
        <h3 class="card-title fw-bold text-primary">All Test Centers</h3>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable table-hover">
            <thead>
                <tr>
                    <th class="w-1">TCID</th>
                    <th>Center Name</th>
                    <th>City</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th class="w-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($centers as $center)
                <tr>
                    <td><span class="badge bg-blue-lt text-blue fw-bold">{{ $center->tcid }}</span></td>
                    <td>
                        <div class="font-weight-medium fw-bold text-body">{{ $center->name }}</div>
                        <div class="text-secondary small">{{ Str::limit($center->address, 50) }}</div>
                    </td>
                    <td>{{ $center->city->name ?? '—' }}</td>
                    <td>
                        <span class="badge bg-info-lt text-info py-1 px-2 fs-5">
                            <i class="ti ti-users me-1"></i> {{ number_format($center->seating_capacity) }}
                        </span>
                    </td>
                    <td>
                        @if($center->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="{{ route('admin.centers.edit', $center) }}" class="btn btn-icon btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Edit Center">
                                <i class="ti ti-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.centers.destroy', $center) }}" onsubmit="return confirm('Delete center?')" class="d-inline-block">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Delete Center">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-5">
                        <div class="empty">
                            <div class="empty-icon text-secondary"><i class="ti ti-building-off fs-1"></i></div>
                            <p class="empty-title">No test centers registered.</p>
                            <div class="empty-action">
                                <a href="{{ route('admin.centers.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-2"></i>Add First Center
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($centers->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $centers->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
