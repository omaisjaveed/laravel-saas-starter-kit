@extends('layouts.app')
@section('title', 'Profile')
@section('page_title', 'My Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="avatar-sm" style="width:64px;height:64px;font-size:1.5rem;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    <div>
                        <h1 class="h5 mb-0">{{ $user->name }}</h1>
                        <span class="text-muted small">{{ $user->email }}</span>
                        @if ($user->isSuperAdmin())
                            <span class="badge bg-danger ms-1">Platform admin</span>
                        @endif
                        @if ($user->email_verified_at)
                            <span class="badge bg-success ms-1">Verified</span>
                        @else
                            <span class="badge bg-warning ms-1">Not verified</span>
                        @endif
                    </div>
                </div>

                <h2 class="h6 mb-3">Profile information</h2>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Save</button>
                </form>

                <hr class="my-4">

                <h2 class="h6 mb-3">Change password</h2>
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="current_password" class="form-label">Current password</label>
                            <input id="current_password" type="password" name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="password" class="form-label">New password</label>
                            <input id="password" type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="bi bi-key me-1"></i> Update password</button>
                </form>
            </div>
        </div>

        {{-- My organizations --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom"><i class="bi bi-building me-1"></i> My organizations</div>
            <div class="card-body p-0">
                @forelse ($user->organizations()->orderBy('name')->get() as $org)
                    <a href="{{ route('organizations.show', $org) }}" class="d-flex align-items-center justify-content-between text-decoration-none text-reset border-bottom px-3 py-2">
                        <span class="small fw-semibold">{{ $org->name }}</span>
                        <span class="badge bg-light text-dark border">{{ $org->pivot->role }}</span>
                    </a>
                @empty
                    <div class="p-3 text-muted small text-center">You are not a member of any organization</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
