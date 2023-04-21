<!-- Main Sidebar Container -->
<aside style="min-height: 100%;" class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/home" class="brand-link">
        <img src="favicon.ico" alt="uprise Logo" class="brand-image img-circle">
        <span class="brand-text font-weight-light">Uprise Rigging Ltd.</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 d-flex">

            <div class="image">
                <i class="fa fa-user-circle fa-2x text-light"></i>
            </div>

            <div class="info">
                <a href="#" class="d-block text-uppercase">{{Auth::user()->name}}</a>
            </div>

        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2 sidebar-main-items">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <li class="nav-item">
                    <a href="/home" class="nav-link  {{ request()->is('home') ? 'active' : '' }}">
                        <i class="fas fa-home nav-icon"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/jobs" class="nav-link  {{ request()->is('jobs') ? 'active' : '' }}">
                        <i class="fas fa-briefcase nav-icon"></i>
                        <p>Jobs</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/invoices" class="nav-link  {{ request()->is('invoices') ? 'active' : '' }}">
                        <i class="fas fa-print nav-icon"></i>
                        <p>Invoices</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/users" class="nav-link  {{ request()->is('users') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        <p>Users</p>
                    </a>
                </li>
            </ul>
        </nav>

        <link href="{{ asset('css/sidebar-menu.css?v=').time() }}" rel="stylesheet">

        <!-- Sidebar Menu Logout Section-->
        <nav class="mt-2 logout-section">

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-power-off"></i>
                        <p>{{ __('Logout') }}</p>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>

    </div>
    <!-- /.sidebar -->
</aside>