<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs.
     *
     * Super admins see platform-wide logs (optionally filtered by organization),
     * organization members only see logs of their current organization.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = ActivityLog::with(['user', 'organization', 'subject']);

        if ($user->isSuperAdmin()) {
            if ($organizationId = $request->input('organization')) {
                $query->where('organization_id', $organizationId);
            }
        } else {
            $organizationId = \App\Support\CurrentOrganization::id();
            $query->where('organization_id', $organizationId ?? 0);
        }

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        $organizations = $user->isSuperAdmin()
            ? \App\Models\Organization::orderBy('name')->get(['id', 'name'])
            : collect();

        return view('activity.index', compact('logs', 'organizations'));
    }
}
