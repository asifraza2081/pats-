<div class="empty border-0 shadow-none py-5">
    <div class="empty-icon text-secondary mb-3">
        <i class="{{ $icon ?? 'ti ti-database-off' }} display-1 opacity-20"></i>
    </div>
    <p class="empty-title fw-bold text-dark">{{ $title ?? 'No records found' }}</p>
    <p class="empty-subtitle text-secondary mx-auto" style="max-width: 320px;">
        {{ $subtitle ?? 'There are no items to display in this list at the moment.' }}
    </p>
    @if(isset($actionUrl) && isset($actionLabel))
    <div class="empty-action mt-4">
        <a href="{{ $actionUrl }}" class="btn btn-primary px-4 shadow-sm">
            <i class="{{ $actionIcon ?? 'ti ti-plus' }} me-2"></i> {{ $actionLabel }}
        </a>
    </div>
    @endif
</div>
