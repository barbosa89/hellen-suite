<!--
Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <title>{{ config('app.name', 'Welkome') }}</title>
    <meta charset="utf-8">
    <meta name="keywords" content="Hotel" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Icon -->
    <link href="{{ asset('images/icon.png') }}" rel="shortcut icon" type="image/x-icon">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/index.css') }}" rel="stylesheet">

  <!-- Custom fonts for this template -->
  <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
</head>

<body id="page-top">
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
            <div class="container">
                <a class="navbar-brand js-scroll-trigger" href="#page-top">
                    {{ config('app.name') }}
                </a>
                
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fa fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="#about">
                                @lang('landing.about')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="#projects">
                                @lang('landing.plans')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="#signup">
                                @lang('landing.contact')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="{{ route('login') }}">
                                @lang('login.identification')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="{{ route('register') }}">
                                @lang('login.signup')
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Header -->
        <header class="masthead">
            <div class="container d-flex h-100 align-items-center">
                <div class="mx-auto text-center">
                    <h1 class="mx-auto my-0 text-uppercase">
                        {{ config('app.name') }}
                    </h1>
                    <h2 class="text-white-50 mx-auto mt-2 mb-5">
                        @lang('landing.excerpt')
                    </h2>
                    <a href="#about" class="btn btn-primary js-scroll-trigger">
                        @lang('landing.more')
                    </a>
                </div>
            </div>
        </header>
        <!-- About Section -->
        <section id="about" class="about-section text-center">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <h2 class="text-white mb-2">{{ trans('landing.about') }} {{ config('app.name') }}</h2>
                        <p class="text-white-50">
                            @lang('landing.app')
                        </p>
                        <a href="#" class="btn btn-primary">@lang('landing.demo')</a>
                    </div>
                </div>
                <img src="{{ asset('images/coffe.png') }}" class="img-fluid" alt="">
            </div>
        </section>
        <!-- Projects Section -->
        <section id="projects" class="projects-section bg-light">
            <div class="container">
                <!-- Featured Project Row -->
                <div class="row align-items-center no-gutters mb-4 mb-lg-5">
                    <div class="col-xl-8 col-lg-7">
                        <img class="img-fluid mb-3 mb-lg-0" src="{{ asset('images/reception.jpg') }}" alt="">
                    </div>
                    <div class="col-xl-4 col-lg-5">
                        <div class="featured-text text-center text-lg-left">
                            <h2>@lang('landing.plans')</h2>
                            <p class="text-black-50 mb-0">@lang('landing.services_description')</p>
                        </div>
                    </div>
                </div>
                <!-- Project One Row -->
                <div class="row justify-content-center no-gutters mb-5 mb-lg-0">
                    <div class="col-lg-6">
                        <img class="img-fluid" src="{{ asset('images/free-plan.jpg') }}" alt="">
                    </div>
                    <div class="col-lg-6">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-left">
                                    <h3 class="text-white">@lang('landing.free_plan')</h3>
                                    <h4 class="text-muted">$ 0.00</h4>
                                    <p class="mb-0 text-white-50">
                                        @lang('landing.free_plan_description')
                                    </p>
                                    <hr class="d-none d-lg-block mb-0 ml-0">
                                    <div class="mt-4">
                                        <a href="#" class="btn btn-primary">
                                            @lang('login.signup')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Project Two Row -->
                <div class="row justify-content-center no-gutters">
                    <div class="col-lg-6">
                        <img class="img-fluid" src="{{ asset('images/full-plan.jpg') }}" alt="">
                    </div>
                    <div class="col-lg-6 order-lg-first">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-right">
                                    <h3 class="text-white">@lang('landing.full_plan')</h3>
                                    <h4 class="text-muted">$ @lang('landing.full_plan_price')</h4>
                                    <p class="mb-0 text-white-50">
                                        @lang('landing.full_plan_description')
                                    </p>
                                    <hr class="d-none d-lg-block mb-0 mr-0">
                                    <div class="mt-4">
                                        <a href="#" class="btn btn-primary">
                                            @lang('login.signup')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center no-gutters mb-5 mb-lg-0">
                    <div class="col-lg-6">
                        <img class="img-fluid" src="{{ asset('images/desktop-plan.jpg') }}" alt="">
                    </div>
                    <div class="col-lg-6">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-left">
                                    <h3 class="text-white">@lang('landing.desktop_plan')</h3>
                                    <h4 class="text-muted">$ @lang('landing.desktop_plan_price')</h4>
                                    <p class="mb-0 text-white-50">
                                        @lang('landing.desktop_plan_description')
                                    </p>
                                    <hr class="d-none d-lg-block mb-0 ml-0">
                                    <div class="mt-4">
                                        <a href="#" class="btn btn-primary">
                                            @lang('landing.buy')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Signup Section -->
        <section id="signup" class="signup-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-lg-8 mx-auto text-center">
                        <i class="fa fa-paper-plane fa-2x mb-2 text-white"></i>
                        <h2 class="text-white mb-5">@lang('landing.subscribe')</h2>
                        <form class="form-inline d-flex">
                            <input type="email" class="form-control flex-fill mr-0 mr-sm-2 mb-3 mb-sm-0" id="inputEmail" placeholder="{{ trans('landing.enter_email') }}...">
                            <button type="submit" class="btn btn-primary mx-auto">@lang('landing.subscription')</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- Contact Section -->
        <section class="contact-section bg-black">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fa fa-map-marker text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">@lang('landing.address')</h4>
                                <hr class="my-4">
                                <div class="small text-black-50">Bucaramanga - Colombia</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fa fa-envelope text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">@lang('login.email')</h4>
                                <hr class="my-4">
                                <div class="small text-black-50">
                                    <a href="#">contacto@omarbarbosa.com</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fa fa-mobile text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">@lang('landing.phone')</h4>
                                <hr class="my-4">
                                <div class="small text-black-50">+1 (555) 902-8832</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="social d-flex justify-content-center">
                    <a href="#" class="mx-2">
                    <i class="fa fa-twitter"></i>
                    </a>
                    <a href="#" class="mx-2">
                    <i class="fa fa-facebook-f"></i>
                    </a>
                    <a href="#" class="mx-2">
                    <i class="fa fa-github"></i>
                    </a>
                </div>
            </div>
        </section>
        <!-- Footer -->
        <footer class="bg-black small text-center text-white-50">
            <div class="container">
                @lang('landing.copyright') &copy; {{ config('app.name') }} {{ date('Y') }}
            </div>
        </footer>
    </div>
    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/index.js') }}"></script>
</body>
</html>
