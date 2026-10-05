<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Demo password for all seeded accounts.
     */
    public const DEMO_PASSWORD = 'password';

    /**
     * Seed demo data (idempotent: safe to re-run):
     *
     * - 1 platform super admin
     * - Organization A (Acme Inc): Owner, Admin, Manager, Member + projects + tasks
     * - Organization B (Globex): Owner, Member + projects + tasks (for tenant isolation testing)
     * - Activity log entries
     */
    public function run()
    {
        // ------------------------------------------------------------------
        // Platform super admin (sees global platform dashboard)
        // ------------------------------------------------------------------
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@saas.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make(self::DEMO_PASSWORD),
                'is_super_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // ------------------------------------------------------------------
        // Organization A: Acme Inc
        // ------------------------------------------------------------------
        $acmeOwner = $this->createUser('Amelia Owner', 'owner@acme.test');
        $acmeAdmin = $this->createUser('Aaron Admin', 'admin@acme.test');
        $acmeManager = $this->createUser('Mia Manager', 'manager@acme.test');
        $acmeMember = $this->createUser('Max Member', 'member@acme.test');

        $acme = Organization::firstOrCreate(
            ['name' => 'Acme Inc'],
            [
                'description' => 'Demo organization A: a software development company.',
                'email' => 'hello@acme.test',
                'website' => 'https://acme.test',
                'owner_id' => $acmeOwner->id,
            ]
        );

        $this->attachMember($acme, $acmeOwner, 'owner');
        $this->attachMember($acme, $acmeAdmin, 'admin');
        $this->attachMember($acme, $acmeManager, 'manager');
        $this->attachMember($acme, $acmeMember, 'member');

        $websiteRedesign = $this->createProject($acme, 'Website Redesign', 'Complete redesign of the company website with a modern look.', 'active', $acmeManager);
        $mobileApp = $this->createProject($acme, 'Mobile App', 'Cross-platform mobile application for customers.', 'active', $acmeOwner);
        $marketingCampaign = $this->createProject($acme, 'Marketing Campaign Q4', 'Planned marketing activities for the fourth quarter.', 'archived', $acmeAdmin);

        $this->createTask($acme, $websiteRedesign, 'Design new homepage', 'Create wireframes and high-fidelity design for the homepage.', 'done', 'high', $acmeMember, $acmeManager, now()->subDays(5));
        $this->createTask($acme, $websiteRedesign, 'Implement responsive layout', 'Build the responsive layout based on the approved design.', 'in_progress', 'high', $acmeMember, $acmeManager, now()->addDays(3));
        $this->createTask($acme, $websiteRedesign, 'Set up analytics', 'Install and configure web analytics.', 'todo', 'medium', $acmeAdmin, $acmeAdmin, now()->addDays(10));
        $this->createTask($acme, $mobileApp, 'Define app architecture', 'Choose the tech stack and define the app architecture.', 'done', 'high', $acmeManager, $acmeOwner, now()->subDays(2));
        $this->createTask($acme, $mobileApp, 'Build login screen', 'Implement the login and registration screens.', 'in_progress', 'medium', $acmeMember, $acmeManager, now()->addDays(7));
        $this->createTask($acme, $mobileApp, 'Prepare app store listing', 'Screenshots, description and metadata for the app stores.', 'todo', 'low', $acmeMember, $acmeOwner, now()->addDays(21));
        $this->createTask($acme, $marketingCampaign, 'Draft campaign brief', 'Write the campaign brief and goals.', 'done', 'medium', $acmeAdmin, $acmeAdmin, now()->subDays(20));

        // ------------------------------------------------------------------
        // Organization B: Globex (second tenant, for isolation testing)
        // ------------------------------------------------------------------
        $globexOwner = $this->createUser('Grace Globex', 'owner@globex.test');
        $globexMember = $this->createUser('Gina Globex', 'member@globex.test');

        $globex = Organization::firstOrCreate(
            ['name' => 'Globex'],
            [
                'description' => 'Demo organization B: a completely separate tenant.',
                'email' => 'hello@globex.test',
                'website' => 'https://globex.test',
                'owner_id' => $globexOwner->id,
            ]
        );

        $this->attachMember($globex, $globexOwner, 'owner');
        $this->attachMember($globex, $globexMember, 'member');

        $inventorySystem = $this->createProject($globex, 'Inventory System', 'Warehouse inventory management system.', 'active', $globexOwner);
        $customerPortal = $this->createProject($globex, 'Customer Portal', 'Self-service portal for customers.', 'active', $globexOwner);

        $this->createTask($globex, $inventorySystem, 'Map warehouse process', 'Document the current warehouse workflow.', 'done', 'high', $globexMember, $globexOwner, now()->subDays(3));
        $this->createTask($globex, $inventorySystem, 'Design inventory schema', 'Design the database schema for inventory items.', 'in_progress', 'medium', $globexMember, $globexOwner, now()->addDays(4));
        $this->createTask($globex, $customerPortal, 'Collect customer requirements', 'Interview key customers about portal features.', 'todo', 'medium', $globexMember, $globexOwner, now()->addDays(14));

        // ------------------------------------------------------------------
        // Activity log entries (only when the log is empty)
        // ------------------------------------------------------------------
        if (ActivityLog::count() === 0) {
            $entries = [
                [$superAdmin, null, 'user.login', 'User logged in: admin@saas.test'],
                [$acmeOwner, $acme, 'organization.created', 'Organization created: Acme Inc'],
                [$acmeAdmin, $acme, 'user.invited', 'Invited new user member@acme.test to Acme Inc as member'],
                [$acmeManager, $acme, 'project.created', 'Project created: Website Redesign'],
                [$acmeOwner, $acme, 'project.created', 'Project created: Mobile App'],
                [$acmeMember, $acme, 'task.updated', 'Task updated: Implement responsive layout'],
                [$globexOwner, $globex, 'organization.created', 'Organization created: Globex'],
                [$globexOwner, $globex, 'project.created', 'Project created: Inventory System'],
            ];

            foreach ($entries as [$user, $organization, $action, $description]) {
                ActivityLog::create([
                    'organization_id' => $organization?->id,
                    'user_id' => $user->id,
                    'action' => $action,
                    'description' => $description,
                    'ip_address' => '127.0.0.1',
                ]);
            }
        }
    }

    /**
     * Create (or fetch) a verified demo user.
     */
    protected function createUser(string $name, string $email): User
    {
        return User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::DEMO_PASSWORD),
                'email_verified_at' => now(),
            ]
        );
    }

    /**
     * Attach a user to an organization with a role (if not already a member).
     */
    protected function attachMember(Organization $organization, User $user, string $role): void
    {
        if (! $user->belongsToOrganization($organization->id)) {
            $organization->users()->attach($user->id, ['role' => $role]);
        }
    }

    /**
     * Create (or fetch) a demo project in an organization.
     */
    protected function createProject(Organization $organization, string $name, string $description, string $status, User $creator)
    {
        return $organization->projects()->firstOrCreate(
            ['name' => $name],
            ['description' => $description, 'status' => $status, 'created_by' => $creator->id]
        );
    }

    /**
     * Create (or fetch) a demo task in a project.
     */
    protected function createTask(Organization $organization, $project, string $title, string $description, string $status, string $priority, User $assignee, User $creator, $dueDate)
    {
        return $project->tasks()->firstOrCreate(
            ['project_id' => $project->id, 'title' => $title],
            [
                'organization_id' => $organization->id,
                'description' => $description,
                'status' => $status,
                'priority' => $priority,
                'assigned_to' => $assignee->id,
                'created_by' => $creator->id,
                'due_date' => $dueDate->toDateString(),
            ]
        );
    }
}
