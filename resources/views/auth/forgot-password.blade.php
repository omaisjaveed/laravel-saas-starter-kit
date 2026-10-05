@extends('layouts.guest')
@section('title', 'Forgot password')

@section('content')
<h2 class="h5 mb-1">Forgot password</h2>
<p class="text-muted small">Enter your email and we will send you a reset link.</p>

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="form-control @error('email') is-invalid @enderror" required autofocus>
        @error('email')
            <span class="invalid-feedback" role="alert">{{ $message }}</span>
        @enderror
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-envelope me-1"></i> Send reset link
        </button>
    </div>

    <div class="mt-3 small">
        <a href="{{ route('login') }}">Back to login</a>
    </div>
</form>
@endsection
