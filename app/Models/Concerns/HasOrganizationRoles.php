<?php

namespace App\Models\Concerns;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Gives a user multi-organization membership with a role in each organization.
 */
trait HasOrganizationRoles
{
    /**
     * All organizations the user belongs to.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * The user's role inside a specific organization (owner, admin, manager, member) or null.
     */
    public function roleIn(Organization $organization): ?string
    {
        $pivot = $this->organizations()->where('organizations.id', $organization->id)->first();

        return $pivot?->pivot->role;
    }

    /**
     * Whether the user belongs to the given organization.
     */
    public function belongsToOrganization(int $organizationId): bool
    {
        return $this->organizations()->where('organizations.id', $organizationId)->exists();
    }

    /**
     * Whether the user has one of the given roles in the organization.
     */
    public function hasRoleIn(Organization $organization, array $roles): bool
    {
        return in_array($this->roleIn($organization), $roles, true);
    }

    /**
     * Whether the user is the owner of the organization.
     */
    public function isOwnerOf(Organization $organization): bool
    {
        return $this->roleIn($organization) === 'owner';
    }

    /**
     * Whether the user can administer the organization (owner or admin).
     */
    public function canManageOrganization(Organization $organization): bool
    {
        return $this->hasRoleIn($organization, ['owner', 'admin']);
    }

    /**
     * Whether the user can manage content such as projects and tasks (owner, admin or manager).
     */
    public function canManageContent(Organization $organization): bool
    {
        return $this->hasRoleIn($organization, ['owner', 'admin', 'manager']);
    }

    /**
     * Whether the user is a platform super admin.
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }
}
