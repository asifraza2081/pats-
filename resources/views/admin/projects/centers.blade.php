@extends('layouts.dashboard')
@section('title', 'Project Centers Management')
@section('page-title', 'Manage Centers & Examiners')

@section('page-actions')
<a href="{{ route('admin.projects.show', $project) }}" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i> Back to Project
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <form method="POST" action="{{ route('admin.projects.centers.sync', $project) }}" class="card shadow-sm border-0">
            @csrf
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">Map Centers & Assign Examiners for {{ $project->name }}</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info py-2">
                    Select the test centers available for this project and assign an examiner to each for local session oversight.
                </div>

                <div class="table-responsive">
                    <table class="table card-table table-vcenter table-hover">
                        <thead>
                            <tr>
                                <th class="w-1">Select</th>
                                <th>Center Name</th>
                                <th>City</th>
                                <th>Capacity</th>
                                <th>Assigned Examiner</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allCenters as $center)
                            @php $isAssigned = isset($assignedCenters[$center->id]); @endphp
                            <tr>
                                <td>
                                    <input type="checkbox" name="centers[]" value="{{ $center->id }}" class="form-check-input" {{ $isAssigned ? 'checked' : '' }}>
                                </td>
                                <td><span class="fw-bold">{{ $center->name }}</span></td>
                                <td>{{ $center->city->name }}</td>
                                <td>{{ $center->seating_capacity }}</td>
                                <td>
                                    <select name="examiner[{{ $center->id }}]" class="form-select form-select-sm tom-select">
                                        <option value="">Select Examiner (Optional)</option>
                                        @foreach($examiners as $ex)
                                        <option value="{{ $ex->id }}" {{ ($assignedCenters[$center->id] ?? null) == $ex->id ? 'selected' : '' }}>
                                            {{ $ex->full_name }} ({{ $ex->email }})
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-link">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-2"></i> Sync Assignments</button>
            </div>
        </form>
    </div>
</div>
@endsection
