<!-- Main Sidebar Container -->
<aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4 sidebar-wrapper">
    <!-- Brand Logo -->
    <a href="/timeclock" class="brand-link">
        <img src="favicon.ico" alt="uprise Logo" class="brand-image img-circle">
        <span class="brand-text font-weight-light">Uprise Rigging Pty Ltd.</span>
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
                    <a href="/calendar" class="nav-link  {{ request()->is('calendar') ? 'active' : '' }}">
                        <i class="fas fa-solid fa-calendar  nav-icon"></i>
                        <p>Calendar</p>
                    </a>
                </li>
                @if(auth()->user()->roles == 'admin' || auth()->user()->roles == 'subadmin')

                <li class="nav-item">
                    <a href="/jobs" class="nav-link  {{ request()->is('jobs') ? 'active' : '' }}">
                        <i class="fas fa-briefcase nav-icon"></i>
                        <p>Jobs</p>
                    </a>
                </li>
                @endif
                @if(auth()->user()->roles != 'admin' && auth()->user()->roles != 'subadmin')
                <li class="nav-item">
                    <a href="/userjobs" class="nav-link  {{ request()->is('userjobs') ? 'active' : '' }}">
                        <i class="fas fa-briefcase nav-icon"></i>
                        <p>Jobs</p>
                    </a>
                </li>
                @endif

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
                @if(auth()->user()->roles != 'admin' || auth()->user()->roles != 'subadmin')
                    <li class="nav-item">
                        <a href="/timesheet" class="nav-link  {{ request()->is('timesheet') ? 'active' : '' }}">
                            <i class="fas fa-file nav-icon"></i>
                            <p>Timesheet</p>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->roles == 'admin' || auth()->user()->roles == 'subadmin')

                <li class="nav-item admin-only has-treeview {{ request()->is('fulltimetimesheet') ? ' menu-open' : '' }}">
                    <a class="nav-link {{ request()->is('timesheet') ? ' active' : '' }}" href="#">
                        <i class="fa-fw fas fa-id-card"></i>
                        <p>
                            <span> Full Timers</span>
                            <i class="right fa fa-plus"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview pl-4">
                        <li class="nav-item">
                            <a href="/fulltimetimesheet?user_type=full-timer" class="nav-link  {{ request()->is('fulltimetimesheet') ? 'active' : '' }}">
                                <i class="fas fa-calendar-check nav-icon"></i>
                                <p>Timesheet</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item admin-only has-treeview {{ (request()->is('subtimesheet') || request()->is('subinvoices')) ? ' menu-open' : '' }}">
                    <a class="nav-link {{ (request()->is('timesheet') || request()->is('subinvoices'))? ' active' : '' }}" href="#">
                        <i class="fa-fw fas fa-briefcase"></i>
                        <p>
                            <span> Subcontractors</span>
                            <i class="right fa fa-plus"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview pl-4">
                        <li class="nav-item">
                            <a href="/subtimesheet?user_type=subcontractor" class="nav-link  {{ request()->is('subtimesheet') ? 'active' : '' }}">
                                <i class="fas fa-calendar-check nav-icon"></i>
                                <p>Timesheet</p>
                            </a>
                        </li>
                        <li class="nav-item admin-only">
                            <a href="/subinvoices" class="nav-link  {{ request()->is('subinvoices') ? 'active' : '' }}">
                                <i class="fas fa-receipt nav-icon"></i>
                                <p>Payable Invoice</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item admin-only has-treeview {{ (request()->is('clients') || request()->is('clients_invoice')) ? ' menu-open' : '' }}">
                    <a class="nav-link {{ (request()->is('clients') || request()->is('clients_invoice'))? ' active' : '' }}" href="#">
                        <i class="fa-fw fas fa-user-tie"></i>
                        <p>
                            <span> Clients</span>
                            <i class="right fa fa-plus"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview pl-4">
                        <li class="nav-item">
                            <a href="/clients" class="nav-link  {{ request()->is('clients') ? 'active' : '' }}">
                                <i class="fas fa-users nav-icon"></i>
                                <p>Clients List</p>
                            </a>
                        </li>
                        <li class="nav-item admin-only">
                            <a href="/clients_invoice" class="nav-link  {{ request()->is('clients_invoice') ? 'active' : '' }}">
                                <i class="fas fa-receipt nav-icon"></i>
                                <p>Receivable Invoice</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item admin-only has-treeview {{ (request()->is('banks') || request()->is('users') || request()->is('footprints')) ? ' menu-open' : '' }}">
                    <a class="nav-link {{ (request()->is('banks') || request()->is('users') || request()->is('footprints'))? ' active' : '' }}" href="#">
                        <i class="fa-fw fas fa-user-ninja"></i>
                        <p>
                            <span> Admin </span>
                            <i class="right fa fa-plus"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview pl-4">
                        <li class="nav-item admin-only">
                            <a href="/users" class="nav-link  {{ request()->is('users') ? 'active' : '' }}">
                                <i class="fas fa-users nav-icon"></i>
                                <p>Users</p>
                            </a>
                        </li>
                        <li class="nav-item admin-only">
                            <a href="/banks" class="nav-link  {{ request()->is('banks') ? 'active' : '' }}">
                                <i class="fas fa-landmark nav-icon"></i>
                                <p>Bank Details</p>
                            </a>
                        </li>
                        <li class="nav-item admin-only">
                            <a href="/footprints" class="nav-link  {{ request()->is('footprints') ? 'active' : '' }}">
                                <i class="fas fa-database nav-icon"></i>
                                <p>Footprints</p>
                            </a>
                        </li>
                        <li class="nav-item admin-only">
                            <a href="/notes" class="nav-link  {{ request()->is('notes') ? 'active' : '' }}">
                                <i class="fas fa-thumbtack nav-icon"></i>
                                <p>Sticky Notes</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- <li class="nav-item admin-only">
                    <a href="/banks" class="nav-link  {{ request()->is('banks') ? 'active' : '' }}">
                        <i class="fas fa-landmark nav-icon"></i>
                        <p>Bank Details</p>
                    </a>
                </li>
                
                <li class="nav-item admin-only">
                    <a href="/users" class="nav-link  {{ request()->is('users') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        <p>Users</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/footprints" class="nav-link  {{ request()->is('footprints') ? 'active' : '' }}">
                        <i class="fas fa-database nav-icon"></i>
                        <p>Footprints</p>
                    </a>
                </li> -->

                @endif

                <li class="nav-item">
                    <a href="/pusher" class="nav-link  {{ request()->is('pusher') ? 'active' : '' }}">
                        <i class="fas fa-comments nav-icon"></i>
                        <p>Open Chat</p>
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

    </div>
    <!-- /.sidebar -->
</aside>