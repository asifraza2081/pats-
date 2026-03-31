@extends('layouts.dashboard')
@section('title', 'Candidates Archive')
@section('page-title', 'Candidate Directory')

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-1 pt-3">
                <h3 class="card-title fw-bold text-primary">Registered Candidates</h3>
                <div class="card-actions">
                    <form action="{{ route('admin.candidates.index') }}" method="GET" class="input-group input-group-flat">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search Name, CNIC, Phone...">
                        <span class="input-group-text">
                            <button type="submit" class="link-secondary border-0 bg-transparent" title="Search"><i class="ti ti-search fs-2"></i></button>
                        </span>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap table-hover">
                    <thead>
                        <tr>
                            <th>Candidate Name</th>
                            <th>CNIC</th>
                            <th>Phone</th>
                            <th>City / Domicile</th>
                            <th class="w-1">Apps</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($candidates as $c)
                        <tr>
                            <td>
                                <div class="d-flex py-1 align-items-center">
                                    @if($c->photo_path)
                                    <span class="avatar me-2" style="background-image: url('{{ asset('storage/'.$c->photo_path) }}')"></span>
                                    @else
                                    <span class="avatar me-2">{{ substr($c->user->first_name, 0, 1) }}</span>
                                    @endif
                                    <div class="flex-fill">
                                        <div class="font-weight-medium text-body fw-bold">{{ $c->user->full_name }}</div>
                                        <div class="text-secondary small">{{ $c->user->email ?: 'No Email' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-secondary">{{ $c->user->cnic }}</span></td>
                            <td><span class="text-secondary">{{ $c->user->phone }}</span></td>
                            <td>
                                <div class="text-body small">{{ $c->addressCity?->name }}</div>
                                <div class="text-secondary xsmall">{{ $c->domicileCity?->province }}</div>
                            </td>
                            <td><span class="badge bg-blue-lt">{{ $c->applications_count ?? $c->applications()->count() }}</span></td>
                            <td>
                                <a href="{{ route('admin.candidates.show', $c) }}" class="btn btn-outline-primary btn-sm btn-icon" title="View Profile">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-secondary py-5 italic">No candidates found in the system.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($candidates->hasPages())
            <div class="card-footer">
                {{ $candidates->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
