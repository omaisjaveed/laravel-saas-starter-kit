@extends('layouts.app')
@section('title', 'Members')
@section('page_title', $organization->name.' — Members')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h1 class="h5 mb-0">Organization members</h1>
        <span class="text-muted small">{{ $members->total() }} members</span>
    </div>
    @if ($canManageMembers)
        <a href="{{ route('organizations.users.invite', $organization) }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Invite user
        </a>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <form method="GET" action="{{ route('organizations.users.index', $organization) }}" class="row g-2">
            <div class="col-sm-6 col-md-4">
                <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search by name or email...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                @if ($search)
                    <a href="{{ route('organizations.users.index', $organization) }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($members as $member)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar-sm">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                <a href="{{ route('users.show', $member) }}" class="text-decoration-none fw-semibold">{{ $member->name }}</a>
                            </div>
                        </td>
                        <td class="text-muted small">{{ $member->email }}</td>
                        <td>
                            <span class="badge bg-{{ ['owner' => 'danger', 'admin' => 'warning', 'manager' => 'info', 'member' => 'secondary'][$member->pivot->role] ?? 'secondary' }}">
                                {{ ucfirst($member->pivot->role) }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $member->pivot->created_at?->format('M j, Y') }}</td>
                        <td class="text-end">
                            @if ($canManageMembers)
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('organizations.users.edit', [$organization, $member->id]) }}"
                                       class="btn btn-sm btn-outline-secondary" title="Edit / change role">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if ($member->pivot->role !== 'owner')
                                        <form method="POST" action="{{ route('organizations.users.destroy', [$organization, $member->id]) }}"
                                              onsubmit="return confirm('Remove {{ $member->name }} from the organization?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove">
                                                <i class="bi bi-person-x"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No members found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $members->links() }}
</div>
@endsection
