<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Support\CurrentOrganization;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the dashboard. Super admins get a platform-wide view,
     * organization members get their organization view.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return $this->platformDashboard();
        }

        return $this->organizationDashboard($user);
    }

    /**
     * Platform admin dashboard with global statistics.
     */
    protected function platformDashboard()
    {
        $stats = [
            'users' => User::count(),
            'organizations' => Organization::count(),
            'projects' => Project::count(),
            'tasks' => Task::count(),
            'tasks_todo' => Task::where('status', 'todo')->count(),
            'tasks_in_progress' => Task::where('status', 'in_progress')->count(),
            'tasks_done' => Task::where('status', 'done')->count(),
            'memberships' => Organization::withCount('users')->get()->sum('users_count'),
        ];

        $recentActivity = ActivityLog::with(['user', 'organization'])
            ->latest()
            ->limit(10)
            ->get();

        $recentOrganizations = Organization::withCount(['users', 'projects', 'tasks'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.admin', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'recentOrganizations' => $recentOrganizations,
        ]);
    }

    /**
     * Organization dashboard with tenant-scoped statistics.
     */
    protected function organizationDashboard(User $user)
    {
        $organization = CurrentOrganization::get();

        if (! $organization) {
            return view('dashboard.index', [
                'organization' => null,
                'stats' => null,
                'recentActivity' => collect(),
                'recentProjects' => collect(),
                'myTasks' => collect(),
            ]);
        }

        $stats = [
            'members' => $organization->users()->count(),
            'projects' => $organization->projects()->count(),
            'tasks' => $organization->tasks()->count(),
            'tasks_todo' => $organization->tasks()->where('status', 'todo')->count(),
            'tasks_in_progress' => $organization->tasks()->where('status', 'in_progress')->count(),
            'tasks_done' => $organization->tasks()->where('status', 'done')->count(),
        ];

        $recentActivity = $organization->activityLogs()
            ->with(['user', 'organization'])
            ->limit(10)
            ->get();

        $recentProjects = $organization->projects()
            ->withCount('tasks')
            ->latest()
            ->limit(5)
            ->get();

        $myTasks = $organization->tasks()
            ->where('assigned_to', $user->id)
            ->where('status', '!=', 'done')
            ->with(['project', 'assignee'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'organization' => $organization,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'recentProjects' => $recentProjects,
            'myTasks' => $myTasks,
        ]);
    }
}
