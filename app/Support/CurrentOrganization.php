<?php

namespace App\Support;

use App\Models\Organization;

/**
 * Accessor for the current tenant organization bound in the container
 * by the SetOrganizationContext / SetCurrentOrganization middleware.
 */
class CurrentOrganization
{
    /**
     * The current tenant organization id, or null when no tenant context exists.
     */
    public static function id(): ?int
    {
        return app()->bound('tenant.organization_id') ? app('tenant.organization_id') : null;
    }

    /**
     * The current tenant organization model, or null.
     */
    public static function get(): ?Organization
    {
        $id = self::id();

        return $id ? Organization::find($id) : null;
    }
}
