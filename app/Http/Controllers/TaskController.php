<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * List the tasks of a project.
     */
    public function index(Request $request, Organization $organization, int $project)
    {
        $project = $organization->projects()->findOrFail($project);

        $this->authorize('view', $project);

        return redirect()->route('organizations.projects.show', [$organization, $project]);
    }

    /**
     * Show the create task form.
     */
    public function create(Request $request, Organization $organization, int $project)
    {
        $project = $organization->projects()->findOrFail($project);

        $this->authorize('create', [Task::class, $project->organization]);

        $members = $organization->users()->orderBy('name')->get();

        return view('tasks.create', compact('organization', 'project', 'members'));
    }

    /**
     * Store a newly created task. The assignee must belong to the organization.
     */
    public function store(Request $request, Organization $organization, int $project)
    {
        $project = $organization->projects()->findOrFail($project);

        $this->authorize('create', [Task::class, $project->organization]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['sometimes', 'required', 'in:todo,in_progress,done'],
            'priority' => ['sometimes', 'required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'integer', function ($attribute, $value, $fail) use ($organization) {
                if ($value && ! $organization->users()->where('users.id', $value)->exists()) {
                    $fail('The selected assignee is not a member of this organization.');
                }
            }],
        ]);

        $task = $project->tasks()->create($data + [
            'organization_id' => $organization->id,
            'created_by' => $request->user()->id,
        ]);

        ActivityLogger::log('task.created', "Task created: {$task->title}", $task);

        return redirect()
            ->route('organizations.projects.show', [$organization, $project])
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the task details.
     */
    public function show(Request $request, Organization $organization, int $project, int $task)
    {
        // Tenant-safe lookup: a task from another organization/project is not found.
        $task = $organization->projects()->findOrFail($project)->tasks()->findOrFail($task);

        $this->authorize('view', $task);

        return view('tasks.show', compact('organization', 'project', 'task'));
    }

    /**
     * Show the edit task form.
     */
    public function edit(Request $request, Organization $organization, int $project, int $task)
    {
        $task = $organization->projects()->findOrFail($project)->tasks()->findOrFail($task);

        $this->authorize('update', $task);

        $members = $organization->users()->orderBy('name')->get();

        return view('tasks.edit', compact('organization', 'project', 'task', 'members'));
    }

    /**
     * Update the task (including status and assignment).
     */
    public function update(Request $request, Organization $organization, int $project, int $task)
    {
        $task = $organization->projects()->findOrFail($project)->tasks()->findOrFail($task);

        $this->authorize('update', $task);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['sometimes', 'required', 'in:todo,in_progress,done'],
            'priority' => ['sometimes', 'required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'integer', function ($attribute, $value, $fail) use ($organization) {
                if ($value && ! $organization->users()->where('users.id', $value)->exists()) {
                    $fail('The selected assignee is not a member of this organization.');
                }
            }],
        ]);

        $changes = collect($data)->diffAssoc(collect($task->only(array_keys($data))))->keys()->all();

        $task->update($data);

        ActivityLogger::log('task.updated', "Task updated: {$task->title}", $task, [
            'changed' => $changes,
        ]);

        return redirect()
            ->route('organizations.projects.tasks.show', [$organization, $project, $task])
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Delete the task.
     */
    public function destroy(Request $request, Organization $organization, int $project, int $task)
    {
        $task = $organization->projects()->findOrFail($project)->tasks()->findOrFail($task);

        $this->authorize('delete', $task);

        $title = $task->title;
        $task->delete();

        ActivityLogger::log('task.deleted', "Task deleted: {$title}", $task);

        return redirect()
            ->route('organizations.projects.show', [$organization, $project])
            ->with('success', 'Task deleted.');
    }
}
