@extends('public.layouts.app')

@section('title', 'Reset Password - Nebulae Cymbals')
@section('meta_description', 'Reset your password for the Nebulae Cymbals warehouse management system.')

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Left side with image and overlay - only visible on larger screens -->
            <div class="col-lg-6 position-relative d-none d-lg-block">
                <div class="reset-banner"
                    style="background-image: url('{{ asset('assets/images/cymbal-bg.jpg') }}'); height: 100vh; background-size: cover; background-position: center;">
                    <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-white"
                        style="background-color: rgba(0, 0, 0, 0.6);">
                        <img src="{{ asset('assets/images/logo-nebulae-full.png') }}" alt="Nebulae Cymbals"
                            class="img-fluid mb-4" style="max-height: 120px;">
                        <h1 class="display-5 fw-bold mb-3">NEBULAE CYMBALS</h1>
                        <p class="lead px-5 text-center">Warehouse Management System</p>
                    </div>
                </div>
            </div>

            <!-- Right side with reset password form -->
            <div class="col-lg-6">
                <div class="reset-container d-flex flex-column justify-content-center h-100 p-4 p-md-5"
                    style="min-height: 100vh;">
                    <!-- Logo for mobile only -->
                    <div class="text-center mb-5 d-lg-none">
                        <img src="{{ asset('assets/images/logo_blue.png') }}" alt="Nebulae Cymbals" class="img-fluid mb-3"
                            style="max-height: 80px;">
                        <h1 class="h2 fw-bold">NEBULAE CYMBALS</h1>
                    </div>

                    <div class="reset-form-container bg-white rounded-3 shadow-sm p-4 p-md-5 mx-auto"
                        style="max-width: 500px;">
                        <h2 class="h3 fw-bold mb-4 text-center">Reset Password</h2>

                        @if (session('status'))
                            <div class="alert alert-success mb-4" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <p class="text-muted mb-4">Enter your email address and we'll send you a link to reset your
                            password.</p>

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label fw-medium">Email Address</label>
                                <input id="email" type="email"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-primary btn-lg">Send Password Reset Link</button>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none">Back to Login</a>
                            </div>
                        </form>
                    </div>

                    <div class="company-info text-center mt-4">
                        <p class="mb-0 small text-muted">&copy; {{ date('Y') }} Nebulae Cymbals. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        body {
            overflow-x: hidden;
        }

        .reset-banner {
            box-shadow: inset 0 0 0 2000px rgba(25, 56, 94, 0.3);
        }

        .reset-form-container {
            border-radius: 10px;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-primary {
            border-radius: 5px;
        }
    </style>
@endpush
</div>
@endsection
