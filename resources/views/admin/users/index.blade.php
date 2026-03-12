@extends('layouts.dashboard')
@section('title', 'User Management')
@section('page-title', 'Administrative Users')

@section('page-actions')
<a href="{{ route('admin.users.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-2"></i> Add New Administrator
</a>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-1 pt-3">
        <h3 class="card-title fw-bold text-primary">System Administrators</h3>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable table-hover">
            <thead>
                <tr>
                    <th>FullName & Email</th>
                    <th>CNIC Number</th>
                    <th>Assigned Role</th>
                    <th>Status</th>
                    <th>Joined Date</th>
                    <th class="w-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex py-1 align-items-center">
                            <span class="avatar me-2 bg-blue-lt text-blue fw-bold">{{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}</span>
                            <div class="flex-fill">
                                <div class="font-weight-medium fw-bold text-body">{{ $user->full_name }}</div>
                                <div class="text-secondary small">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="text-secondary small fw-semibold">{{ $user->cnic }}</span></td>
                    <td>
                        @foreach($user->getRoleNames() as $role)
                        <span class="badge bg-blue text-blue-fg text-capitalize">{{ str_replace('_',' ',$role) }}</span>
                        @endforeach
                    </td>
                    <td>
                        @if($user->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-danger">Disabled</span>
                        @endif
                    </td>
                    <td><span class="text-secondary small">{{ $user->created_at->format('d M Y') }}</span></td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-icon btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Edit User Account">
                                <i class="ti ti-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="d-inline-block">
                                @csrf
                                <button type="submit" class="btn btn-icon {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} btn-sm" data-bs-toggle="tooltip" title="{{ $user->is_active ? 'Suspend Account' : 'Reactivate Account' }}">
                                    <i class="ti ti-{{ $user->is_active ? 'user-minus' : 'user-check' }}"></i>
                                </button>
                            </form>
                            @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Permanently delete this administrator?')" class="d-inline-block">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Delete Account">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-5">
                        <div class="empty">
                            <div class="empty-icon text-secondary"><i class="ti ti-users-minus fs-1"></i></div>
                            <p class="empty-title">No administrative users found.</p>
                            <div class="empty-action">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-2"></i>Create Admin Account
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
