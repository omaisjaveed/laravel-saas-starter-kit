<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #1e293b;
        }
        body { background-color: #f1f5f9; min-height: 100vh; }
        .app-sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1030;
            overflow-y: auto;
        }
        .app-sidebar .brand {
            color: #fff;
            font-weight: 700;
            font-size: 1.15rem;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: .5rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .app-sidebar .nav-link {
            color: #cbd5e1;
            padding: .65rem 1.25rem;
            display: flex;
            align-items: center;
            gap: .65rem;
            font-size: .925rem;
        }
        .app-sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,.06); }
        .app-sidebar .nav-link.active { color: #fff; background: #3b82f6; }
        .app-main { margin-left: var(--sidebar-width); display: flex; flex-direction: column; min-height: 100vh; }
        .app-topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: .65rem 1.25rem;
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .avatar-sm {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #3b82f6;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: .85rem;
        }
        .stat-card .stat-icon {
            width: 48px; height: 48px;
            border-radius: .75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }
        .org-logo-lg {
            width: 96px; height: 96px;
            border-radius: 1rem;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #fff;
        }
        @media (max-width: 991.98px) {
            .app-sidebar { display: none; }
            .app-main { margin-left: 0; }
        }
        .table > :not(caption) > * > * { padding: .65rem .75rem; }
    </style>
</head>
<body>
@php
    $user = auth()->user();
    $currentOrganization = \App\Support\CurrentOrganization::get();
    $myOrganizations = $user ? $user->organizations()->orderBy('name')->get() : collect();
    $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
    $latestNotifications = $user ? $user->unreadNotifications()->limit(5)->get() : collect();
@endphp

<!-- Sidebar (desktop) -->
<aside class="app-sidebar d-none d-lg-block">
    <div class="brand">
        <i class="bi bi-boxes"></i>
        <span>{{ config('app.name') }}</span>
    </div>
    <nav class="nav flex-column mt-2">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('organizations.*') ? 'active' : '' }}" href="{{ route('organizations.index') }}">
            <i class="bi bi-building"></i> Organizations
        </a>
        @if ($currentOrganization)
            <a class="nav-link {{ request()->routeIs('organizations.projects.*') ? 'active' : '' }}" href="{{ route('organizations.projects.index', $currentOrganization) }}">
                <i class="bi bi-kanban"></i> Projects
            </a>
            <a class="nav-link {{ request()->routeIs('organizations.users.*') ? 'active' : '' }}" href="{{ route('organizations.users.index', $currentOrganization) }}">
                <i class="bi bi-people"></i> Members
            </a>
        @endif
        <a class="nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}" href="{{ route('roles.index') }}">
            <i class="bi bi-shield-lock"></i> Roles & Permissions
        </a>
        <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
            <i class="bi bi-bell"></i> Notifications
            @if ($unreadCount)
                <span class="badge bg-danger ms-auto">{{ $unreadCount }}</span>
            @endif
        </a>
        <a class="nav-link {{ request()->routeIs('activity.index') ? 'active' : '' }}" href="{{ route('activity.index') }}">
            <i class="bi bi-clock-history"></i> Activity Logs
        </a>
        @if ($currentOrganization)
            <a class="nav-link {{ request()->routeIs('organizations.edit') ? 'active' : '' }}" href="{{ route('organizations.edit', $currentOrganization) }}">
                <i class="bi bi-gear"></i> Settings
            </a>
        @endif
        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
            <i class="bi bi-person-circle"></i> Profile
        </a>
    </nav>
</aside>

<div class="app-main">
    <!-- Top navigation -->
    <nav class="app-topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                <i class="bi bi-list"></i>
            </button>
            <strong class="fs-6">@yield('page_title', 'Dashboard')</strong>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if ($user && ($myOrganizations->isNotEmpty() || $user->isSuperAdmin()))
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-building"></i>
                        <span class="d-none d-sm-inline">{{ $currentOrganization?->name ?? ($user->isSuperAdmin() ? 'Platform' : 'Select organization') }}</span>
                        <span class="d-sm-none">{{ Str::limit($currentOrganization?->name ?? 'Org', 10) }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach ($myOrganizations as $org)
                            <li>
                                <a class="dropdown-item {{ $currentOrganization?->id === $org->id ? 'active' : '' }}"
                                   href="{{ route('organizations.switch', $org) }}">
                                    {{ $org->name }}
                                    @if ($currentOrganization?->id === $org->id)
                                        <i class="bi bi-check2 float-end"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('organizations.create') }}"><i class="bi bi-plus-lg me-1"></i> New organization</a></li>
                    </ul>
                </div>
            @endif

            @if ($user)
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        @if ($unreadCount)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 320px;">
                        <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <strong class="small">Notifications</strong>
                            <a href="{{ route('notifications.index') }}" class="small">View all</a>
                        </div>
                        @forelse ($latestNotifications as $notification)
                            <a class="dropdown-item small border-bottom py-2" href="{{ route('notifications.index') }}">
                                <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong><br>
                                <span class="text-muted">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 60) }}</span>
                            </a>
                        @empty
                            <div class="p-3 text-muted small text-center">No unread notifications</div>
                        @endforelse
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn btn-sm btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <span class="avatar-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        <span class="d-none d-sm-inline">{{ $user->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                        @if ($user->isSuperAdmin())
                            <li><span class="dropdown-item-text small text-muted"><i class="bi bi-star me-1"></i>Platform admin</span></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </nav>

    <!-- Mobile sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" style="background: var(--sidebar-bg); width: 260px;">
        <div class="offcanvas-header border-bottom border-secondary">
            <span class="brand"><i class="bi bi-boxes text-white"></i> <span class="text-white">{{ config('app.name') }}</span></span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a class="nav-link {{ request()->routeIs('organizations.*') ? 'active' : '' }}" href="{{ route('organizations.index') }}"><i class="bi bi-building"></i> Organizations</a>
                @if ($currentOrganization)
                    <a class="nav-link" href="{{ route('organizations.projects.index', $currentOrganization) }}"><i class="bi bi-kanban"></i> Projects</a>
                    <a class="nav-link" href="{{ route('organizations.users.index', $currentOrganization) }}"><i class="bi bi-people"></i> Members</a>
                @endif
                <a class="nav-link" href="{{ route('roles.index') }}"><i class="bi bi-shield-lock"></i> Roles & Permissions</a>
                <a class="nav-link" href="{{ route('notifications.index') }}"><i class="bi bi-bell"></i> Notifications</a>
                <a class="nav-link" href="{{ route('activity.index') }}"><i class="bi bi-clock-history"></i> Activity Logs</a>
                @if ($currentOrganization)
                    <a class="nav-link" href="{{ route('organizations.edit', $currentOrganization) }}"><i class="bi bi-gear"></i> Settings</a>
                @endif
                <a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i> Profile</a>
            </nav>
        </div>
    </div>

    <!-- Page content -->
    <main class="p-3 p-lg-4 flex-grow-1">
        @include('partials.alerts')
        @yield('content')
    </main>

    <footer class="text-center text-muted small py-3 border-top bg-white">
        {{ config('app.name') }} &middot; Laravel SaaS Starter Kit
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
