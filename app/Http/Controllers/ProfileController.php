<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $emailChanged = $data['email'] !== $user->email;

        $user->update($data);

        if ($emailChanged && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
            $user->email_verified_at = null;
            $user->save();
            $user->sendEmailVerificationNotification();
        }

        ActivityLogger::log('profile.updated', 'Profile updated: '.$user->email, $user);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', PasswordRule::min(8), 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        ActivityLogger::log('profile.password_changed', 'Password changed: '.$user->email, $user);

        return redirect()->route('profile.edit')->with('success', 'Password updated successfully.');
    }

    /**
     * Display the public profile of a user (within current organization context).
     */
    public function show(Request $request, User $user)
    {
        $organization = \App\Support\CurrentOrganization::get();

        if ($organization) {
            abort_unless(
                $request->user()->isSuperAdmin() || $organization->users()->where('users.id', $user->id)->exists(),
                404
            );
        }

        $role = $organization ? $user->roleIn($organization) : null;

        return view('profile.show', compact('user', 'organization', 'role'));
    }
}
