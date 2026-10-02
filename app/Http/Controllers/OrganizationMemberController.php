<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Notifications\RoleChangedNotification;
use App\Notifications\UserInvitedToOrganization;
use App\Notifications\UserRemovedFromOrganization;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationMemberController extends Controller
{
    /**
     * Display the organization members (users list).
     */
    public function index(Request $request, Organization $organization)
    {
        $this->authorize('view', $organization);

        $search = $request->input('q');

        $members = $organization->users()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%");
                });
            })
            ->orderBy('users.name')
            ->paginate(15)
            ->withQueryString();

        $canManageMembers = $request->user()->can('manageMembers', $organization);

        return view('organizations.members', compact('organization', 'members', 'canManageMembers', 'search'));
    }

    /**
     * Show the invite user form.
     */
    public function create(Request $request, Organization $organization)
    {
        $this->authorize('manageMembers', $organization);

        return view('organizations.invite', compact('organization'));
    }

    /**
     * Invite / add a user to the organization.
     */
    public function invite(Request $request, Organization $organization)
    {
        $this->authorize('manageMembers', $organization);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:admin,manager,member'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user && $user->belongsToOrganization($organization->id)) {
            return back()->withErrors(['email' => 'This user is already a member of the organization.']);
        }

        $isNew = ! $user;

        if (! $user) {
            // Create the account; the invited user sets a password via "Forgot password".
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

        return redirect()
            ->route('organizations.users.index', $organization)
            ->with('success', ($isNew ? 'Invitation sent to ' : 'User added: ').$user->email);
    }

    /**
     * Show the edit member form (change role / edit name).
     */
    public function edit(Request $request, Organization $organization, int $member)
    {
        $this->authorize('manageMembers', $organization);

        $member = $organization->users()->findOrFail($member);

        return view('organizations.edit-member', compact('organization', 'member'));
    }

    /**
     * Update a member (name and/or role).
     */
    public function update(Request $request, Organization $organization, int $member)
    {
        $this->authorize('manageMembers', $organization);

        $member = $organization->users()->findOrFail($member);
        $oldRole = $member->pivot->role;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:admin,manager,member'],
        ]);

        if ($oldRole === 'owner') {
            return back()->withErrors(['role' => 'The owner role cannot be changed. Transfer ownership first.']);
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

        return redirect()
            ->route('organizations.users.index', $organization)
            ->with('success', 'Member updated successfully.');
    }

    /**
     * Remove a user from the organization.
     */
    public function destroy(Request $request, Organization $organization, int $member)
    {
        $this->authorize('manageMembers', $organization);

        $member = $organization->users()->findOrFail($member);

        if ($member->pivot->role === 'owner') {
            return back()->withErrors(['user' => 'The owner cannot be removed from the organization.']);
        }

        if ($member->pivot->role === 'admin' && ! $request->user()->isOwnerOf($organization)) {
            return back()->withErrors(['user' => 'Only the owner can remove an administrator.']);
        }

        $organization->users()->detach($member->id);

        $member->notify(new UserRemovedFromOrganization($organization));

        ActivityLogger::log('user.removed', "User removed from {$organization->name}: {$member->email}", $member, [
            'organization_id' => $organization->id,
        ]);

        return redirect()
            ->route('organizations.users.index', $organization)
            ->with('success', 'User removed from the organization.');
    }
}
