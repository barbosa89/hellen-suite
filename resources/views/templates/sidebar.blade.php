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
            <span>@lang('users.title')</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('plans.index') }}">
            <i class="fas fa-fw fa-star"></i>
            <span>@lang('plans.title')</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('configurations.index') }}">
            <i class="fas fa-fw fa-cogs"></i>
            <span>@lang('configurations.title')</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('logs.viewer') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>Logs</span></a>
        </li>
    @endrole

    @role('manager')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('invoices.index') }}">
            <i class="fas fa-file-invoice"></i>
            <span>@lang('invoices.title')</span></a>
        </li>
    @endrole

    @can('hotels.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('hotels.index') }}">
            <i class="fas fa-hotel"></i>
            <span>@lang('hotels.title')</span></a>
        </li>
    @endcan

    @role('manager')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('team.index') }}">
            <i class="fas fa-user"></i>
            <span>@lang('users.team')</span></a>
        </li>
    @endrole

    @can('rooms.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('rooms.index') }}">
            <i class="fa fa-fw fa-bed"></i>
            <span>@lang('rooms.title')</span></a>
        </li>
    @endcan

    @can('vouchers.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('vouchers.index') }}">
            <i class="fas fa-receipt"></i>
            <span>@lang('vouchers.title')</span></a>
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

    @can('vehicles.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('vehicles.index') }}">
            <i class="fa fa-fw fa-car"></i>
            <span>@lang('vehicles.title')</span></a>
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

    @can('dining.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dining.index') }}">
            <i class="fa fa-fw fa-utensils"></i>
            <span>@lang('dining.title')</span></a>
        </li>
    @endcan

    @can('assets.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('assets.index') }}">
            <i class="fa fa-fw fa-tv"></i>
            <span>@lang('assets.title')</span></a>
        </li>
    @endcan

    @can('props.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('props.index') }}">
            <i class="fas fa-fw fa-person-booth"></i>
            <span>@lang('props.title')</span></a>
        </li>
    @endcan

    @can('shifts.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('shifts.index') }}">
            <i class="fas fa-fw fa-clock"></i>
            <span>@lang('shifts.title')</span></a>
        </li>
    @endcan

    @can('notes.index')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('notes.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>@lang('notes.title')</span></a>
        </li>
    @endcan

    @can('tags.index')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('tags.index') }}">
        <i class="fas fa-fw fa-tags"></i>
        <span>Tags</span></a>
    </li>
    @endcan

    {{-- @role('manager')
        <li class="nav-item">
            <a class="nav-link" href="#">
            <i class="fas fa-cogs"></i>
            <span>Configuración</span></a>
        </li>
    @endrole --}}
</ul>
