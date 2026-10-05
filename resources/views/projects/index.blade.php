@extends('layouts.app')
@section('title', 'Projects')
@section('page_title', $organization->name.' — Projects')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <h1 class="h4 mb-0">Projects</h1>
    @can('create', [\App\Models\Project::class, $organization])
        <a href="{{ route('organizations.projects.create', $organization) }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New project
        </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <form method="GET" action="{{ route('organizations.projects.index', $organization) }}" class="row g-2">
            <div class="col-sm-6 col-md-4">
                <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search projects...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                @if ($search)
                    <a href="{{ route('organizations.projects.index', $organization) }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Tasks</th>
                    <th>Created by</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr>
                        <td>
                            <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="text-decoration-none fw-semibold">
                                {{ $project->name }}
                            </a>
                            <div class="text-muted small">{{ \Illuminate\Support\Str::limit($project->description ?? '', 60) }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $project->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($project->status) }}</span>
                        </td>
                        <td>{{ $project->tasks_count }}</td>
                        <td class="small">{{ $project->creator?->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $project->created_at->format('M j, Y') }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                                @can('update', $project)
                                    <a href="{{ route('organizations.projects.edit', [$organization, $project]) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('delete', $project)
                                    <form method="POST" action="{{ route('organizations.projects.destroy', [$organization, $project]) }}"
                                          onsubmit="return confirm('Delete project {{ $project->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No projects found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $projects->links() }}
</div>
@endsection
