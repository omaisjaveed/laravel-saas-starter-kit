@extends('layouts.app')
@section('title', 'User Profile')
@section('page_title', 'User Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar-sm" style="width:64px;height:64px;font-size:1.5rem;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    <div class="flex-grow-1">
                        <h1 class="h5 mb-0">{{ $user->name }}</h1>
                        <span class="text-muted small">{{ $user->email }}</span>
                    </div>
                    @if ($role)
                        <span class="badge bg-primary">{{ ucfirst($role) }}</span>
                    @endif
                </div>

                <hr class="my-4">

                <div class="row small">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Member since</span>
                            <span>{{ $user->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Email verified</span>
                            <span>{{ $user->email_verified_at ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if ($organization)
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Organization</span>
                                <a href="{{ route('organizations.show', $organization) }}" class="text-decoration-none">{{ $organization->name }}</a>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted">Role in organization</span>
                                <span>{{ $role ? ucfirst($role) : '—' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
