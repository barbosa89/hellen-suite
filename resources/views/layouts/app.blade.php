<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Welkome') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/welkome.css') }}" rel="stylesheet">
</head>
<body id="page-top">
    <div id="app">
{{--         <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Welkome') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav> --}}
        <nav class="navbar navbar-expand navbar-dark bg-dark static-top">
              <a class="navbar-brand mr-1" href="index.html">Start Bootstrap</a>
              <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
              <i class="fa fa-bars"></i>
              </button>
              <!-- Navbar Search -->
              <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
                  <div class="input-group">
                      <input type="text" class="form-control" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                      <div class="input-group-append">
                          <button class="btn btn-primary" type="button">
                          <i class="fa fa-search"></i>
                          </button>
                      </div>
                  </div>
              </form>
              <!-- Navbar -->
              <ul class="navbar-nav ml-auto ml-md-0">
                  <li class="nav-item dropdown no-arrow mx-1">
                      <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fa fa-bell fa-fw"></i>
                      <span class="badge badge-danger">9+</span>
                      </a>
                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="alertsDropdown">
                          <a class="dropdown-item" href="#">Action</a>
                          <a class="dropdown-item" href="#">Another action</a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="#">Something else here</a>
                      </div>
                  </li>
                  <li class="nav-item dropdown no-arrow mx-1">
                      <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fa fa-envelope fa-fw"></i>
                      <span class="badge badge-danger">7</span>
                      </a>
                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="messagesDropdown">
                          <a class="dropdown-item" href="#">Action</a>
                          <a class="dropdown-item" href="#">Another action</a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="#">Something else here</a>
                      </div>
                  </li>
                  <li class="nav-item dropdown no-arrow">
                      <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fa fa-user-circle fa-fw"></i>
                      </a>
                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                          <a class="dropdown-item" href="#">Settings</a>
                          <a class="dropdown-item" href="#">Activity Log</a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="#" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Logout</a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                      </div>
                  </li>
              </ul>
        </nav>
        <main>
            @yield('content')
        </main>
    </div>

        <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/welkome.js') }}"></script>
</body>
</html>
