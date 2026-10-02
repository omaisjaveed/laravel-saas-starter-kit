<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InviteMemberRequest;
use App\Http\Requests\Api\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Http\Traits\ApiResponse;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\RoleChangedNotification;
use App\Notifications\UserInvitedToOrganization;
use App\Notifications\UserRemovedFromOrganization;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    use ApiResponse;

    /**
     * List organization members.
     */
    public function index(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $members = $organization->users()
            ->when($request->input('q'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%");
                });
            })
            ->orderBy('users.name')
            ->paginate(15);

        return MemberResource::collection($members);
    }

    /**
     * Invite / add a user to the organization.
     */
    public function store(InviteMemberRequest $request, Organization $organization): JsonResponse
    {
        $this->authorize('manageMembers', $organization);

        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if ($user && $user->belongsToOrganization($organization->id)) {
            return $this->error('This user is already a member of the organization.', 409);
        }

        $isNew = ! $user;

        if (! $user) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(32)),
            ]);
            ActivityLogger::log('user.created', "User created: {$user->email}", $user);
        }

        $organization->users()->attach($user->id, ['role' => $data['role']]);

        $user->notify(new UserInvitedToOrganization($organization, $data['role']));

        ActivityLogger::log(
            'user.invited',
            ($isNew ? 'Invited new user ' : 'Added existing user ').$user->email." to {$organization->name} as {$data['role']}",
            $user,
            ['organization_id' => $organization->id, 'role' => $data['role']]
        );

        return $this->success(
            new MemberResource($organization->users()->findOrFail($user->id)),
            $isNew ? 'Invitation sent.' : 'User added.',
            201
        );
    }

    /**
     * Update a member (name and/or role).
     */
    public function update(UpdateMemberRequest $request, Organization $organization, int $member): JsonResponse
    {
        $this->authorize('manageMembers', $organization);

        $member = $organization->users()->findOrFail($member);
        $oldRole = $member->pivot->role;
        $data = $request->validated();

        if ($oldRole === 'owner') {
            return $this->error('The owner role cannot be changed.', 403);
        }

        $member->update(['name' => $data['name']]);
        $organization->users()->updateExistingPivot($member->id, ['role' => $data['role']]);

        if ($oldRole !== $data['role']) {
            $user = User::find($member->id);

            $user->notify(new RoleChangedNotification($organization, $data['role']));

            ActivityLogger::log('user.role_changed', "Role changed for {$user->email}: {$oldRole} → {$data['role']}", $user, [
                'organization_id' => $organization->id,
                'old_role' => $oldRole,
                'new_role' => $data['role'],
            ]);
        } else {
            ActivityLogger::log('user.updated', "Member updated: {$data['name']}", User::find($member->id));
        }

        return $this->success(
            new MemberResource($organization->users()->findOrFail($member->id)),
            'Member updated.'
        );
    }

    /**
     * Remove a member from the organization.
     */
    public function destroy(Request $request, Organization $organization, int $member): JsonResponse
    {
        $this->authorize('manageMembers', $organization);

        $member = $organization->users()->findOrFail($member);

        if ($member->pivot->role === 'owner') {
            return $this->error('The owner cannot be removed from the organization.', 403);
        }

        if ($member->pivot->role === 'admin' && ! $request->user()->isOwnerOf($organization)) {
            return $this->error('Only the owner can remove an administrator.', 403);
        }

        $organization->users()->detach($member->id);

        $member->notify(new UserRemovedFromOrganization($organization));

        ActivityLogger::log('user.removed', "User removed from {$organization->name}: {$member->email}", $member, [
            'organization_id' => $organization->id,
        ]);

        return $this->success(null, 'User removed from the organization.');
    }
}
