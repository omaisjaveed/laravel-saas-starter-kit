<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrganizationRequest;
use App\Http\Requests\Api\UpdateOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Http\Traits\ApiResponse;
use App\Models\Organization;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    use ApiResponse;

    /**
     * List the authenticated user's organizations.
     */
    public function index(Request $request): JsonResponse
    {
        $organizations = Organization::forUser($request->user())
            ->withCount(['users', 'projects', 'tasks'])
            ->latest()
            ->paginate(15);

        return OrganizationResource::collection($organizations);
    }

    /**
     * Create a new organization owned by the authenticated user.
     */
    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $organization = Organization::create(
            $request->validated() + ['owner_id' => $request->user()->id]
        );

        $request->user()->organizations()->attach($organization->id, ['role' => 'owner']);

        ActivityLogger::log('organization.created', 'Organization created: '.$organization->name, $organization);

        return $this->success(
            new OrganizationResource($organization),
            'Organization created.',
            201
        );
    }

    /**
     * Display an organization.
     */
    public function show(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        return $this->success(
            new OrganizationResource($organization->loadCount(['users', 'projects', 'tasks']))
        );
    }

    /**
     * Update an organization.
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $organization->update($request->validated());

        ActivityLogger::log('organization.updated', 'Organization updated: '.$organization->name, $organization, [
            'changed' => array_keys($request->validated()),
        ]);

        return $this->success(new OrganizationResource($organization->fresh()), 'Organization updated.');
    }

    /**
     * Delete an organization (owner only).
     */
    public function destroy(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('delete', $organization);

        $organization->users()->detach();
        $organization->delete();

        ActivityLogger::log('organization.deleted', 'Organization deleted: '.$organization->name, $organization);

        return $this->success(null, 'Organization deleted.');
    }
}
