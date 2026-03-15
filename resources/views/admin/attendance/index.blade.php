@extends('layouts.dashboard')

@section('title', 'Attendance Sheets')
@section('page-title', 'Attendance Tracking')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <!-- Step 1: Selection -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold py-3">
                <i class="ti ti-filter me-2"></i> Select Center, Date & Time Slot
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.attendance.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Test Center</label>
                        <select name="center_id" class="form-select tom-select" onchange="this.form.submit()">
                            <option value="">-- Select Center --</option>
                            @foreach($centers as $c)
                                <option value="{{ $c->id }}" {{ $centerId == $c->id ? 'selected' : '' }}>
                                    {{ $c->city->name ?? 'Unknown' }} — {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($centerId)
                            <input type="hidden" name="slot_date" value="{{ $slotDate }}">
                            <input type="hidden" name="slot_id" value="{{ $slotId }}">
                        @endif
                    </div>

                    @if ($centerId && $dates->isNotEmpty())
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Test Date</label>
                        <select name="slot_date" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Select Date --</option>
                            @foreach($dates as $d)
                                <option value="{{ $d->format('Y-m-d') }}" {{ $slotDate === $d->format('Y-m-d') ? 'selected' : '' }}>
                                    {{ $d->format('D, d M Y') }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="center_id" value="{{ $centerId }}">
                    </div>
                    @endif

                    @if ($slotDate && $slots->isNotEmpty())
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Time Slot</label>
                        <select name="slot_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Select Slot --</option>
                            @foreach($slots as $s)
                                <option value="{{ $s->id }}" {{ $slotId == $s->id ? 'selected' : '' }}>
                                    {{ $s->start_time }} — {{ $s->project->name }}
                                    ({{ $s->booked_seats }}/{{ $s->total_seats }} booked)
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="center_id" value="{{ $centerId }}">
                        <input type="hidden" name="slot_date" value="{{ $slotDate }}">
                    </div>
                    @endif

                    @if ($slotId)
                    <div class="col-md-2 d-flex gap-2">
                        <a href="{{ route('admin.attendance.print', $slotId) }}" target="_blank" class="btn btn-success w-100">
                            <i class="ti ti-printer me-1"></i> Print Sheet
                        </a>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Attendance Preview -->
        @if ($slotId && $attendees->isNotEmpty())
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold">
                    <i class="ti ti-users me-2"></i>
                    {{ $center->name ?? '' }} — {{ \Carbon\Carbon::parse($slotInfo->test_date)->format('D, d M Y') }} at {{ $slotInfo->start_time }}
                    <span class="badge bg-primary ms-2">{{ count($attendees) }} candidates</span>
                </span>
                <a href="{{ route('admin.attendance.print', $slotId) }}" target="_blank" class="btn btn-sm btn-outline-success">
                    <i class="ti ti-printer me-1"></i> Print Full Sheet
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px">#</th>
                                <th>Roll No.</th>
                                <th>Candidate Name</th>
                                <th>Father's Name</th>
                                <th>CNIC</th>
                                <th>Post Applied</th>
                                <th>Status</th>
                                <th class="text-center">Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendees as $i => $a)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-bold text-primary">{{ $a->roll_no ?? '—' }}</td>
                                <td>{{ $a->candidate_name }}</td>
                                <td>{{ $a->father_name ?? '—' }}</td>
                                <td>{{ $a->cnic }}</td>
                                <td>{{ $a->job_title }}</td>
                                <td>
                                    @php
                                        $sc = ['submitted' => 'secondary', 'fee_paid' => 'info', 'scheduled' => 'primary', 'result_declared' => 'success'];
                                        $badgeClass = $sc[$a->status] ?? 'dark';
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}-lt text-{{ $badgeClass }} border border-{{ $badgeClass }}">
                                        {{ strtoupper(str_replace('_', ' ', $a->status)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input" disabled>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @elseif ($slotId && $attendees->isEmpty())
        <div class="alert alert-info py-4 text-center border-0 shadow-sm">
            <i class="ti ti-info-circle fs-1 mb-2 d-block"></i>
            <p class="mb-0">No candidates are assigned to this slot yet.</p>
        </div>
        @elseif ($centerId)
        <div class="alert alert-secondary py-4 text-center border-0 shadow-sm">
            <i class="ti ti-calendar-event fs-1 mb-2 d-block"></i>
            <p class="mb-0">Select a date and time slot to preview the attendance sheet.</p>
        </div>
        @else
        <div class="alert alert-secondary py-4 text-center border-0 shadow-sm">
            <i class="ti ti-buildingfs-1 mb-2 d-block"></i>
            <p class="mb-0">Select a test center to get started.</p>
        </div>
        @endif
    </div>
</div>
@endsection
