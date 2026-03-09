@extends('layouts.admin')
@section('title', 'Test Centers')
@section('page-title', 'Test Centers')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.centers.create') }}" class="btn btn-pats"><i class="bi bi-plus-circle me-1"></i>Add Center</a>
</div>
<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr class="small text-muted">
                <th class="px-4 py-3">TCID</th><th>Center Name</th><th>City</th><th>Capacity</th><th></th>
            </tr></thead>
            <tbody>
                @forelse($centers as $center)
                <tr>
                    <td class="px-4 fw-bold text-primary">{{ $center->tcid }}</td>
                    <td>
                        <div class="fw-semibold">{{ $center->name }}</div>
                        <div class="text-muted small">{{ $center->address }}</div>
                    </td>
                    <td class="small">{{ $center->city }}</td>
                    <td><span class="badge bg-info text-dark">{{ $center->total_capacity }} seats</span></td>
                    <td class="pe-4">
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.centers.edit',$center) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('admin.centers.destroy',$center) }}" onsubmit="return confirm('Delete center?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-5">No test centers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
