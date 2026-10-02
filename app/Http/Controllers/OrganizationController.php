<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Organization;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    /**
     * Display a list of the user's organizations.
     *
     * The membership pivot (with the role) is loaded by querying through the
     * user's organizations relation. Super admins see all organizations.
     */
    public function index(Request $request)
    {
        if ($request->user()->isSuperAdmin()) {
            $organizations = Organization::query()
                ->withCount(['users', 'projects', 'tasks'])
                ->latest()
                ->paginate(12);
        } else {
            $organizations = $request->user()->organizations()
                ->withCount(['users', 'projects', 'tasks'])
                ->latest('organizations.created_at')
                ->paginate(12);
        }

        return view('organizations.index', compact('organizations'));
    }

    /**
     * Show the create organization form.
     */
    public function create()
    {
        return view('organizations.create');
    }

    /**
     * Store a newly created organization.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);

        $organization = Organization::create($data + ['owner_id' => $request->user()->id]);

        $request->user()->organizations()->attach($organization->id, ['role' => 'owner']);

        ActivityLogger::log('organization.created', 'Organization created: '.$organization->name, $organization);

        session(['current_organization_id' => $organization->id]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Organization created successfully.');
    }

    /**
     * Display the organization profile page.
     */
    public function show(Request $request, Organization $organization)
    {
        $this->authorize('view', $organization);

        $members = $organization->users()->orderBy('name')->limit(8)->get();
        $recentActivity = $organization->activityLogs()->with('user')->limit(8)->get();

        $stats = [
            'members' => $organization->users()->count(),
            'projects' => $organization->projects()->count(),
            'tasks' => $organization->tasks()->count(),
        ];

        return view('organizations.show', compact('organization', 'members', 'recentActivity', 'stats'));
    }

    /**
     * Show the organization settings form.
     */
    public function edit(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);

        return view('organizations.edit', compact('organization'));
    }

    /**
     * Update the organization settings.
     */
    public function update(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);

        $organization->update($data);

        ActivityLogger::log('organization.updated', 'Organization updated: '.$organization->name, $organization, [
            'changed' => array_keys($data),
        ]);

        return redirect()
            ->route('organizations.edit', $organization)
            ->with('success', 'Organization settings updated.');
    }

    /**
     * Upload the organization logo.
     */
    public function updateLogo(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);

        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($organization->logo_path) {
            Storage::disk('public')->delete($organization->logo_path);
        }

        $path = $request->file('logo')->store('organization-logos', 'public');

        $organization->update(['logo_path' => $path]);

        ActivityLogger::log('organization.updated', 'Organization logo updated: '.$organization->name, $organization);

        return back()->with('success', 'Organization logo updated.');
    }

    /**
     * Remove the organization (owner only, soft delete).
     */
    public function destroy(Request $request, Organization $organization)
    {
        $this->authorize('delete', $organization);

        $organization->users()->detach();
        $organization->delete();

        ActivityLogger::log('organization.deleted', 'Organization deleted: '.$organization->name, $organization);

        Session()->forget('current_organization_id');

        return redirect()
            ->route('organizations.index')
            ->with('success', 'Organization removed.');
    }

    /**
     * Switch the active organization for the authenticated user.
     */
    public function switchOrganization(Request $request, Organization $organization)
    {
        abort_unless(
            $request->user()->isSuperAdmin() || $request->user()->belongsToOrganization($organization->id),
            403,
            'You do not belong to this organization.'
        );

        session(['current_organization_id' => $organization->id]);

        return redirect()->route('dashboard')->with('success', 'Switched to '.$organization->name);
    }
}
