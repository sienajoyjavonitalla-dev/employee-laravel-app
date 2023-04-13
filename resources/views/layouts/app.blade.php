<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Uprise</title>

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.0/js/toastr.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

    @yield('scripts')

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- Styles -->
    <link href="{{ asset('css/app.css?v=').time() }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.0/css/toastr.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

</head>
<body>
    <div id="app">
        @guest
        <main class="">
            @yield('content')
        </main>
        @else
        
          <!-- Main Sidebar Container -->
        <aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">
          <!-- Brand Logo -->
          <a href="index3.html" class="brand-link">
            <img src="favicon.ico" alt="uprise Logo" class="brand-image img-circle">
            <span class="brand-text font-weight-light">Uprise Rigging Ltd.</span>
          </a>

          <!-- Sidebar -->
          <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
              <div class="image">
                <!-- <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image"> -->
                <i class="fa fa-user-circle fa-2x text-light"></i>
              </div>
              <div class="info">
                <a href="#" class="d-block text-lowercase">{{Auth::user()->name}}</a>
              </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
              <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <!-- Add icons to the links using the .nav-icon class
                    with font-awesome or any other icon font library -->
                <!-- <li class="nav-item has-treeview menu-open">
                  <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                      Starter Pages
                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="#" class="nav-link active">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Active Page</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Inactive Page</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                      Simple Link
                      <span class="right badge badge-danger">New</span>
                    </p>
                  </a>
                </li> -->
                <li class="nav-item">
                      <a href="/home" class="nav-link  {{ request()->is('home') ? 'active' : '' }}">
                        <i class="fas fa-home nav-icon"></i>
                        <p>Dashboard</p>
                      </a>
                    </li>
                <li class="nav-item">
                  <a href="{{ route('logout') }}" class="nav-link"
                      onclick="event.preventDefault();
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
            <!-- /.sidebar-menu -->
          </div>
          <!-- /.sidebar -->
        </aside>
        

        <main class="content-wrapper">
            @yield('content')
        </main>
        @endguest

    </div>
    
    
</body>

</html>
