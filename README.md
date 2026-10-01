# Laravel SaaS Starter Kit

![Laravel](https://img.shields.io/badge/Laravel-8.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Tests](https://img.shields.io/badge/Tests-70%20passing-22c55e?style=flat-square)
![Sanctum](https://img.shields.io/badge/API-Sanctum%20Tokens-F05340?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-3b82f6?style=flat-square)

A professional, reusable **SaaS Starter Kit** built with **Laravel 8** and **PHP 8.2**.
It provides authentication, multi-tenancy (organizations), roles & permissions,
projects & tasks as a demo module, activity logs, notifications, a versioned REST API
and a responsive admin dashboard — everything a new SaaS product needs to get started.

---

## Features

- **Authentication** — Login, Register, Logout, Forgot password, Reset password, Email verification
- **Multi-Tenancy (SaaS)** — Users belong to organizations/companies with strict tenant isolation
- **Roles & Permissions** — Owner, Admin, Manager, Member with Laravel Policies
- **User Management** — Users list, invite/add, edit, delete/remove, change role, user profile
- **Organization Management** — List, create, settings, members, logo upload, organization profile
- **Dashboard** — Real database statistics with separate platform-admin and organization views
- **Activity Logs** — Login, user created/invited/removed, role changed, organization/project/task CRUD
- **Notifications** — Laravel notifications (mail + database) for invitations and important events
- **REST API** — Versioned `/api/v1` with Sanctum tokens, API Resources, Form Requests, pagination
- **Projects & Tasks** — Demo SaaS module demonstrating how future modules should be built
- **Security** — Tenant isolation, policies, mass-assignment protection, CSRF, IDOR protection, file upload validation
- **Tests** — Feature tests for auth, tenant isolation, roles, projects, tasks and API authorization

---

## Requirements

- Windows / Linux / macOS
- PHP **8.2** (XAMPP supported)
- Composer **2.x**
- MySQL 5.7+ / MariaDB
- Laravel **8.x** (do not upgrade to 9/10/11/12)

---

## Installation

```bash
# 1. Install dependencies
composer install

# 2. Copy the environment file
copy .env.example .env        # Windows
# cp .env.example .env        # Linux/macOS

# 3. Generate the application key
php artisan key:generate

# 4. Link the public storage disk (organization logos)
php artisan storage:link
```

## `.env` Configuration

```env
APP_NAME="SaaS Starter Kit"
APP_ENV=local
APP_KEY=            # generated via php artisan key:generate
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=SAAS
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log     # emails are written to storage/logs/laravel.log locally
```

## Database Setup

```bash
# Create the database if it does not exist (MySQL CLI)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS SAAS CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed demo data
php artisan db:seed
```

> Note: migrations are never dropped/reset. The `SAAS` database is not dropped by this project.

## Demo Credentials

All demo accounts use the password: **`password`**

| Account | Email | Password | Role | Scope |
|---|---|---|---|---|
| Super Admin | `admin@saas.test` | `password` | Platform admin (global) | Sees the platform-wide dashboard |
| Amelia Owner | `owner@acme.test` | `password` | Owner of **Acme Inc** | Organization A |
| Aaron Admin | `admin@acme.test` | `password` | Admin of **Acme Inc** | Organization A |
| Mia Manager | `manager@acme.test` | `password` | Manager of **Acme Inc** | Organization A |
| Max Member | `member@acme.test` | `password` | Member of **Acme Inc** | Organization A |
| Grace Globex | `owner@globex.test` | `password` | Owner of **Globex** | Organization B |
| Gina Globex | `member@globex.test` | `password` | Member of **Globex** | Organization B |

Demo data includes: 2 organizations, 5 projects and 11 tasks with realistic
statuses, priorities and assignments, plus activity log entries.

---

## Running the Project

```bash
php artisan serve
```

Open http://127.0.0.1:8000 and log in with any demo account above.

---

## SaaS Architecture

```
User  ──belongs to──▶  Organization (tenant)
                          │
                          ├──▶ Members (users + role on pivot)
                          ├──▶ Projects
                          │       └──▶ Tasks (assignee, status, priority)
                          └──▶ Activity Logs
```

### Multi-Tenancy Explanation

Every user can belong to many organizations, with a role (`owner`, `admin`,
`manager`, `member`) stored on the `organization_user` pivot table — the same
user can be an **Owner** in one organization and a **Member** in another.

**Tenant isolation is enforced in four layers:**

1. **Middleware** — `SetOrganizationContext` resolves the `{organization}` route
   parameter (by slug), verifies membership and binds the tenant context. It is
   registered as the `organization` middleware alias.
2. **Relationships** — Projects/tasks are always looked up through their parent
   organization (`$organization->projects()->findOrFail($id)`), so records from
   another organization are simply *not found* (404).
3. **Scopes** — `BelongsToTenant` adds a global scope to tenant models: whenever
   the tenant context is bound, all queries are automatically restricted to that
   organization. It also defaults `organization_id` on create.
4. **Policies** — `OrganizationPolicy`, `ProjectPolicy`, `TaskPolicy` authorize
   every action per role.

**`organization_id` is never trusted from the frontend** — it is derived
server-side from the route parameter and validated tests prove client-supplied
values are ignored.

### Roles

| Role | Capabilities |
|---|---|
| **Owner** | Full control: settings, logo, deletion, members, projects, tasks |
| **Admin** | Settings, logo, members (except owner/admin removal), projects, tasks |
| **Manager** | Projects & tasks management, view members |
| **Member** | View projects/tasks, update assigned tasks |

---

## API Documentation

Base URL: `/api/v1` — authentication via **Laravel Sanctum** Bearer tokens.

### Authentication

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/v1/auth/login` | Login, returns `{ token, token_type, user }` |
| GET | `/api/v1/auth/me` | Current user with organizations (auth required) |
| POST | `/api/v1/auth/logout` | Revoke the current token (auth required) |

```bash
# Example: login
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Accept: application/json" \
  -d "email=owner@acme.test&password=password"

# Example: authenticated request
curl http://127.0.0.1:8000/api/v1/auth/me \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Accept: application/json"
```

### Organizations

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/v1/organizations` | List my organizations (paginated) |
| POST | `/api/v1/organizations` | Create organization (become owner) |
| GET | `/api/v1/organizations/{slug}` | Show organization |
| PUT | `/api/v1/organizations/{slug}` | Update (owner/admin) |
| DELETE | `/api/v1/organizations/{slug}` | Delete (owner only) |

### Members

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/v1/organizations/{slug}/members` | List members (paginated, `?q=` search) |
| POST | `/api/v1/organizations/{slug}/members` | Invite/add member (`name`, `email`, `role`) |
| PUT | `/api/v1/organizations/{slug}/members/{id}` | Update name/role |
| DELETE | `/api/v1/organizations/{slug}/members/{id}` | Remove member |

### Projects

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/v1/organizations/{slug}/projects` | List projects (paginated, `?q=&status=`) |
| POST | `/api/v1/organizations/{slug}/projects` | Create project |
| GET | `/api/v1/organizations/{slug}/projects/{id}` | Show project with tasks |
| PUT | `/api/v1/organizations/{slug}/projects/{id}` | Update project |
| DELETE | `/api/v1/organizations/{slug}/projects/{id}` | Delete project |

### Tasks

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/v1/organizations/{slug}/projects/{id}/tasks` | List tasks (`?status=&assigned_to=`) |
| POST | `/api/v1/organizations/{slug}/projects/{id}/tasks` | Create task (optionally `assigned_to`) |
| GET | `/api/v1/organizations/{slug}/projects/{id}/tasks/{taskId}` | Show task |
| PUT | `/api/v1/organizations/{slug}/projects/{id}/tasks/{taskId}` | Update task/status/assignment |
| DELETE | `/api/v1/organizations/{slug}/projects/{id}/tasks/{taskId}` | Delete task |

All endpoints use **API Resources**, **Form Requests**, **pagination**
(`meta`/`links` in resource collections) and proper HTTP status codes
(`200`, `201`, `401`, `403`, `404`, `409`, `422`) with a consistent JSON envelope:

```json
{
    "success": true,
    "message": "...",
    "data": { ... }
}
```

API endpoints respect tenant isolation exactly like the web routes.

---

## Testing Commands

```bash
php artisan test                 # full suite
php artisan test --testdox       # readable output
php artisan test tests/Feature/TenantIsolationTest.php   # tenant isolation only
```

The suite (70 tests) covers authentication, password reset, email verification,
organization access, tenant isolation (including IDOR attempts via URL IDs),
roles & permissions, projects, tasks and API authorization. Tests run against
in-memory SQLite (`phpunit.xml`) and never touch the `SAAS` database.

---

## Project Structure

```
app/
├── Console/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/              # Login, Register, Password reset, Verification
│   │   ├── Api/               # Versioned API controllers (Sanctum)
│   │   ├── DashboardController.php
│   │   ├── OrganizationController.php
│   │   ├── OrganizationMemberController.php
│   │   ├── ProjectController.php
│   │   ├── TaskController.php
│   │   ├── ProfileController.php
│   │   ├── RoleController.php
│   │   ├── NotificationController.php
│   │   └── ActivityLogController.php
│   ├── Middleware/
│   │   ├── SetOrganizationContext.php   # tenant isolation (route-based)
│   │   └── SetCurrentOrganization.php   # tenant isolation (session-based)
│   ├── Requests/Api/          # API Form Requests
│   ├── Resources/             # API Resources
│   ├── Traits/ApiResponse.php # consistent JSON envelope
│   └── Kernel.php
├── Models/
│   ├── Concerns/BelongsToTenant.php     # global tenant scope
│   ├── Concerns/HasOrganizationRoles.php
│   ├── Organization.php
│   ├── Project.php
│   ├── Task.php
│   ├── ActivityLog.php
│   └── User.php
├── Notifications/             # Invitation, removal, role changed
├── Policies/                  # Organization/Project/Task policies
├── Services/ActivityLogger.php
└── Support/CurrentOrganization.php

database/
├── migrations/                # organizations, org_user, projects, tasks, activity_logs, ...
└── seeders/DemoSeeder.php     # demo organizations, users, projects, tasks

resources/views/               # Blade + Bootstrap 5 (responsive admin UI)
routes/                        # web.php, api.php (/api/v1)
tests/                         # Feature tests (auth, tenancy, roles, API)
```

---

## Remaining Notes

- Emails use the `log` mailer locally (see `storage/logs/laravel.log`). Configure
  SMTP in `.env` for production.
- `php artisan storage:link` is required once so organization logos are publicly served.
- Some antivirus software (e.g. AVG Web Shield) blocks the default Laravel
  `server.php` router filename. This kit ships a custom `php artisan serve` command
  that uses `server-artisan.php` instead — no manual action is needed.
- The file `proxy-ca-bundle.pem` in the project root is a machine-specific CA bundle
  used by Composer on networks with SSL inspection (e.g. AVG Web Shield). It is safe
  to delete on machines without such software.

---

## Author

Built and maintained by **Omais Javeed**.

- Email: [omaisjaveed7@gmail.com](mailto:omaisjaveed7@gmail.com)
- Contributions, suggestions and feedback are welcome!

---

## License

This project is open-source software licensed under the [MIT License](LICENSE).
