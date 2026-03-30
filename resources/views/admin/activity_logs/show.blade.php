@extends('layouts.dashboard')
@section('title', 'Activity Log Detail')
@section('page-title', 'Log Entry #' . $activityLog->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="mb-3">
            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-2"></i> Back to Audit Trail
            </a>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title fw-bold">Verification Details</h3>
            </div>
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Event Timestamp</div>
                        <div class="datagrid-content fw-bold">{{ $activityLog->created_at->format('d M Y, h:i:s A') }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Action Performed</div>
                        <div class="datagrid-content">
                            @php
                                $badgeClass = match($activityLog->action) {
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
                            <span class="badge {{ $badgeClass }} text-white">{{ strtoupper(str_replace('_', ' ', $activityLog->action)) }}</span>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Initiated By</div>
                        <div class="datagrid-content">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2" style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode($activityLog->user?->full_name ?? 'System') }}')"></span>
                                {{ $activityLog->user?->full_name ?? 'System Process' }}
                            </div>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">IP Address</div>
                        <div class="datagrid-content">{{ $activityLog->ip_address ?? '—' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Resource Type</div>
                        <div class="datagrid-content text-monospace">{{ $activityLog->model_type ?? 'N/A' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Resource ID</div>
                        <div class="datagrid-content fw-bold text-primary">#{{ $activityLog->model_id ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title fw-bold"><i class="ti ti-history me-2"></i> Event Payload & Context</h3>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label text-muted small">Payload Data (JSON format)</label>
                    <div class="bg-dark p-3 rounded-3 text-light">
                        <pre class="m-0 text-pats-gold" style="white-space: pre-wrap; word-break: break-all;">{{ json_encode($activityLog->payload, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-muted small">Browser / User Agent</label>
                        <div class="bg-light p-2 rounded border small text-muted font-monospace">
                            {{ $activityLog->user_agent ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0 pb-4">
                <div class="alert alert-info border-0 shadow-none mb-0 d-flex align-items-center">
                    <i class="ti ti-info-circle fs-2 me-2"></i>
                    <div>This log entry is immutable and serves as part of the official PATS system audit trail.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
