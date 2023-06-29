<!-- Main Sidebar Container -->
<aside style="min-height: 150%;" class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4 sidebar-wrapper">
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
                    <a href="/timeclock" class="nav-link  {{ request()->is('timeclock') ? 'active' : '' }}">
                        <i class="fas fa-clock nav-icon"></i>
                        <p>Timeclock</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/timelogs" class="nav-link  {{ request()->is('timelogs') ? 'active' : '' }}">
                        <i class="fas fa-user-clock nav-icon"></i>
                        <p>Time Logs</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/timesheet" class="nav-link  {{ request()->is('timesheet') ? 'active' : '' }}">
                        <i class="fas fa-file nav-icon"></i>
                        <p>Timesheet</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/calendar" class="nav-link  {{ request()->is('calendar') ? 'active' : '' }}">
                        <i class="fas fa-solid fa-calendar  nav-icon"></i>
                        <p>Calendar</p>
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
                    <a href="/clients" class="nav-link  {{ request()->is('clients') ? 'active' : '' }}">
                        <i class="fas fa-address-book nav-icon"></i>
                        <p>Clients</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/users" class="nav-link  {{ request()->is('users') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        <p>Users</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/pusher" class="nav-link  {{ request()->is('pusher') ? 'active' : '' }}">
                        <i class="fas fa-comments nav-icon"></i>
                        <p>Chat</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/how_tos" class="nav-link  {{ request()->is('how_tos') ? 'active' : '' }}">
                        <i class="fas fa-question nav-icon"></i>
                        <p>How To's</p>
                    </a>
                </li>
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

        <link href="{{ asset('css/sidebar-menu.css?v=').time() }}" rel="stylesheet">

        <!-- Sidebar Menu Logout Section-->
        <!-- <nav class="mt-2 logout-section">

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
        </nav> -->

    </div>
    <!-- /.sidebar -->
</aside>