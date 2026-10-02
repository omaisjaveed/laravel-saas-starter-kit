<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Traits\ApiResponse;
use App\Models\Organization;
use App\Models\Task;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use ApiResponse;

    /**
     * List the tasks of a project.
     */
    public function index(Request $request, Organization $organization, int $project): JsonResponse
    {
        $project = $organization->projects()->findOrFail($project);

        $this->authorize('view', $project);

        $tasks = $project->tasks()
            ->with(['assignee', 'creator'])
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->input('assigned_to'), function ($query, $assignedTo) {
                $query->where('assigned_to', $assignedTo);
            })
            ->orderBy('due_date')
            ->orderBy('id')
            ->paginate(15);

        return TaskResource::collection($tasks);
    }

    /**
     * Create a task in the project. The assignee must belong to the organization.
     */
    public function store(StoreTaskRequest $request, Organization $organization, int $project): JsonResponse
    {
        $project = $organization->projects()->findOrFail($project);

        $this->authorize('create', [Task::class, $project->organization]);

        $data = $request->validated();

        if (! empty($data['assigned_to']) && ! $organization->users()->where('users.id', $data['assigned_to'])->exists()) {
            return $this->error('The selected assignee is not a member of this organization.', 422);
        }

        $task = $project->tasks()->create($data + [
            'organization_id' => $organization->id,
            'created_by' => $request->user()->id,
        ]);

        ActivityLogger::log('task.created', "Task created: {$task->title}", $task);

        return $this->success(new TaskResource($task->load(['assignee', 'creator'])), 'Task created.', 201);
    }

    /**
     * Display a task.
     */
    public function show(Request $request, Organization $organization, int $project, int $task): JsonResponse
    {
        // Tenant-safe lookup: a task from another organization/project is not found.
        $task = $organization->projects()->findOrFail($project)->tasks()->findOrFail($task);

        $this->authorize('view', $task);

        return $this->success(new TaskResource($task->load(['assignee', 'creator', 'project'])));
    }

    /**
     * Update a task (including status and assignment).
     */
    public function update(UpdateTaskRequest $request, Organization $organization, int $project, int $task): JsonResponse
    {
        $task = $organization->projects()->findOrFail($project)->tasks()->findOrFail($task);

        $this->authorize('update', $task);

        $data = $request->validated();

        if (! empty($data['assigned_to']) && ! $organization->users()->where('users.id', $data['assigned_to'])->exists()) {
            return $this->error('The selected assignee is not a member of this organization.', 422);
        }

        $task->update($data);

        ActivityLogger::log('task.updated', "Task updated: {$task->title}", $task, [
            'changed' => array_keys($data),
        ]);

        return $this->success(new TaskResource($task->fresh()->load(['assignee', 'creator'])), 'Task updated.');
    }

    /**
     * Delete a task.
     */
    public function destroy(Request $request, Organization $organization, int $project, int $task): JsonResponse
    {
        $task = $organization->projects()->findOrFail($project)->tasks()->findOrFail($task);

        $this->authorize('delete', $task);

        $title = $task->title;
        $task->delete();

        ActivityLogger::log('task.deleted', "Task deleted: {$title}", $task);

        return $this->success(null, 'Task deleted.');
    }
}
