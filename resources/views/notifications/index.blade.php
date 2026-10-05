@extends('layouts.app')
@section('title', 'Notifications')
@section('page_title', 'Notifications')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <h1 class="h4 mb-0">Notifications</h1>
    @if ($unreadCount)
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-check2-all me-1"></i> Mark all as read
            </button>
        </form>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @forelse ($notifications as $notification)
            <div class="d-flex align-items-start gap-3 border-bottom px-3 py-3 {{ $notification->read_at ? '' : 'bg-primary bg-opacity-10' }}">
                <span class="stat-icon {{ $notification->read_at ? 'bg-light text-secondary' : 'bg-primary bg-opacity-25 text-primary' }}"
                      style="width:40px;height:40px;font-size:1.1rem;">
                    @switch($notification->data['type'] ?? '')
                        @case('invitation')<i class="bi bi-envelope-plus"></i>@break
                        @case('removal')<i class="bi bi-envelope-x"></i>@break
                        @case('role_changed')<i class="bi bi-shield-check"></i>@break
                        @default<i class="bi bi-bell"></i>
                    @endswitch
                </span>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <strong class="small">{{ $notification->data['title'] ?? 'Notification' }}</strong>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="small text-muted mb-1">{{ $notification->data['message'] ?? '' }}</p>
                    @if (! $notification->read_at)
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary py-0">Mark as read</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-muted p-5">
                <i class="bi bi-bell-slash" style="font-size: 2.5rem;"></i>
                <p class="mt-3 mb-0">No notifications yet.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="mt-4">
    {{ $notifications->links() }}
</div>
@endsection
