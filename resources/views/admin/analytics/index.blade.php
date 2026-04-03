@extends('layouts.dashboard')
@section('title', 'Candidate Funnel Analytics')
@section('page-title', 'Active Recruitment Funnels')

@section('page-actions')
@endsection

@section('content')
<div class="row row-cards">
    <!-- Filters Column -->
    <div class="col-lg-3">
        <div class="card shadow-sm border-0 mb-3 sticky-top" style="top: 20px;">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary"><i class="ti ti-briefcase me-2"></i> Select Target Job</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.analytics.index') }}">
                    <div class="mb-3">
                        <label class="form-label">Active Job Posts</label>
                        <select name="job_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Choose Job --</option>
                            <option value="all" {{ ($selectedJob && $selectedJob->id == 'all') ? 'selected' : '' }}>-- ALL ACTIVE JOBS --</option>
                            @foreach($jobs as $job)
                                <option value="{{ $job->id }}" {{ ($selectedJob && $selectedJob->id == $job->id) ? 'selected' : '' }}>
                                    {{ $job->title }} ({{ $job->project->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Column -->
    <div class="col-lg-9">
        @if($selectedJob)
            <!-- High Level Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card card-sm border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-primary text-white avatar"><i class="ti ti-users"></i></span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Applied Total</div>
                                    <div class="text-secondary fw-bold fs-3">{{ number_format($analytics['total_applied']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-sm border-0 shadow-sm rounded-4 border-success border-bottom border-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-success text-white avatar"><i class="ti ti-check"></i></span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Paid & Verified</div>
                                    <div class="text-success fw-bold fs-3">{{ number_format($analytics['paid_eligible']) }} <small class="fw-normal text-muted">Ready</small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-sm border-0 shadow-sm rounded-4 border-warning border-bottom border-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-yellow text-white avatar"><i class="ti ti-clock"></i></span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Pending Payments</div>
                                    <div class="text-yellow fw-bold fs-3">{{ number_format($analytics['unpaid_ineligible']) }} <small class="fw-normal text-muted">Waiting</small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deep Breakdown -->
            @foreach($groupedCandidates as $city => $paymentGroups)
                <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
                    <div class="card-header border-bottom bg-light bg-opacity-50 pb-2 pt-3 d-flex justify-content-between">
                        <h3 class="card-title fw-bold text-navy"><i class="ti ti-map-pin text-teal me-2"></i> {{ $city }}</h3>
                        <span class="badge bg-indigo-lt px-3 py-2 fw-bold fs-5 shadow-sm">{{ $paymentGroups['Paid & Eligible']->count() + $paymentGroups['Pending Payment']->count() }} Applicants</span>
                    </div>
                    
                    <div class="accordion accordion-flush" id="accordion-city-{{ Str::slug($city) }}">
                        @foreach(['Paid & Eligible' => 'success', 'Pending Payment' => 'warning'] as $status => $colorClass)
                        @if($paymentGroups[$status]->count() > 0)
                        <div class="accordion-item shadow-none border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-3 fw-medium text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ Str::slug($city . '-' . $status) }}">
                                    <div class="d-flex w-100 justify-content-between align-items-center me-3">
                                        <span>
                                            <span class="status-indicator status-{{ $colorClass }} status-indicator-animated me-2"></span>
                                            {{ $status }}
                                        </span>
                                        <span class="badge bg-{{ $colorClass }}-lt px-2">{{ $paymentGroups[$status]->count() }}</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse-{{ Str::slug($city . '-' . $status) }}" class="accordion-collapse collapse" data-bs-parent="#accordion-city-{{ Str::slug($city) }}">
                                <div class="accordion-body p-0 pt-0">
                                    <form action="{{ route('admin.applications.bulk-mark-paid') }}" method="POST">
                                        @csrf
                                        @if($status === 'Pending Payment')
                                        <div class="p-2 bg-light border-bottom d-flex justify-content-between align-items-center">
                                            <span class="small text-muted fw-bold ps-2">Selected candidates will be moved to the verified pool.</span>
                                            <button type="submit" class="btn btn-success btn-sm px-3 shadow-sm rounded-pill">
                                                <i class="ti ti-check me-1"></i> Bulk Mark Paid
                                            </button>
                                        </div>
                                        @endif
                                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                            <table class="table table-vcenter table-hover card-table table-sm">
                                                <thead class="sticky-top bg-white">
                                                    <tr>
                                                        @if($status === 'Pending Payment')
                                                        <th class="w-1"></th>
                                                        @endif
                                                        <th class="w-1">Sr.</th>
                                                        <th>Applicant Name</th>
                                                        <th>CNIC</th>
                                                        <th>Contact</th>
                                                        <th>Submission Date</th>
                                                        @if($status === 'Pending Payment')
                                                        <th class="w-1">Action</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($paymentGroups[$status] as $index => $app)
                                                    <tr>
                                                        @if($status === 'Pending Payment')
                                                        <td><input type="checkbox" name="application_ids[]" value="{{ $app->id }}" class="form-check-input check-{{ Str::slug($city . '-' . $status) }}"></td>
                                                        @endif
                                                        <td class="text-secondary small text-center">{{ $index + 1 }}</td>
                                                        <td class="fw-bold">
                                                            <a href="{{ route('admin.applications.show', $app->id) }}" target="_blank" class="text-reset">
                                                                {{ $app->candidate->user->full_name }}
                                                            </a>
                                                        </td>
                                                        <td class="text-secondary font-mono">{{ $app->candidate->user->cnic }}</td>
                                                        <td class="text-secondary">{{ $app->candidate->user->phone }}</td>
                                                        <td class="text-secondary small">{{ $app->created_at->format('M d, Y') }}</td>
                                                        @if($status === 'Pending Payment')
                                                        <td>
                                                            <button type="button" class="btn btn-icon btn-sm btn-ghost-success border-0" 
                                                                    onclick="event.preventDefault(); ajaxMarkPaid('{{ route('admin.applications.mark-paid', $app->id) }}', this)"
                                                                    data-bs-toggle="tooltip" title="Mark as Paid">
                                                                <i class="ti ti-currency-dollar"></i>
                                                            </button>
                                                        </td>
                                                        @endif
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-5">
                <div class="empty bg-transparent">
                    <div class="empty-icon text-primary mb-4">
                        <i class="ti ti-chart-pie" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="empty-title mb-3">Candidate Intelligence Tracking</h2>
                    <p class="empty-subtitle text-secondary mb-4 mx-auto" style="max-width: 600px;">
                        Select a target active job position on the left panel. The system will aggregate the entire pipeline identifying who has paid, who is verified, and physically exactly what test center they map to out of the candidate pool.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@push('scripts')
<script>
function ajaxMarkPaid(url, button) {
    if(!confirm('Are you sure you want to mark this candidate as paid?')) return;
    
    // Disable button to prevent double clicks
    button.disabled = true;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="ti ti-loader ti-spin"></i>';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if(response.ok) {
            // Success - for simplicity we reload the page to refresh all stats
            window.location.reload();
        } else {
            alert('Failed to mark as paid. Check permissions.');
            button.disabled = false;
            button.innerHTML = originalContent;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An unexpected error occurred.');
        button.disabled = false;
        button.innerHTML = originalContent;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Add Select All logic if needed for many city groups
    // The current template handles individual city-status groups
});
</script>
@endpush
@endsection
