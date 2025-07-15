<footer class="footer mt-auto py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4 d-flex flex-column gap-3 mb-3">
                <a href="#">
                    <img src="{{ asset('assets/images/logo_blue.png') }}" alt="Nebulae Cymbals" height="80">
                </a>
                <p class="text-left mt-2">&copy; Copyright <strong>Nebulae Cymbals</strong>. All rights reserved</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5 class="fw-bolder">CONTACT US</h5>
                <ul class="list-unstyled d-flex flex-column gap-2">
                    <li>
                        <i class="bi bi-geo-alt-fill"></i>
                        123 Cymbal Street, Suite 101, Music District, Percussion City, 12345
                    </li>
                    <li>
                        <i class="bi bi-telephone-fill"></i>
                        +1-234-567-890
                    </li>
                    <li>
                        <i class="bi bi-envelope-fill"></i>
                        <a href="mailto:info@nebulaecymbals.com" class="text-decoration-none">
                            info@nebulaecymbals.com
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h5 class="fw-bolder">AUTHENTICATION</h5>
                <ul class="list-unstyled d-flex flex-column gap-2">
                    <li><a href="{{ route('login') }}" class="text-decoration-none">Login</a></li>
                    <li><a href="{{ route('register') }}" class="text-decoration-none">Register</a></li>
                    <li><a href="{{ route('password.request') }}" class="text-decoration-none">Forgot Password</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
