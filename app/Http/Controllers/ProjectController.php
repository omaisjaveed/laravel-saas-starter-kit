<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Project;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display the organization's projects.
     */
    public function index(Request $request, Organization $organization)
    {
        $this->authorize('view', $organization);

        $search = $request->input('q');

        $projects = $organization->projects()
            ->withCount('tasks')
            ->with('creator')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('projects.index', compact('organization', 'projects', 'search'));
    }

    /**
     * Show the create project form.
     */
    public function create(Request $request, Organization $organization)
    {
        $this->authorize('create', [Project::class, $organization]);

        return view('projects.create', compact('organization'));
    }

    /**
     * Store a newly created project in the current organization.
     */
    public function store(Request $request, Organization $organization)
    {
        $this->authorize('create', [Project::class, $organization]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['sometimes', 'required', 'in:active,archived'],
        ]);

        $project = $organization->projects()->create($data + ['created_by' => $request->user()->id]);

        ActivityLogger::log('project.created', "Project created: {$project->name}", $project);

        return redirect()
            ->route('organizations.projects.show', [$organization, $project])
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the project with its tasks.
     */
    public function show(Request $request, Organization $organization, int $project)
    {
        // Tenant-safe lookup: a project from another organization is not found.
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
            ->paginate(10)
            ->withQueryString();

        $members = $organization->users()->orderBy('name')->get();

        return view('projects.show', compact('organization', 'project', 'tasks', 'members'));
    }

    /**
     * Show the edit project form.
     */
    public function edit(Request $request, Organization $organization, int $project)
    {
        $project = $organization->projects()->findOrFail($project);

        $this->authorize('update', $project);

        return view('projects.edit', compact('organization', 'project'));
    }

    /**
     * Update the project.
     */
    public function update(Request $request, Organization $organization, int $project)
    {
        $project = $organization->projects()->findOrFail($project);

        $this->authorize('update', $project);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['sometimes', 'required', 'in:active,archived'],
        ]);

        $project->update($data);

        ActivityLogger::log('project.updated', "Project updated: {$project->name}", $project);

        return redirect()
            ->route('organizations.projects.show', [$organization, $project])
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Delete the project.
     */
    public function destroy(Request $request, Organization $organization, int $project)
    {
        $project = $organization->projects()->findOrFail($project);

        $this->authorize('delete', $project);

        $name = $project->name;
        $project->delete();

        ActivityLogger::log('project.deleted', "Project deleted: {$name}", $project);

        return redirect()
            ->route('organizations.projects.index', $organization)
            ->with('success', 'Project deleted.');
    }
}
