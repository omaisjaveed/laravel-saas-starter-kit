<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreProjectRequest;
use App\Http\Requests\Api\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Traits\ApiResponse;
use App\Models\Organization;
use App\Models\Project;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    /**
     * List the organization's projects.
     */
    public function index(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $projects = $organization->projects()
            ->withCount('tasks')
            ->with('creator')
            ->when($request->input('q'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15);

        return ProjectResource::collection($projects);
    }

    /**
     * Create a project in the organization.
     */
    public function store(StoreProjectRequest $request, Organization $organization): JsonResponse
    {
        $this->authorize('create', [Project::class, $organization]);

        $project = $organization->projects()->create(
            $request->validated() + ['created_by' => $request->user()->id]
        );

        ActivityLogger::log('project.created', "Project created: {$project->name}", $project);

        return $this->success(
            new ProjectResource($project->loadCount('tasks')),
            'Project created.',
            201
        );
    }

    /**
     * Display a project with its tasks.
     */
    public function show(Request $request, Organization $organization, int $project): JsonResponse
    {
        // Tenant-safe lookup: a project from another organization is not found.
        $project = $organization->projects()->findOrFail($project);

        $this->authorize('view', $project);

        return $this->success(
            new ProjectResource(
                $project->load(['tasks.assignee', 'tasks.creator', 'creator'])->loadCount('tasks')
            )
        );
    }

    /**
     * Update a project.
     */
    public function update(UpdateProjectRequest $request, Organization $organization, int $project): JsonResponse
    {
        $project = $organization->projects()->findOrFail($project);

        $this->authorize('update', $project);

        $project->update($request->validated());

        ActivityLogger::log('project.updated', "Project updated: {$project->name}", $project, [
            'changed' => array_keys($request->validated()),
        ]);

        return $this->success(new ProjectResource($project->fresh()->loadCount('tasks')), 'Project updated.');
    }

    /**
     * Delete a project.
     */
    public function destroy(Request $request, Organization $organization, int $project): JsonResponse
    {
        $project = $organization->projects()->findOrFail($project);

        $this->authorize('delete', $project);

        $name = $project->name;
        $project->delete();

        ActivityLogger::log('project.deleted', "Project deleted: {$name}", $project);

        return $this->success(null, 'Project deleted.');
    }
}
