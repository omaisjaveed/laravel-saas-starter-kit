@extends('layouts.app')
@section('title', 'Organizations')
@section('page_title', 'My Organizations')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0">Organizations</h1>
    <a href="{{ route('organizations.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Create organization
    </a>
</div>

<div class="row g-3">
    @forelse ($organizations as $organization)
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if ($organization->logo_path)
                            <img src="{{ asset(\Illuminate\Support\Facades\Storage::url($organization->logo_path)) }}"
                                 class="org-logo-lg" style="width:56px;height:56px;" alt="{{ $organization->name }}">
                        @else
                            <span class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-building"></i></span>
                        @endif
                        <div>
                            <h2 class="h6 mb-0">{{ $organization->name }}</h2>
                            <span class="badge bg-light text-dark border">{{ $organization->pivot->role ?? '—' }}</span>
                        </div>
                    </div>

                    <p class="text-muted small mb-3">{{ \Illuminate\Support\Str::limit($organization->description ?? 'No description.', 90) }}</p>

                    <div class="d-flex gap-3 small text-muted mb-3">
                        <span><i class="bi bi-people me-1"></i>{{ $organization->users_count }} members</span>
                        <span><i class="bi bi-kanban me-1"></i>{{ $organization->projects_count }} projects</span>
                        <span><i class="bi bi-list-check me-1"></i>{{ $organization->tasks_count }} tasks</span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('organizations.show', $organization) }}" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="bi bi-eye me-1"></i> Open
                        </a>
                        @can('update', $organization)
                            <a href="{{ route('organizations.edit', $organization) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-gear"></i>
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-5">
                    <i class="bi bi-building" style="font-size: 3rem; color: #94a3b8;"></i>
                    <p class="text-muted mt-3 mb-0">No organizations yet. Create your first one.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $organizations->links() }}
</div>
@endsection
