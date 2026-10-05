@extends('layouts.app')
@section('title', 'Edit Member')
@section('page_title', $organization->name.' — Edit member')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <i class="bi bi-person-gear me-1"></i> Edit member
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('organizations.users.update', [$organization, $member->id]) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name', $member->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" value="{{ $member->email }}" class="form-control" disabled>
                        <div class="form-text">Email cannot be changed here.</div>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror"
                                {{ $member->pivot->role === 'owner' ? 'disabled' : '' }}>
                            <option value="member" {{ old('role', $member->pivot->role) === 'member' ? 'selected' : '' }}>Member</option>
                            <option value="manager" {{ old('role', $member->pivot->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="admin" {{ old('role', $member->pivot->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            @if ($member->pivot->role === 'owner')
                                <option value="owner" selected>Owner</option>
                            @endif
                        </select>
                        @error('role')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        @if ($member->pivot->role === 'owner')
                            <div class="form-text">The owner role cannot be changed.</div>
                        @endif
                    </div>

                    @if ($member->pivot->role === 'owner')
                        <input type="hidden" name="role" value="owner">
                        <div class="alert alert-warning small">
                            <i class="bi bi-shield-lock me-1"></i> This member is the owner. The owner role cannot be changed.
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" @if ($member->pivot->role === 'owner') disabled @endif>
                            <i class="bi bi-check-lg me-1"></i> Save changes
                        </button>
                        <a href="{{ route('organizations.users.index', $organization) }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
