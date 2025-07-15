@extends('public.layouts.app')

@section('title', 'Register - Nebulae Cymbals')
@section('meta_description', 'Register for the Nebulae Cymbals warehouse management system.')

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Left side with image and overlay - only visible on larger screens -->
            <div class="col-lg-5 position-relative d-none d-lg-block">
                <div class="register-banner"
                    style="background-image: url('{{ asset('assets/images/cymbal-bg.jpg') }}'); min-height: 100vh; background-size: cover; background-position: center;">
                    <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-white"
                        style="background-color: rgba(0, 0, 0, 0.6);">
                        <img src="{{ asset('assets/images/logo-nebulae-full.png') }}" alt="Nebulae Cymbals"
                            class="img-fluid mb-4" style="max-height: 120px;">
                        <h1 class="display-5 fw-bold mb-3">NEBULAE CYMBALS</h1>
                        <div class="px-4 text-center">
                            <h5 class="fw-bold mb-3">Premium Quality Cymbals</h5>
                            <p class="mb-4">At Nebulae Cymbals, we craft premium quality cymbals with meticulous attention
                                to detail. Our products are designed for professional musicians and enthusiasts who demand
                                exceptional sound quality and durability.</p>

                            <h5 class="fw-bold mb-3">Handcrafted Excellence</h5>
                            <p>Our cymbals are handcrafted by skilled artisans with decades of experience in metallurgy and
                                sound engineering. We use only the finest materials to ensure consistent tone, resonance,
                                and longevity.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side with registration form -->
            <div class="col-lg-7">
                <div class="register-container p-4 p-md-5" style="min-height: 100vh;">
                    <!-- Logo for mobile only -->
                    <div class="text-center mb-4 d-lg-none">
                        <img src="{{ asset('assets/images/logo_blue.png') }}" alt="Nebulae Cymbals" class="img-fluid mb-3"
                            style="max-height: 80px;">
                        <h1 class="h2 fw-bold">NEBULAE CYMBALS</h1>
                    </div>

                    <div class="register-form-container bg-white rounded-3 shadow-sm p-4 p-md-5">
                        <h2 class="h3 fw-bold mb-4">Create an Account</h2>
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" id="registrationForm">
                            @csrf
                            <fieldset>
                                <legend class="fs-5 fw-bold mb-3">Company Information</legend>
                                {{-- Company Name --}}
                                <div class="row mb-3">
                                    <label for="company_name" class="col-md-4 col-form-label text-md-end">
                                        Company Name
                                    </label>
                                    <div class="col-md-6">
                                        <input type="text"
                                            class="form-control @error('company_name') is-invalid @enderror"
                                            id="company_name" name="company_name" value="{{ old('company_name') }}"
                                            required>
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Company Address --}}
                                <div class="row mb-3">
                                    <label for="company_address" class="col-md-4 col-form-label text-md-end">
                                        Company Address
                                    </label>
                                    <div class="col-md-6">
                                        <textarea class="form-control @error('company_address') is-invalid @enderror" id="company_address"
                                            name="company_address" rows="3" required>{{ old('company_address') }}</textarea>
                                        @error('company_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- City --}}
                                <div class="row mb-3">
                                    <label for="city" class="col-md-4 col-form-label text-md-end">
                                        City
                                    </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                                            id="city" name="city" value="{{ old('city') }}" required>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Postal Code --}}
                                <div class="row mb-3">
                                    <label for="postal_code" class="col-md-4 col-form-label text-md-end">
                                        Postal Code
                                    </label>
                                    <div class="col-md-6">
                                        <input type="text"
                                            class="form-control @error('postal_code') is-invalid @enderror" id="postal_code"
                                            name="postal_code" value="{{ old('postal_code') }}" required>
                                        @error('postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Phone Number --}}
                                <div class="row mb-3">
                                    <label for="phone_number" class="col-md-4 col-form-label text-md-end">
                                        Phone Number
                                    </label>
                                    <div class="col-md-6">
                                        <input type="text"
                                            class="form-control @error('phone_number') is-invalid @enderror"
                                            id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                                            required>
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Website --}}
                                <div class="row mb-3">
                                    <label for="website" class="col-md-4 col-form-label text-md-end">
                                        Website
                                    </label>
                                    <div class="col-md-6">
                                        <input type="url" class="form-control @error('website') is-invalid @enderror"
                                            id="website" name="website" value="{{ old('website') }}">
                                        @error('website')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="mt-4">
                                <legend class="fs-5 fw-bold mb-3">Contact Person Information</legend>
                                {{-- Contact Person --}}
                                <div class="row mb-3">
                                    <label for="contact_person" class="col-md-4 col-form-label text-md-end">
                                        Full Name
                                    </label>
                                    <div class="col-md-6">
                                        <input type="text"
                                            class="form-control @error('contact_person') is-invalid @enderror"
                                            id="contact_person" name="contact_person"
                                            value="{{ old('contact_person') }}" required>
                                        @error('contact_person')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Position --}}
                                <div class="row mb-3">
                                    <label for="position" class="col-md-4 col-form-label text-md-end">
                                        Position
                                    </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control @error('position') is-invalid @enderror"
                                            id="position" name="position" value="{{ old('position') }}" required>
                                        @error('position')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Mobile Number --}}
                                <div class="row mb-3">
                                    <label for="contact_mobile" class="col-md-4 col-form-label text-md-end">
                                        Mobile Number
                                    </label>
                                    <div class="col-md-6">
                                        <input type="text"
                                            class="form-control @error('contact_mobile') is-invalid @enderror"
                                            id="contact_mobile" name="contact_mobile"
                                            value="{{ old('contact_mobile') }}" required>
                                        @error('contact_mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset class="mt-4">
                                <legend class="fs-5 fw-bold mb-3">Account Information</legend>
                                {{-- Email --}}
                                <div class="row mb-3">
                                    <label for="email" class="col-md-4 col-form-label text-md-end">
                                        Email
                                    </label>
                                    <div class="col-md-6">
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" required autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Password --}}
                                <div class="row mb-3">
                                    <label for="password" class="col-md-4 col-form-label text-md-end">
                                        Password
                                    </label>
                                    <div class="col-md-6">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="new-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Password Confirmation --}}
                                <div class="row mb-3">
                                    <label for="password-confirm" class="col-md-4 col-form-label text-md-end">
                                        Confirm Password
                                    </label>
                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control"
                                            name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>
                                {{-- Terms and Conditions --}}
                                <div class="row mb-4">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="terms"
                                                id="terms" required>
                                            <label class="form-check-label" for="terms">
                                                I agree to the <a href="#" class="text-primary">Terms and
                                                    Conditions</a>
                                            </label>
                                            @error('terms')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                            Register
                                        </button>
                                        <div class="mt-3">
                                            Already have an account? <a href="{{ route('login') }}"
                                                class="text-primary">Login here</a>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            const form = document.getElementById('registrationForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    </script>
@endpush
