<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ asset('dist/img/Logo1222.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3 bg-light" style="opacity: .8">
        <span style="display:block; text-align:center; width:80%; margin: 0 auto" class="brand-text font-weight-light">Infra Helpdesk</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x text-white"></i>
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add your sidebar menu items here -->
                @if (auth()->user()->isAdmin || auth()->user()->isVendor)
                    <li class="nav-item">
                        <a href="{{ url('dashboard') }}" class="nav-link {{ Request::is('dashboard*') ? 'active' : '' }}" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                                {{-- <i class="right fas fa-angle-left"></i> --}}
                            </p>
                        </a>
                    </li>
                @endif
                @if (auth()->user()->isAdmin)
                    <li class="nav-item">
                        <a href="{{ url('user-management') }}" class="nav-link {{ Request::is('user-management*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Users
                            </p>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a href="{{ url('ticket-management') }}" class="nav-link {{ Request::is('ticket-management*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>
                            Tickets
                        </p>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>
                            Reports
                        </p>
                    </a>
                </li> --}}
            </ul>
        </nav>
    </div>
</aside>
