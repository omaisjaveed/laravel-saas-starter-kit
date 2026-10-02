<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Roles & Permissions overview page.
     */
    public function index(Request $request)
    {
        $roles = [
            [
                'name' => 'Owner',
                'slug' => 'owner',
                'color' => 'danger',
                'description' => 'Full control of the organization: settings, deletion, members and billing.',
                'capabilities' => [
                    'View organization', 'Update organization settings', 'Upload organization logo',
                    'Delete organization', 'Invite users', 'Change member roles', 'Remove users',
                    'Create projects', 'Update projects', 'Delete projects',
                    'Create tasks', 'Update tasks', 'Delete tasks',
                ],
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'color' => 'warning',
                'description' => 'Manages members and content, but cannot delete the organization or the owner.',
                'capabilities' => [
                    'View organization', 'Update organization settings', 'Upload organization logo',
                    'Invite users', 'Change member roles', 'Remove users (except owner & admins)',
                    'Create projects', 'Update projects', 'Delete projects',
                    'Create tasks', 'Update tasks', 'Delete tasks',
                ],
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'color' => 'info',
                'description' => 'Manages projects and tasks, views members, but cannot manage organization members.',
                'capabilities' => [
                    'View organization', 'View members',
                    'Create projects', 'Update projects', 'Delete projects',
                    'Create tasks', 'Update tasks', 'Delete tasks',
                ],
            ],
            [
                'name' => 'Member',
                'slug' => 'member',
                'color' => 'secondary',
                'description' => 'Works on projects and assigned tasks within the organization.',
                'capabilities' => [
                    'View organization', 'View members',
                    'View projects', 'View tasks', 'Update assigned tasks',
                ],
            ],
        ];

        $organization = \App\Support\CurrentOrganization::get();

        $users = User::count();

        return view('roles.index', compact('roles', 'organization', 'users'));
    }
}
