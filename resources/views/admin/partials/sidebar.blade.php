<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand" style="background-color: #fcfcfc">
        <a href="{{ route('mindo.home') }}" class="brand-link">
            <img src="{{ asset('assets/images/logo-nebulae.png') }}" alt="Nebulae Cymbals" class="brand-image" />
            {{-- <span class="brand-text fw-light">Nebulae Cymbals</span> --}}
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('mindo.home') }}"
                        class="nav-link {{ Request::routeIs('mindo/dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @canany(['PRODUCT_CATEGORY_LIST', 'UNIT_LIST', 'PRODUCT_LIST', 'CUSTOMER_LIST', 'TRANSACTION_LIST'])
                    <li
                        class="nav-item {{ Request::is('mindo/product-categories*', 'mindo/units*', 'mindo/products*', 'mindo/customers*', 'mindo/transactions*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa-solid fa-warehouse"></i>
                            <p>
                                Manajemen Gudang
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('PRODUCT_CATEGORY_LIST')
                                <li class="nav-item">
                                    <a href="{{ route('mindo.product-categories.index') }}"
                                        class="nav-link {{ Request::is('mindo/product-categories*') ? 'active' : '' }}">
                                        <i class="nav-icon fa-solid fa-tags"></i>
                                        <p>Kategori Produk</p>
                                    </a>
                                </li>
                            @endcan

                            @can('UNIT_LIST')
                                <li class="nav-item">
                                    <a href="{{ route('mindo.units.index') }}"
                                        class="nav-link {{ Request::is('mindo/units*') ? 'active' : '' }}">
                                        <i class="nav-icon fa-solid fa-ruler"></i>
                                        <p>Satuan</p>
                                    </a>
                                </li>
                            @endcan

                            @can('PRODUCT_LIST')
                                <li class="nav-item">
                                    <a href="{{ route('mindo.products.index') }}"
                                        class="nav-link {{ Request::is('mindo/products*') ? 'active' : '' }}">
                                        <i class="nav-icon fa-solid fa-boxes-stacked"></i>
                                        <p>Produk</p>
                                    </a>
                                </li>
                            @endcan

                            @can('CUSTOMER_LIST')
                                <li class="nav-item">
                                    <a href="{{ route('mindo.customers.index') }}"
                                        class="nav-link {{ Request::is('mindo/customers*') ? 'active' : '' }}">
                                        <i class="nav-icon fa-solid fa-users"></i>
                                        <p>Pelanggan</p>
                                    </a>
                                </li>
                            @endcan

                            @can('TRANSACTION_LIST')
                                <li class="nav-item">
                                    <a href="{{ route('mindo.transactions.index') }}"
                                        class="nav-link {{ Request::is('mindo/transactions*') ? 'active' : '' }}">
                                        <i class="nav-icon fa-solid fa-exchange-alt"></i>
                                        <p>Transaksi</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['USER_LIST', 'GRUP_USER_LIST', 'HAK_AKSES_LIST'])
                    <li
                        class="nav-item {{ Request::is('mindo/users*', 'mindo/roles*', 'mindo/permissions*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fa-solid fa-users"></i>
                            <p>
                                Manajemen User
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('USER_LIST')
                                <li class="nav-item">
                                    <a href="{{ route('mindo.users.index') }}"
                                        class="nav-link {{ Request::is('mindo/users*') ? 'active' : '' }}">
                                        <i class="nav-icon fa-solid fa-user"></i>
                                        <p>User</p>
                                    </a>
                                </li>
                            @endcan
                            @can('GRUP_USER_LIST')
                                <li class="nav-item">
                                    <a href="{{ route('mindo.roles.index') }}"
                                        class="nav-link {{ Request::is('mindo/roles*') ? 'active' : '' }}">
                                        <i class="nav-icon fa-solid fa-people-group"></i>
                                        <p>Grup User</p>
                                    </a>
                                </li>
                            @endcan
                            @can('HAK_AKSES_LIST')
                                <li class="nav-item">
                                    <a href="{{ route('mindo.permissions.index') }}"
                                        class="nav-link {{ Request::is('mindo/permissions*') ? 'active' : '' }}">
                                        <i class="nav-icon fa-solid fa-user-shield"></i>
                                        <p>Hak Akses</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany



                @can('LOG_LIST')
                    <li class="nav-item">
                        <a href="{{ route('mindo.activity-logs.index') }}"
                            class="nav-link {{ Request::is('mindo/activity-logs*') ? 'active' : '' }}">
                            <i class="nav-icon fa-solid fa-clock-rotate-left"></i>
                            <p>Logs</p>
                        </a>
                    </li>
                @endcan

                <li class="nav-item">
                    <a href="{{ route('mindo.notifications.index') }}"
                        class="nav-link {{ Request::is('mindo/notifications*') ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-bell"></i>
                        <p>
                            Notifications
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="badge bg-danger ms-auto">{{ auth()->user()->unreadNotifications->count() }}</span>
                            @endif
                        </p>
                    </a>
                </li>





            </ul>
        </nav>
    </div>
</aside>
