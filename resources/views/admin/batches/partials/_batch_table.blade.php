<table class="table table-vcenter table-hover card-table text-nowrap">
    <thead>
        <tr class="bg-light bg-opacity-75">
            <th class="w-1">Session ID</th>
            <th>Project</th>
            <th>Test Date & Time</th>
            <th>Allocation</th>
            <th class="w-1 text-end">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($batches as $batch)
        <tr class="bg-white">
            <td><span class="text-secondary fw-bold">#{{ $batch->id }}</span></td>
            <td>
                <div class="font-weight-medium text-body">{{ $batch->project->name }}</div>
                <div class="text-secondary small">Session No: {{ $batch->batch_number }}</div>
            </td>
            <td>
                <div class="font-weight-medium text-body">{{ $batch->test_date->format('d M, Y') }}</div>
                <div class="text-secondary small">{{ date('h:i A', strtotime($batch->reporting_time)) }} - {{ date('h:i A', strtotime($batch->start_time)) }}</div>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="flex-fill" style="width: 150px;">
                        <div class="font-weight-medium mb-1 small">{{ $batch->booked_seats }} / {{ $batch->total_seats }}</div>
                        <div class="progress progress-xs">
                            <div class="progress-bar bg-blue" style="width: {{ $batch->total_seats > 0 ? ($batch->booked_seats / $batch->total_seats) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </td>
            <td class="text-end">
                <div class="btn-list flex-nowrap justify-content-end">
                    @if($batch->is_ready)
                    <div class="btn-group btn-group-sm rounded-pill shadow-sm">
                        <a href="{{ route('admin.batches.bulk-slips', $batch) }}" target="_blank" class="btn btn-white" title="Print Roll No Slips">
                            <i class="ti ti-id me-1"></i> Slips
                        </a>
                        <a href="{{ route('admin.batches.attendance-sheet', $batch) }}" target="_blank" class="btn btn-white text-info" title="Print Attendance Sheet">
                            <i class="ti ti-file-text me-1"></i> Sheet
                        </a>
                        <a href="{{ route('admin.batches.answer-sheets', $batch) }}" target="_blank" class="btn btn-white text-warning" title="Print OMR Sheets">
                            <i class="ti ti-circle-check me-1"></i> OMR
                        </a>
                    </div>
                    @endif
                    <a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-outline-primary btn-icon btn-sm rounded-pill" title="View Session Details">
                        <i class="ti ti-eye"></i>
                    </a>
                    <a href="{{ route('admin.batches.attendance', $batch) }}" class="btn btn-outline-info btn-icon btn-sm rounded-pill" title="Attendance Tracking">
                        <i class="ti ti-user-check"></i>
                    </a>
                    <a href="{{ route('admin.batches.group-show', ['project' => $batch->project_id, 'test_date' => $batch->test_date->toDateString(), 'batch_number' => $batch->batch_number]) }}" class="btn btn-outline-purple btn-icon btn-sm rounded-pill" title="View Mega Session Group">
                        <i class="ti ti-layout-distribute-vertical"></i>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
