<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Adds automatic multi-tenant isolation to models that have an organization_id column.
 *
 * - Global scope: whenever a tenant context is bound in the container (by the
 *   SetOrganizationContext / SetCurrentOrganization middleware), all queries are
 *   restricted to that organization. This protects against forgotten where() clauses.
 * - Creating hook: organization_id defaults to the current tenant when not set explicitly.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (app()->bound('tenant.organization_id')) {
                $builder->where(
                    $builder->getModel()->getTable().'.organization_id',
                    app('tenant.organization_id')
                );
            }
        });

        static::creating(function (Model $model) {
            if (app()->bound('tenant.organization_id') && blank($model->organization_id)) {
                $model->organization_id = app('tenant.organization_id');
            }
        });
    }
}
