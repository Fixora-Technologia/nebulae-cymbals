@extends('public.layouts.app')

@section('title', 'Welcome to Nebulae Cymbals')
@section('meta_description',
    'Nebulae Cymbals is a premium cymbal manufacturer specializing in handcrafted, high-quality
    cymbals for professional musicians and enthusiasts.')

@section('content')
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Left side with image and overlay -->
            <div class="col-lg-6 position-relative d-none d-lg-block">
                <div class="welcome-banner"
                    style="background-image: url('{{ asset('assets/images/cymbal-bg.jpg') }}'); height: 100vh; background-size: cover; background-position: center;">
                    <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-white"
                        style="background-color: rgba(0, 0, 0, 0.6);">
                        <img src="{{ asset('assets/images/logo-nebulae-full.png') }}" alt="Nebulae Cymbals"
                            class="img-fluid mb-4" style="max-height: 120px;">
                        {{-- <h1 class="display-4 fw-bold mb-3">NEBULAE CYMBALS</h1> --}}
                        {{-- <p class="lead px-5 text-center">Premium handcrafted cymbals for professional musicians and enthusiasts</p> --}}
                    </div>
                </div>
            </div>

            <!-- Right side with content -->
            <div class="col-lg-6">
                <div class="d-flex flex-column justify-content-between h-100 p-4 p-md-5" style="min-height: 100vh;">
                    <!-- Logo for mobile only -->
                    <div class="text-center mb-4 d-lg-none">
                        <img src="{{ asset('assets/images/logo-nebulae-full.png') }}" alt="Nebulae Cymbals"
                            class="img-fluid mb-3" style="max-height: 80px;">
                        <h1 class="h2 fw-bold">NEBULAE CYMBALS</h1>
                    </div>

                    <div class="welcome-content my-auto p-5">
                        <h2 class="h3 fw-bold mb-4">Welcome to Nebulae Cymbals</h2>

                        <div class="mb-5">
                            {{-- <p class="mb-4">Nebulae cymbal adalah salah satu merk cymbal produk di dunia yang merupakan
                                merk cymbal yang dibuat di Indonesia. Tahun 1998, kami mulai membuat cymbals berbahan dasar
                                Kuningan (CuZn35) dan unsur lainnya.</p> --}}

                            <div class="d-grid gap-3">
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Login</a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary">Register</a>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="company-info pt-4 border-top">
                        <h5 class="fw-bold mb-3">Contact Information</h5>
                        <p class="mb-2">Jl. Kasuari No.5 Bandung - 40184</p>
                        <p class="mb-2"><a href="mailto:nebulaecymbal@gmail.com"
                                class="text-decoration-none">nebulaecymbal@gmail.com</a></p>
                        <p class="mb-0">(022) - 6032332, WA 628156249909, WA 628112032470</p>
                    </div> --}}
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

        .welcome-banner {
            box-shadow: inset 0 0 0 2000px rgba(25, 56, 94, 0.3);
        }

        .company-info {
            font-size: 0.9rem;
        }

        @media (max-width: 991.98px) {
            .welcome-content {
                padding-top: 2rem;
                padding-bottom: 2rem;
            }
        }
    </style>
@endpush
