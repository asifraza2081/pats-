<div class="d-flex justify-content-between align-items-center py-2 animate__animated animate__fadeIn">
    <div>
        <div class="fw-bold">{{ $exp->designation }} <span class="badge bg-secondary-lt ms-2">{{ $exp->job_type }}</span></div>
        <div class="text-muted small mt-1"><i class="ti ti-building me-1"></i> {{ $exp->organization_name }}</div>
        <div class="text-muted small mt-1">
            <i class="ti ti-calendar me-1"></i> {{ $exp->from_date->format('M Y') }} &mdash; {{ $exp->to_date ? $exp->to_date->format('M Y') : 'Present' }} 
            <span class="ms-2 fw-semibold text-secondary">({{ $exp->duration_label }})</span>
        </div>
    </div>
    @if(!$candidate->profile_locked)
    <form method="POST" action="{{ route('candidate.experience.destroy', $exp) }}" onsubmit="return confirm('Remove this record?')">
        @csrf @method('DELETE')
        <button class="btn btn-action text-danger" title="Remove"><i class="ti ti-trash"></i></button>
    </form>
    @endif
</div>
