@extends('layouts.app')
@section('title', $organization->name)
@section('page_title', $organization->name)

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center gap-3">
            @if ($organization->logo_path)
                <img src="{{ asset(\Illuminate\Support\Facades\Storage::url($organization->logo_path)) }}"
                     class="org-logo-lg" alt="{{ $organization->name }}">
            @else
                <span class="stat-icon bg-primary bg-opacity-10 text-primary" style="width:96px;height:96px;font-size:2.5rem;border-radius:1rem;">
                    <i class="bi bi-building"></i>
                </span>
            @endif

            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2">
                    <h1 class="h4 mb-0">{{ $organization->name }}</h1>
                    <span class="badge bg-light text-dark border">{{ auth()->user()->roleIn($organization) ?? 'guest' }}</span>
                </div>
                <p class="text-muted small mb-1 mt-1">
                    {{ $organization->description ?? 'No description.' }}
                </p>
                <div class="d-flex flex-wrap gap-3 small text-muted">
                    @if ($organization->email)<span><i class="bi bi-envelope me-1"></i>{{ $organization->email }}</span>@endif
                    @if ($organization->website)<span><i class="bi bi-globe me-1"></i><a href="{{ $organization->website }}" target="_blank" class="text-decoration-none">{{ $organization->website }}</a></span>@endif
                    <span><i class="bi bi-hash me-1"></i>{{ $organization->slug }}</span>
                </div>
            </div>

            <div class="d-flex gap-2">
                @can('update', $organization)
                    <a href="{{ route('organizations.edit', $organization) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-gear me-1"></i> Settings
                    </a>
                @endcan
                @can('manageMembers', $organization)
                    <a href="{{ route('organizations.users.index', $organization) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-people me-1"></i> Members
                    </a>
                @endcan
            </div>
        </div>
    </div>
</div>

{{-- Organization statistics --}}
<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold text-primary">{{ $stats['members'] }}</div>
                <div class="text-muted small">Members</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold text-success">{{ $stats['projects'] }}</div>
                <div class="text-muted small">Projects</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold text-warning">{{ $stats['tasks'] }}</div>
                <div class="text-muted small">Tasks</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Members preview --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people me-1"></i> Members</span>
                <a href="{{ route('organizations.users.index', $organization) }}" class="small">Manage</a>
            </div>
            <div class="card-body p-0">
                @forelse ($members as $member)
                    <a href="{{ route('users.show', $member) }}" class="d-flex align-items-center gap-2 text-decoration-none text-reset border-bottom px-3 py-2">
                        <span class="avatar-sm">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                        <div class="flex-grow-1">
                            <div class="small fw-semibold">{{ $member->name }}</div>
                            <small class="text-muted">{{ $member->email }}</small>
                        </div>
                        <span class="badge bg-light text-dark border">{{ $member->pivot->role }}</span>
                    </a>
                @empty
                    <div class="p-3 text-muted small text-center">No members</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-1"></i> Recent activity</span>
                <a href="{{ route('activity.index') }}" class="small">View all</a>
            </div>
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
    </div>
</div>
@endsection
