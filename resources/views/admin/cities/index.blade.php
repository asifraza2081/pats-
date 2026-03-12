@extends('layouts.dashboard')
@section('title', 'City Management')
@section('page-title', 'Cities & Locations')

@section('page-actions')
<a href="{{ route('admin.cities.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-2"></i> Add New City
</a>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-1 pt-3">
        <h3 class="card-title fw-bold text-primary">Registered Cities</h3>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable table-hover">
            <thead>
                <tr>
                    <th class="w-1">ID</th>
                    <th>City Name</th>
                    <th>Province</th>
                    <th>Test Center Status</th>
                    <th>Centers Count</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($cities as $city)
                <tr>
                    <td><span class="text-secondary fw-bold">#{{ $city->id }}</span></td>
                    <td><div class="font-weight-medium text-body fw-bold">{{ $city->name }}</div></td>
                    <td>{{ $city->province }}</td>
                    <td>
                        @if($city->is_test_center)
                        <span class="badge bg-success-lt text-success"><i class="ti ti-check me-1"></i> Active Venue</span>
                        @else
                        <span class="badge bg-secondary-lt text-secondary">General Only</span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold">{{ $city->test_centers_count }}</span>
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="{{ route('admin.cities.edit', $city) }}" class="btn btn-outline-primary btn-icon btn-sm" title="Edit City">
                                <i class="ti ti-edit"></i>
                            </a>
                            <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" onsubmit="return confirm('Are you sure?')" class="d-inline">
                                @propto
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-icon btn-sm" title="Delete City">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-5 italic">No cities found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cities->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $cities->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
