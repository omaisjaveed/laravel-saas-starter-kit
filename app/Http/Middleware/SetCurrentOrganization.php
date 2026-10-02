<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Binds the current tenant context for routes that are not organization-scoped
 * (dashboard, activity, etc.), based on the selected organization stored in the
 * session and validated against the user's memberships.
 */
class SetCurrentOrganization
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            // Super admins get a platform-wide context (no tenant scope).
            return $next($request);
        }

        $organization = null;
        $selectedId = Session::get('current_organization_id');

        if ($selectedId && $user->belongsToOrganization((int) $selectedId)) {
            $organization = Organization::find((int) $selectedId);
        }

        if (! $organization) {
            $organization = $user->organizations()->first();
        }

        if ($organization) {
            app()->instance('tenant.organization_id', $organization->id);
            Session::put('current_organization_id', $organization->id);
        }

        return $next($request);
    }
}
