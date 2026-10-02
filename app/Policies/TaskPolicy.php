<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Any member of the task's organization (and super admins) can view.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->isSuperAdmin() || $user->belongsToOrganization($task->organization_id);
    }

    /**
     * Manager and above can create tasks. Called with the parent project's organization.
     */
    public function create(User $user, $organization): bool
    {
        return $user->isSuperAdmin() || $user->canManageContent($organization);
    }

    /**
     * Manager and above, or the assignee, can update a task.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($task->assigned_to === $user->id) {
            return true;
        }

        return $user->canManageContent($task->organization);
    }

    /**
     * Admin and above can delete tasks.
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasRoleIn($task->organization, ['owner', 'admin']);
    }
}
