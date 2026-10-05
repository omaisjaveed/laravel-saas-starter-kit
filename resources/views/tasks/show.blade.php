@extends('layouts.app')
@section('title', 'Task')
@section('page_title', $organization->name.' — Task')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span><i class="bi bi-list-check me-1"></i> Task details</span>
                <div class="d-flex gap-2">
                    <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Project
                    </a>
                    @can('update', $task)
                        <a href="{{ route('organizations.projects.tasks.edit', [$organization, $project, $task]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <h1 class="h5 mb-0 me-auto">{{ $task->title }}</h1>
                    <span class="badge {{ ['todo' => 'bg-secondary', 'in_progress' => 'bg-primary', 'done' => 'bg-success'][$task->status] }}">
                        {{ ['todo' => 'To do', 'in_progress' => 'In progress', 'done' => 'Done'][$task->status] }}
                    </span>
                    <span class="badge {{ ['low' => 'bg-light text-dark border', 'medium' => 'bg-warning', 'high' => 'bg-danger'][$task->priority] }}">
                        {{ ucfirst($task->priority) }} priority
                    </span>
                </div>

                <p class="text-muted">{{ $task->description ?? 'No description.' }}</p>

                <hr>

                <div class="row small">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Project</span>
                            <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="text-decoration-none">{{ $project->name }}</a>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Assigned to</span>
                            <span>{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Due date</span>
                            <span>{{ $task->due_date?->format('M j, Y') ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Created by</span>
                            <span>{{ $task->creator?->name ?? '—' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Created</span>
                            <span>{{ $task->created_at->format('M j, Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Updated</span>
                            <span>{{ $task->updated_at->format('M j, Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                @can('delete', $task)
                    <div class="mt-4 pt-2 border-top d-flex justify-content-end">
                        <form method="POST" action="{{ route('organizations.projects.tasks.destroy', [$organization, $project, $task]) }}"
                              onsubmit="return confirm('Delete task {{ $task->title }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash me-1"></i> Delete task
                            </button>
                        </form>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
