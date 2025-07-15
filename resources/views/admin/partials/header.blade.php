<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown" id="notification-dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-bell"></i>
                    <span class="badge bg-danger navbar-badge notification-count" style="display: none;">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end notification-dropdown-menu">
                    <span class="dropdown-item dropdown-header">Notifications</span>
                    <div class="dropdown-divider"></div>
                    <div class="notification-items">
                        <!-- Notification items will be loaded here via JavaScript -->
                        <div class="text-center p-3 notification-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div class="text-center p-3 notification-empty" style="display: none;">
                            <i class="bi bi-bell-slash text-muted"></i>
                            <p class="text-muted mb-0">No new notifications</p>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('mindo.notifications.index') }}" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                </a>
            </li>
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <li class="user-header text-bg-primary">
                        <p>
                            <b> {{ Auth::user()->name }} </b> <br />
                            {{ Auth::user()->email }} <br />
                            @if (Auth::user()->getRoleNames()->count() > 0)
                                <small> ({{ Auth::user()->getRoleNames()[0] }}) </small>
                            @endif
                            <small>Member since
                                {{ Auth::user()->created_at->format('M. Y') }}
                            </small>
                        </p>
                    </li>
                    <li class="user-footer">
                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="btn btn-default btn-flat float-end">
                            Sign out
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
