@extends('layouts.app')
@section('title', 'Activity Logs')
@section('page_title', 'Activity Logs')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <h1 class="h4 mb-0">Activity logs</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <form method="GET" action="{{ route('activity.index') }}" class="row g-2">
            @if ($organizations->isNotEmpty())
                <div class="col-sm-4 col-md-3">
                    <select name="organization" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All organizations</option>
                        @foreach ($organizations as $org)
                            <option value="{{ $org->id }}" {{ request('organization') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-sm-4 col-md-3">
                <select name="action" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All actions</option>
                    @foreach (['user.login', 'user.logout', 'user.registered', 'user.created', 'user.invited', 'user.removed', 'user.role_changed', 'organization.created', 'organization.updated', 'organization.deleted', 'project.created', 'project.updated', 'project.deleted', 'task.created', 'task.updated', 'task.deleted', 'profile.updated'] as $action)
                        <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Action</th>
                    <th>Description</th>
                    <th>User</th>
                    <th>Organization</th>
                    <th>IP</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>
                            <span class="badge {{ str_starts_with($log->action, 'user.') ? 'bg-info' : (str_starts_with($log->action, 'organization.') ? 'bg-danger' : (str_starts_with($log->action, 'project.') ? 'bg-primary' : 'bg-success')) }} bg-opacity-75">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="small">{{ $log->description ?? '—' }}</td>
                        <td class="small">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="small">{{ $log->organization?->name ?? 'Platform' }}</td>
                        <td class="text-muted small">{{ $log->ip_address ?? '—' }}</td>
                        <td class="text-muted small" title="{{ $log->created_at->format('M j, Y H:i:s') }}">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No activity recorded</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $logs->links() }}
</div>
@endsection
