<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Any member of the project's organization (and super admins) can view.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->isSuperAdmin() || $user->belongsToOrganization($project->organization_id);
    }

    /**
     * Manager and above can create projects inside an organization.
     */
    public function create(User $user, Organization $organization): bool
    {
        return $user->isSuperAdmin() || $user->canManageContent($organization);
    }

    /**
     * Manager and above can update projects.
     */
    public function update(User $user, Project $project): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->canManageContent($project->organization);
    }

    /**
     * Admin and above can delete projects.
     */
    public function delete(User $user, Project $project): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasRoleIn($project->organization, ['owner', 'admin']);
    }
}
