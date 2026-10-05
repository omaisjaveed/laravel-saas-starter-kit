@extends('layouts.app')
@section('title', $project->name)
@section('page_title', $organization->name.' — '.$project->name)

@section('content')
<div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h1 class="h4 mb-0">{{ $project->name }}</h1>
            <span class="badge {{ $project->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($project->status) }}</span>
        </div>
        <p class="text-muted small mb-0 mt-1">{{ $project->description ?? 'No description.' }}</p>
        <small class="text-muted">Created by {{ $project->creator?->name ?? '—' }} on {{ $project->created_at->format('M j, Y') }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('organizations.projects.index', $organization) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> All projects
        </a>
        @can('create', [\App\Models\Task::class, $organization])
            <a href="{{ route('organizations.projects.tasks.create', [$organization, $project]) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i> New task
            </a>
        @endcan
        @can('update', $project)
            <a href="{{ route('organizations.projects.edit', [$organization, $project]) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
        @endcan
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><i class="bi bi-list-check me-1"></i> Tasks ({{ $tasks->total() }})</span>
        <form method="GET" action="{{ route('organizations.projects.show', [$organization, $project]) }}" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <option value="todo" {{ request('status') === 'todo' ? 'selected' : '' }}>To do</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In progress</option>
                <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
            </select>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Assignee</th>
                    <th>Due date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                    <tr>
                        <td>
                            <a href="{{ route('organizations.projects.tasks.show', [$organization, $project, $task]) }}" class="text-decoration-none fw-semibold">
                                {{ $task->title }}
                            </a>
                            <div class="text-muted small">{{ \Illuminate\Support\Str::limit($task->description ?? '', 60) }}</div>
                        </td>
                        <td>
                            <span class="badge {{ ['todo' => 'bg-secondary', 'in_progress' => 'bg-primary', 'done' => 'bg-success'][$task->status] }}">
                                {{ ['todo' => 'To do', 'in_progress' => 'In progress', 'done' => 'Done'][$task->status] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ ['low' => 'bg-light text-dark border', 'medium' => 'bg-warning', 'high' => 'bg-danger'][$task->priority] }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>
                        <td class="small">{{ $task->assignee?->name ?? '—' }}</td>
                        <td class="small {{ $task->due_date && $task->due_date->isPast() && $task->status !== 'done' ? 'text-danger fw-semibold' : 'text-muted' }}">
                            {{ $task->due_date?->format('M j, Y') ?? '—' }}
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('organizations.projects.tasks.show', [$organization, $project, $task]) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                                @can('update', $task)
                                    <a href="{{ route('organizations.projects.tasks.edit', [$organization, $project, $task]) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('delete', $task)
                                    <form method="POST" action="{{ route('organizations.projects.tasks.destroy', [$organization, $project, $task]) }}"
                                          onsubmit="return confirm('Delete task {{ $task->title }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No tasks found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $tasks->links() }}
</div>
@endsection
