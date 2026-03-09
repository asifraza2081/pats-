@extends('layouts.admin')
@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.users.create') }}" class="btn btn-pats"><i class="bi bi-plus-circle me-1"></i>Add Admin User</a>
</div>
<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr class="small text-muted">
                <th class="px-4 py-3">User</th><th>CNIC</th><th>Role</th><th>Status</th><th>Joined</th><th></th>
            </tr></thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="px-4">
                        <div class="fw-semibold">{{ $user->full_name }}</div>
                        <div class="text-muted small">{{ $user->email }}</div>
                    </td>
                    <td class="small fw-semibold">{{ $user->cnic }}</td>
                    <td>
                        @foreach($user->getRoleNames() as $role)
                        <span class="badge bg-primary text-capitalize">{{ str_replace('_',' ',$role) }}</span>
                        @endforeach
                    </td>
                    <td>
                        @if($user->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="pe-4">
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.users.edit',$user) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('admin.users.toggle',$user) }}">
                                @csrf
                                <button class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="bi bi-{{ $user->is_active ? 'person-dash' : 'person-check' }}"></i>
                                </button>
                            </form>
                            @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('admin.users.destroy',$user) }}" onsubmit="return confirm('Delete user?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No admin users yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())<div class="card-footer bg-white border-0 px-4 pb-3">{{ $users->links() }}</div>@endif
</div>
@endsection
