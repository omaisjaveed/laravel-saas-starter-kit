@extends('layouts.guest')
@section('title', 'Register')

@section('content')
<h2 class="h5 mb-3">Create account</h2>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}"
               class="form-control @error('name') is-invalid @enderror" required autofocus autocomplete="name">
        @error('name')
            <span class="invalid-feedback" role="alert">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="form-control @error('email') is-invalid @enderror" required autocomplete="email">
        @error('email')
            <span class="invalid-feedback" role="alert">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" name="password"
               class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
        @error('password')
            <span class="invalid-feedback" role="alert">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm password</label>
        <input id="password_confirmation" type="password" name="password_confirmation"
               class="form-control" required autocomplete="new-password">
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Register
        </button>
    </div>

    <div class="mt-3 small">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </div>
</form>
@endsection
