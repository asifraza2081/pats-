<div class="d-flex justify-content-between align-items-center py-2 animate__animated animate__fadeIn">
    <div>
        <div class="fw-bold">{{ $edu->degree_name }} <span class="badge bg-blue-lt ms-2">{{ \App\Models\EducationHistory::$levelLabels[$edu->degree_level] ?? '' }}</span></div>
        <div class="text-muted small mt-1">
            <i class="ti ti-book me-1"></i> {{ $edu->subject_major ?? 'N/A' }} &bull; 
            <i class="ti ti-building-bank me-1"></i> {{ $edu->institution ?? 'N/A' }} &bull; 
            <i class="ti ti-calendar me-1"></i> {{ $edu->passing_year ?? 'N/A' }}
        </div>
        <div class="text-success small fw-semibold mt-1"><i class="ti ti-chart-bar me-1"></i> {{ $edu->percentage_display }}</div>
    </div>
    @if(!$candidate->profile_locked)
    <form method="POST" action="{{ route('candidate.education.destroy', $edu) }}" onsubmit="return confirm('Remove this record?')">
        @csrf @method('DELETE')
        <button class="btn btn-action text-danger" title="Remove"><i class="ti ti-trash"></i></button>
    </form>
    @endif
</div>
