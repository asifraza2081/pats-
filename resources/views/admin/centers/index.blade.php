@extends('layouts.dashboard')
@section('title', 'Test Centers')
@section('page-title', 'Test Centers Management')

@section('page-actions')
<a href="{{ route('admin.centers.create') }}" class="btn btn-primary shadow-sm fw-bold">
    <i class="ti ti-plus me-2"></i> Add New Center
</a>
@endsection

@section('content')
<div class="row row-cards">
    @forelse($cities as $city)
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
            <div class="card-header border-bottom bg-light bg-opacity-50 pb-2 pt-3">
                <h3 class="card-title fw-black text-navy"><i class="ti ti-map-pin text-teal me-2"></i> {{ $city->name }}</h3>
                <div class="card-actions">
                    <span class="badge bg-indigo-lt fw-bold">{{ $city->testCenters->count() }} Centers</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap table-hover">
                    <thead>
                        <tr class="bg-navy bg-opacity-10 text-navy">
                            <th class="w-1"></th>
                            <th class="w-1">TCID</th>
                            <th>Center Name</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th class="w-1 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="sortable-list" data-city="{{ $city->id }}">
                        @foreach($city->testCenters as $center)
                        <tr data-id="{{ $center->id }}" class="bg-white">
                            <td class="sort-handle cursor-move text-teal opacity-50 px-2 text-center" style="width: 40px;">
                                <i class="ti ti-grip-vertical fs-3"></i>
                            </td>
                            <td><span class="badge bg-blue-lt text-blue fw-bold">{{ $center->tcid }}</span></td>
                            <td>
                                <div class="font-weight-medium fw-bold text-body">{{ $center->name }}</div>
                                <div class="text-secondary small text-wrap" style="max-width:300px;">{{ Str::limit($center->address, 60) }}</div>
                            </td>
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
                            <td class="text-end">
                                <div class="btn-list flex-nowrap justify-content-end">
                                    <a href="{{ route('admin.centers.edit', $center) }}" class="btn btn-icon btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Edit Center">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.centers.destroy', $center) }}" onsubmit="return confirmAction('Delete this test center definitively?')" class="d-inline-block">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Delete Center">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="empty">
                    <div class="empty-icon text-secondary"><i class="ti ti-building-off fs-1"></i></div>
                    <p class="empty-title">No test centers registered.</p>
                    <div class="empty-action">
                        <a href="{{ route('admin.centers.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Add First Center
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Sortable === 'undefined') {
        console.warn('SortableJS could not be loaded.');
        return;
    }

    const lists = document.querySelectorAll('.sortable-list');
    lists.forEach(list => {
        new Sortable(list, {
            handle: '.sort-handle',
            animation: 150,
            ghostClass: 'bg-light',
            onEnd: function (evt) {
                const itemEl = evt.item; 
                const cityId = itemEl.closest('tbody').dataset.city;
                const rows = itemEl.closest('tbody').querySelectorAll('tr');
                
                let order = [];
                rows.forEach(row => {
                    order.push(row.dataset.id);
                });

                // Update Backend
                fetch('{{ route("admin.centers.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: order })
                }).then(res => res.json())
                .then(data => {
                    if (data.success) {
                        toastr.success('Priority order updated for ' + order.length + ' centers.');
                    } else {
                        toastr.error('Failed to update priority order.');
                    }
                }).catch(err => {
                    toastr.error('Error contacting server.');
                });
            }
        });
    });
});
</script>
@endpush
