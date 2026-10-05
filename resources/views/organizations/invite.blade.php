@extends('layouts.app')
@section('title', 'Invite User')
@section('page_title', $organization->name.' — Invite user')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <i class="bi bi-person-plus me-1"></i> Add / invite user to {{ $organization->name }}
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('organizations.users.store', $organization) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror" required autofocus>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <div class="form-text">If the user does not exist, an account will be created and an invitation email sent.</div>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="member" {{ old('role') === 'member' ? 'selected' : '' }}>Member — works on projects and assigned tasks</option>
                            <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager — manages projects and tasks</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin — manages members and content</option>
                        </select>
                        @error('role')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-envelope-plus me-1"></i> Send invitation
                        </button>
                        <a href="{{ route('organizations.users.index', $organization) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
