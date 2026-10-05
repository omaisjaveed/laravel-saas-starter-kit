@extends('layouts.app')
@section('title', 'Roles & Permissions')
@section('page_title', 'Roles & Permissions')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h1 class="h4 mb-0">Roles & Permissions</h1>
        <span class="text-muted small">
            @if ($organization)
                Showing capabilities for <strong>{{ $organization->name }}</strong>
            @else
                Roles apply per organization
            @endif
        </span>
    </div>
    @if ($organization)
        @can('manageMembers', $organization)
            <a href="{{ route('organizations.users.index', $organization) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-people me-1"></i> Manage members
            </a>
        @endcan
    @endif
</div>

<div class="row g-3">
    @foreach ($roles as $role)
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-{{ $role['color'] }} fs-6">{{ $role['name'] }}</span>
                    </div>
                    <p class="text-muted small">{{ $role['description'] }}</p>
                    <ul class="list-unstyled small mb-0">
                        @foreach ($role['capabilities'] as $capability)
                            <li class="py-1 border-bottom"><i class="bi bi-check2 text-success me-2"></i>{{ $capability }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-bottom"><i class="bi bi-info-circle me-1"></i> How it works</div>
    <div class="card-body small text-muted">
        <p class="mb-2">
            Roles are stored per organization on the <code>organization_user</code> pivot table, so the same user
            can be an <strong>Owner</strong> in one organization and a <strong>Member</strong> in another.
            Authorization is enforced with Laravel Policies (OrganizationPolicy, ProjectPolicy, TaskPolicy)
            and the <code>organization</code> middleware ensures users can only access organizations they belong to.
        </p>
        <p class="mb-0">
            Tenant isolation: models with an <code>organization_id</code> use a global scope bound to the current
            organization context, all lookups are performed through the parent organization relationship, and
            <code>organization_id</code> is never trusted from client input.
        </p>
    </div>
</div>
@endsection
