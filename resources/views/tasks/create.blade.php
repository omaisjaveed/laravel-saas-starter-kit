@extends('layouts.app')
@section('title', 'New Task')
@section('page_title', $organization->name.' — New task')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <i class="bi bi-list-check me-1"></i> Create task in <strong>{{ $project->name }}</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('organizations.projects.tasks.store', [$organization, $project]) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input id="title" type="text" name="title" value="{{ old('title') }}"
                               class="form-control @error('title') is-invalid @enderror" required autofocus>
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="todo" {{ old('status', 'todo') === 'todo' ? 'selected' : '' }}>To do</option>
                                <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In progress</option>
                                <option value="done" {{ old('status') === 'done' ? 'selected' : '' }}>Done</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select id="priority" name="priority" class="form-select @error('priority') is-invalid @enderror">
                                <option value="low" {{ old('priority', 'medium') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="due_date" class="form-label">Due date</label>
                            <input id="due_date" type="date" name="due_date" value="{{ old('due_date') }}"
                                   class="form-control @error('due_date') is-invalid @enderror">
                            @error('due_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="assigned_to" class="form-label">Assign to</label>
                        <select id="assigned_to" name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                            <option value="">— Unassigned —</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}" {{ old('assigned_to') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }} ({{ ucfirst($member->pivot->role) }})
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Create task</button>
                        <a href="{{ route('organizations.projects.show', [$organization, $project]) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
