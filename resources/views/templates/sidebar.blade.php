<!-- Sidebar -->
<ul class="sidebar navbar-nav">
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('home') }}">
        <i class="fa fa-fw fa-tachometer"></i>
        <span>@lang('dashboard.dashboard')</span>
        </a>
    </li>

    @role('root')
        {{-- <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-folder"></i>
            <span>Pages</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                <h6 class="dropdown-header">Login Screens:</h6>
                <a class="dropdown-item" href="login.html">Login</a>
                <a class="dropdown-item" href="register.html">Register</a>
                <a class="dropdown-item" href="forgot-password.html">Forgot Password</a>
                <div class="dropdown-divider"></div>
                <h6 class="dropdown-header">Other Pages:</h6>
                <a class="dropdown-item" href="404.html">404 Page</a>
                <a class="dropdown-item" href="blank.html">Blank Page</a>
            </div>
        </li> --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Usuarios</span></a>
        </li>
    @endrole

    @role('manager')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('hotels.index') }}">
            <i class="fas fa-hotel"></i>
            <span>Hoteles</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('team.index') }}">
            <i class="fas fa-user"></i>
            <span>Mi equipo</span></a>
        </li>
    @endrole

    @can('rooms.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('rooms.index') }}">
            <i class="fa fa-fw fa-bed"></i>
            <span>@lang('rooms.title')</span></a>
        </li>
    @endcan

    @can('invoices.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('invoices.index') }}">
            <i class="fas fa-file-invoice"></i>
            <span>@lang('invoices.title')</span></a>
        </li>
    @endcan

    @can('guests.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('guests.index') }}">
            <i class="fa fa-fw fa-users"></i>
            <span>@lang('guests.title')</span></a>
        </li>
    @endcan

    @can('companies.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('companies.index') }}">
            <i class="fa fa-fw fa-building"></i>
            <span>@lang('companies.title')</span></a>
        </li>
    @endcan

    @can('products.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('products.index') }}">
            <i class="fa fa-fw fa-boxes"></i>
            <span>@lang('products.title')</span></a>
        </li>
    @endcan

    @can('services.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('services.index') }}">
            <i class="fa fa-fw fa-concierge-bell"></i>
            <span>@lang('services.title')</span></a>
        </li>
    @endcan

    @role('manager')
        <li class="nav-item">
            <a class="nav-link" href="#">
            <i class="fas fa-cogs"></i>
            <span>Configuración</span></a>
        </li>
    @endrole
</ul>