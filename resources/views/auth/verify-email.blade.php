@extends('layouts.guest')
@section('title', 'Verify email')

@section('content')
<h2 class="h5 mb-1">Verify your email</h2>
<p class="text-muted small mb-4">
    Thanks for signing up! Before getting started, could you verify your email address by clicking
    on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
</p>

@if (session('verification-link-sent'))
    <div class="alert alert-success small">A new verification link has been sent to your email address.</div>
@endif

<div class="d-grid gap-2">
    @if (session('resend_available', true))
        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-envelope-arrow-up me-1"></i> Resend verification email
            </button>
        </form>
    @endif

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-secondary w-100">Logout</button>
    </form>
</div>
@endsection
