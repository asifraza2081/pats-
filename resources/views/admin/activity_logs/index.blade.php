@extends('layouts.dashboard')
@section('title', 'Activity Logs')
@section('page-title', 'System Audit Trail')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-1 pt-3">
        <h3 class="card-title fw-bold">Admin Activity Logs</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Resource</th>
                    <th>IP Address</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-secondary">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>
                        <div class="d-flex py-1 align-items-center">
                            <span class="avatar me-2" style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode($log->user?->full_name ?? 'Deleted User') }}')"></span>
                            <div class="flex-fill">
                                <div class="font-weight-medium">{{ $log->user?->full_name ?? 'Deleted User' }}</div>
                                <div class="text-secondary"><a href="#" class="text-reset">{{ $log->user?->email ?? '—' }}</a></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $badgeClass = match($log->action) {
                                'allocate_seats'   => 'bg-blue',
                                'publish_slips'    => 'bg-green',
                                'publish_results'  => 'bg-purple',
                                'login'            => 'bg-azure',
                                'create'           => 'bg-success',
                                'update'           => 'bg-warning',
                                'delete'           => 'bg-danger',
                                default            => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }} text-white">{{ strtoupper(str_replace('_', ' ', $log->action)) }}</span>
                    </td>
                    <td class="text-secondary">
                        {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                    </td>
                    <td class="text-secondary">{{ $log->ip_address }}</td>
                    <td>
                        <button class="btn btn-ghost-primary btn-sm" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                            View Details
                        </button>
                        
                        <!-- Modal -->
                        <div class="modal modal-blur fade" id="logModal{{ $log->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Log Detail #{{ $log->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted">Action</label>
                                                <div class="fw-bold">{{ strtoupper($log->action) }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted">User Agent</label>
                                                <div class="small">{{ $log->user_agent }}</div>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <label class="form-label text-muted">Payload (Context Data)</label>
                                                <pre class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">{{ json_encode($log->payload, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No activity logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
