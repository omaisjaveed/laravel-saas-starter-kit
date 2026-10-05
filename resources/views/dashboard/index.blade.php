@extends('layouts.app')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
@if (! $organization)
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center p-5">
            <i class="bi bi-building" style="font-size: 3rem; color: #3b82f6;"></i>
            <h2 class="h4 mt-3">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-muted">You are not a member of any organization yet.<br>Create your own organization or ask an owner to invite you.</p>
            <a href="{{ route('organizations.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Create organization
            </a>
        </div>
    </div>
@else
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 mb-0">{{ $organization->name }}</h1>
            <span class="text-muted small">Organization dashboard</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('organizations.show', $organization) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-building me-1"></i> Organization profile
            </a>
            @can('create', [\App\Models\Project::class, $organization])
                <a href="{{ route('organizations.projects.create', $organization) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> New project
                </a>
            @endcan
        </div>
    </div>

    {{-- Statistics --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people"></i></span>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['members'] }}</div>
                        <div class="text-muted small">Members</div>
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
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-check2-all"></i></span>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['tasks_done'] }}</div>
                        <div class="text-muted small">Tasks done</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Task status breakdown --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><i class="bi bi-pie-chart me-1"></i> Task status</div>
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
                </div>
            </div>
        </div>

        {{-- My open tasks --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><i class="bi bi-person-check me-1"></i> My open tasks</div>
                <div class="card-body p-0">
                    @forelse ($myTasks as $task)
                        <a href="{{ route('organizations.projects.tasks.show', [$organization, $task->project_id, $task->id]) }}"
                           class="d-block text-decoration-none text-reset border-bottom px-3 py-2">
                            <div class="d-flex justify-content-between">
                                <span class="small fw-semibold">{{ \Illuminate\Support\Str::limit($task->title, 40) }}</span>
                                <span class="badge bg-light text-dark border">{{ $task->project->name }}</span>
                            </div>
                            <small class="text-muted">
                                @if ($task->due_date) Due {{ $task->due_date->format('M j, Y') }} @else No due date @endif
                            </small>
                        </a>
                    @empty
                        <div class="p-3 text-muted small text-center">No open tasks assigned to you</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent projects --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><i class="bi bi-kanban me-1"></i> Recent projects</div>
                <div class="card-body p-0">
                    @forelse ($recentProjects as $project)
                        <a href="{{ route('organizations.projects.show', [$organization, $project]) }}"
                           class="d-block text-decoration-none text-reset border-bottom px-3 py-2">
                            <div class="d-flex justify-content-between">
                                <span class="small fw-semibold">{{ $project->name }}</span>
                                <span class="badge bg-light text-dark border">{{ $project->tasks_count }} tasks</span>
                            </div>
                            <small class="text-muted">
                                <span class="badge {{ $project->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($project->status) }}</span>
                            </small>
                        </a>
                    @empty
                        <div class="p-3 text-muted small text-center">No projects yet</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom"><i class="bi bi-clock-history me-1"></i> Recent activity</div>
        <div class="card-body p-0">
            @forelse ($recentActivity as $activity)
                <div class="d-flex align-items-start gap-2 border-bottom px-3 py-2">
                    <i class="bi bi-dot text-primary fs-4"></i>
                    <div>
                        <div class="small">
                            <strong>{{ $activity->user?->name ?? 'System' }}</strong>
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
@endif
@endsection
