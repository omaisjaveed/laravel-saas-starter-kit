@extends('layouts.app')
@section('title', 'Organization Settings')
@section('page_title', $organization->name.' — Settings')

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom"><i class="bi bi-gear me-1"></i> Organization settings</div>
            <div class="card-body">
                <form method="POST" action="{{ route('organizations.update', $organization) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name', $organization->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $organization->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Contact email</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $organization->email) }}"
                                   class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input id="website" type="url" name="website" value="{{ old('website', $organization->website) }}"
                                   class="form-control @error('website') is-invalid @enderror" placeholder="https://example.com">
                            @error('website')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Save changes
                    </button>
                </form>
            </div>
        </div>

        @can('delete', $organization)
            <div class="card border-danger shadow-sm mb-4">
                <div class="card-header bg-danger bg-opacity-10 text-danger border-bottom"><i class="bi bi-exclamation-triangle me-1"></i> Danger zone</div>
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <strong>Delete this organization</strong>
                        <p class="text-muted small mb-0">This will remove the organization and all its data. This action cannot be undone.</p>
                    </div>
                    <form method="POST" action="{{ route('organizations.destroy', $organization) }}"
                          onsubmit="return confirm('Are you sure you want to delete this organization? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Delete</button>
                    </form>
                </div>
            </div>
        @endcan
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom"><i class="bi bi-image me-1"></i> Organization logo</div>
            <div class="card-body text-center">
                @if ($organization->logo_path)
                    <img src="{{ asset(\Illuminate\Support\Facades\Storage::url($organization->logo_path)) }}"
                         class="org-logo-lg mb-3" alt="{{ $organization->name }}">
                @else
                    <span class="stat-icon bg-primary bg-opacity-10 text-primary mb-3" style="width:96px;height:96px;font-size:2.5rem;border-radius:1rem;">
                        <i class="bi bi-building"></i>
                    </span>
                @endif

                <form method="POST" action="{{ route('organizations.logo', $organization) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="logo" id="logo"
                               class="form-control @error('logo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp">
                        @error('logo')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <div class="form-text">JPG, PNG or WEBP. Max 2MB.</div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-upload me-1"></i> Upload logo
                    </button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom"><i class="bi bi-info-circle me-1"></i> Details</div>
            <div class="card-body small">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Slug</span><span>{{ $organization->slug }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Owner</span><span>{{ $organization->owner?->name ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Created</span><span>{{ $organization->created_at->format('M j, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
