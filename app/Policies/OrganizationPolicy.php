<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    /**
     * Members (and super admins) can view the organization.
     */
    public function view(User $user, Organization $organization): bool
    {
        return $user->isSuperAdmin() || $user->belongsToOrganization($organization->id);
    }

    /**
     * Owner or admin can update organization settings.
     */
    public function update(User $user, Organization $organization): bool
    {
        return $user->isSuperAdmin() || $user->canManageOrganization($organization);
    }

    /**
     * Only the owner can delete the organization.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return $user->isOwnerOf($organization);
    }

    /**
     * Owner or admin can manage members (invite, edit role, remove).
     */
    public function manageMembers(User $user, Organization $organization): bool
    {
        return $user->isSuperAdmin() || $user->canManageOrganization($organization);
    }

    /**
     * Any member can create an organization of their own (handled without model).
     */
    public function create(User $user): bool
    {
        return true;
    }
}
