<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Central activity logging service.
 *
 * Records important actions (login, user created/invited/removed, role changed,
 * organization created/updated, project/task CRUD) with tenant context, subject,
 * properties and request metadata.
 */
class ActivityLogger
{
    /**
     * Log an activity entry.
     *
     * @param  string  $action  Machine name, e.g. "project.created"
     * @param  string|null  $description  Human readable description
     * @param  Model|null  $subject  The affected model (polymorphic)
     * @param  array  $properties  Extra context data
     */
    public static function log(string $action, ?string $description = null, ?Model $subject = null, array $properties = []): ?ActivityLog
    {
        $organizationId = self::resolveOrganizationId($subject);

        if ($subject && ! $organizationId && $subject instanceof ActivityLog) {
            return null;
        }

        return ActivityLog::create([
            'organization_id' => $organizationId,
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties ?: null,
            'ip_address' => request()?->ip(),
            'user_agent' => substr((string) (request()?->userAgent() ?? ''), 0, 255),
        ]);
    }

    /**
     * Resolve the tenant for the log entry: the bound tenant context, the subject's
     * organization, or null (platform-level event such as a login).
     */
    protected static function resolveOrganizationId(?Model $subject): ?int
    {
        if (app()->bound('tenant.organization_id')) {
            return app('tenant.organization_id');
        }

        if ($subject && isset($subject->organization_id)) {
            return (int) $subject->organization_id;
        }

        return null;
    }
}
