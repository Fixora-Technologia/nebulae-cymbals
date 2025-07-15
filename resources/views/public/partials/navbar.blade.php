<header class="fixed-top">
    <!-- Top Bar -->
    <div class="top-bar py-1 bg-light border-bottom">
        <div class="container-fluid container-lg">
            <div class="d-flex justify-content-between align-items-center">
                <div class="contact-item">
                    <a href="tel:+1234567890" class="text-decoration-none text-body small">
                        <i class="bi bi-telephone text-primary me-1"></i> +1-234-567-890
                    </a>
                </div>
                <div class="ms-auto">
                    <div id="google_translate_element" class="d-inline-block"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary py-2">
        <div class="container-fluid container-lg">
            <!-- Brand -->
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/images/logo-nebulae-full.png') }}" alt="Nebulae Cymbals" height="50"
                    class="d-inline-block align-text-top">
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item mx-2">
                            <a class="nav-link fs-5" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item mx-2">
                            <a class="nav-link fs-5" href="{{ route('register') }}">Register</a>
                        </li>
                    @endguest
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu border-0 shadow" aria-labelledby="navbarDropdown">
                                @can('DASHBOARD')
                                    <li><a class="dropdown-item" href="{{ route('mindo.home') }}">Dashboard</a></li>
                                @endcan
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                </li>
                            </ul>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>

{{-- No spacer needed - using proper CSS in public-app.scss instead --}}
