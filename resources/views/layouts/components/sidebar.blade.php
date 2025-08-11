<!--begin::Sidebar-->
{{-- data-bs-theme="dark" --}}
<aside class="app-sidebar bg-body-secondary shadow">
    <div class="sidebar-brand">
        {{-- <a href="/" class="brand-link logo-switch">
            <img src="{{ asset('adminAssets/img/logo.png') }}" alt="Pion"
                class="brand-image-xl logo-xl opacity-75 px-2" />
        </a> --}}
        <a href="/" class="brand-link d-flex align-items-center text-decoration-none">
            <img src="{{ asset('adminAssets/img/logo_icon.png') }}" alt="Pion"
                class="brand-image img-circle elevation-3 d-flex items-center"
                style="width: 36px; height: 36px;">
            <span class="brand-text fw-semibold text-dark ms-2">PION ADMIN</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="/" class="nav-link fw-semibold">
                        {{-- <i class="nav-icon bi bi-circle-fill"></i> --}}
                        <i class="nav-icon bi bi-house-door-fill"></i>
                        <p>Tổng quan</p>
                    </a>
                </li>

                {{-- Show if it is admin or staff --}}
                @if (Auth::user()?->hasRole('admin') || Auth::user()?->hasRole('staff'))
                    <li class="nav-item">
                        <a href="/categories" class="nav-link fw-semibold">
                            <i class="nav-icon bi bi-folder-fill"></i>
                            <p>
                                Quản lý danh mục
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link fw-semibold">
                            <i class="nav-icon bi bi-newspaper"></i>
                            <p>
                                Tin tức
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/news" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Quản lý</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/news/create" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Thêm tin tức</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Show if it is admin (SUPER) --}}
                @if (Auth::user()?->hasRole('admin'))
                    <li class="nav-item">
                        <a href="#" class="nav-link fw-semibold">
                            <i class="nav-icon bi bi-people-fill"></i>
                            <p>
                                Quản lý tài khoản
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/users" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Quản lý</p>
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="#!" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Quyền truy cập</p>
                                </a>
                            </li> --}}
                            <li class="nav-item">
                                <a href="/users/create" class="nav-link">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Thêm người quản lý</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- Show if it is staff ads --}}
                @if (Auth::user()?->hasRole('staffads'))
                    <li class="nav-item">
                        <a href="{{ route('admin.consultations.index') }}" class="nav-link fw-semibold">
                            <i class="nav-icon bi bi-chat-dots-fill"></i>
                            <p>Quản lý tư vấn</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>
<!--end::Sidebar-->
