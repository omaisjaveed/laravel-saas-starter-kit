@extends('layouts.app')
@section('title', 'Platform Admin')
@section('page_title', 'Platform Admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h4 mb-0">Platform administration</h1>
        <span class="text-muted small">Global statistics across all organizations</span>
    </div>
    <a href="{{ route('organizations.index') }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-building me-1"></i> All organizations
    </a>
</div>

{{-- Global statistics --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people"></i></span>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['users'] }}</div>
                    <div class="text-muted small">Total users</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-building"></i></span>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['organizations'] }}</div>
                    <div class="text-muted small">Organizations</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-kanban"></i></span>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['projects'] }}</div>
                    <div class="text-muted small">Projects</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-list-check"></i></span>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['tasks'] }}</div>
                    <div class="text-muted small">Tasks</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Task breakdown --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom"><i class="bi bi-pie-chart me-1"></i> Tasks by status</div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center py-1">
                    <span><span class="badge bg-secondary me-2">To do</span></span>
                    <strong>{{ $stats['tasks_todo'] }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1">
                    <span><span class="badge bg-primary me-2">In progress</span></span>
                    <strong>{{ $stats['tasks_in_progress'] }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1">
                    <span><span class="badge bg-success me-2">Done</span></span>
                    <strong>{{ $stats['tasks_done'] }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Total memberships</span>
                    <strong>{{ $stats['memberships'] }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent organizations --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom"><i class="bi bi-building me-1"></i> Recent organizations</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Members</th>
                            <th>Projects</th>
                            <th>Tasks</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrganizations as $org)
                            <tr>
                                <td class="fw-semibold">
                                    @if (auth()->user()->belongsToOrganization($org->id))
                                        <a href="{{ route('organizations.show', $org) }}" class="text-decoration-none">{{ $org->name }}</a>
                                    @else
                                        {{ $org->name }}
                                    @endif
                                </td>
                                <td>{{ $org->users_count }}</td>
                                <td>{{ $org->projects_count }}</td>
                                <td>{{ $org->tasks_count }}</td>
                                <td class="text-muted small">{{ $org->created_at->format('M j, Y') }}</td>
                                <td>
                                    @if (auth()->user()->belongsToOrganization($org->id))
                                        <a href="{{ route('organizations.show', $org) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No organizations yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Recent activity --}}
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-1"></i> Recent activity (all organizations)</span>
        <a href="{{ route('activity.index') }}" class="small">View all</a>
    </div>
    <div class="card-body p-0">
        @forelse ($recentActivity as $activity)
            <div class="d-flex align-items-start gap-2 border-bottom px-3 py-2">
                <i class="bi bi-dot text-primary fs-4"></i>
                <div class="w-100">
                    <div class="small">
                        <strong>{{ $activity->user?->name ?? 'System' }}</strong>
                        @if ($activity->organization)
                            <span class="badge bg-light text-dark border">{{ $activity->organization->name }}</span>
                        @endif
                        {{ $activity->description ?? $activity->action }}
                    </div>
                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                </div>
            </div>
        @empty
            <div class="p-3 text-muted small text-center">No activity yet</div>
        @endforelse
    </div>
</div>
@endsection
