@extends('layouts.guest')
@section('title', 'Login')

@section('content')
<h2 class="h5 mb-3">Sign in</h2>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="username">
        @error('email')
            <span class="invalid-feedback" role="alert">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" name="password"
               class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
        @error('password')
            <span class="invalid-feedback" role="alert">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" id="remember">
        <label class="form-check-label" for="remember">Remember me</label>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-box-arrow-in-right me-1"></i> Login
        </button>
    </div>

    <div class="d-flex justify-content-between mt-3 small">
        <a href="{{ route('password.request') }}">Forgot password?</a>
        <a href="{{ route('register') }}">Create account</a>
    </div>
</form>
@endsection
