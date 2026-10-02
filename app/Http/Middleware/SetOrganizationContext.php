<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Resolves the {organization} route parameter into the current tenant context.
 *
 * Tenant isolation: the organization always comes from the route (never from a
 * client-supplied organization_id), and the authenticated user must be a member
 * of it (super admins are allowed for platform administration).
 */
class SetOrganizationContext
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $organization = $request->route('organization');

        abort_unless($organization instanceof Organization, 404);

        if (! $user->isSuperAdmin() && ! $user->belongsToOrganization($organization->id)) {
            abort(403, 'You do not belong to this organization.');
        }

        app()->instance('tenant.organization_id', $organization->id);
        Session::put('current_organization_id', $organization->id);

        return $next($request);
    }
}
