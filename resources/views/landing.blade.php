<!--
Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <title>{{ config('app.name') }} - {{ trans('landing.slogan.short') }}</title>
    <meta charset="utf-8">
    <meta name="keywords" content="Hotel" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Icon -->
    <link href="{{ asset('images/blue-logo.png') }}" rel="shortcut icon" type="image/x-icon">
    <link rel="canonical" href="{{ config('app.url') }}">

    <meta name="description" content="{{ trans('landing.meta.description') }}">
    <meta name="keywords" content="{{ trans('landing.keywords') }}">
    <meta name="author" content="{{ config('app.name') }}">

    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ config('app.name') }} - {{ trans('landing.slogan.short') }}">
    <meta property="og:description" content="{{ trans('landing.meta.description') }}">
    <meta property="og:image" content="{{ asset('images/brand.jpg') }}">
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ config('app.url') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image:alt" content="{{ config('app.name') }} - {{ trans('landing.slogan.short') }}">
    <meta name="twitter:image" content="{{ asset('images/brand.jpg') }}">
    <meta name="twitter:title" content="{{ config('app.name') }}">
    <meta name="twitter:description" content="{{ trans('landing.meta.description') }}">
    <meta name="twitter:site" content="@Hellen_Suite">

    <meta property="fb:app_id" content="1595428934178032" />

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/landing.css') }}" rel="stylesheet">

    <script>
        addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, {passive: false});

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>

    <!-- Custom fonts for this template -->
	<link href="//fonts.googleapis.com/css?family=Source+Sans+Pro:200,200i,300,300i,400,400i,600,600i,700,700i,900,900i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-7YCHMNHYP3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-7YCHMNHYP3');
    </script>
</head>
<body>
    <div id="app">
        @include('flash::message')

    <!-- header -->
    <header class="index-banner">
        <!-- nav -->
        <nav class="main-header">
            <div class="container-fluid mt-4">
                <div class="row">
                        <div class="col-8 col-sm-8 col-md-4 col-lg-4 col-xl-4">
                            <div id="brand">
                                <div id="logo" class="text-center pt-2">
                                    <a href="{{ url('/') }}">
                                        <img src="{{ asset('images/white-logo.png') }}" width="40" height="40" alt="{{ config('app.name') }}">
                                    </a>
                                </div>
                                <div id="word-mark">
                                    <h1>
                                        <a href="{{ url('/') }}" id="app-name">
                                            @include('partials.name')
                                        </a>
                                    </h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-sm-4 col-md-8 col-lg-8 col-xl-8">
                            <div id="menu">
                                <div id="menu-toggle">
                                    <div id="menu-icon">
                                        <div class="bar"></div>
                                        <div class="bar"></div>
                                        <div class="bar"></div>
                                    </div>
                                </div>
                                <ul id="menu-list" class="text-center nav-agile">
                                    <li>
                                        <a href="{{ url('/') }}" class="active">
                                            @lang('landing.home')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#overview" class="scroll">
                                            @lang('landing.about')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#pricing" class="scroll">
                                            @lang('landing.plans')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#faq" class="scroll">
                                            @lang('landing.questions')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#contact" class="scroll">
                                            @lang('landing.contact')
                                        </a>
                                    </li>
                                    <li class="menu-list-button">
                                        @if (auth()->check())
                                            <a href="{{ url('/home') }}" class="btn w3ls-btn" href="#contact" class="scroll">
                                                @lang('dashboard.dashboard')
                                            </a>
                                        @else
                                            <a href="{{ url('/login') }}" class="btn w3ls-btn" href="#contact" class="scroll">
                                                @lang('login.identification')
                                            </a>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </div>
                </div>
            </div>
        </nav>
        <!-- //nav -->
        <!-- banner -->
        <div class="banner layer" id="home">
            <div class="container">
                <div class="row banner-text">
                    <div class="slider-info col-lg-8">
                        <div class="agileinfo-logo mt-5">
                            <h2>
                                @lang('landing.slogan.section.one')
                            </h2>
                        </div>
                        <h3 class="txt-w3_agile">@lang('landing.slogan.section.two')</h3>
                        <a class="btn mt-4 mr-2 text-capitalize"  href="#overview" role="button">@lang('landing.more')</a>
                        <a class="btn mt-4 text-capitalize" href="#contact" role="button">@lang('landing.contact')</a>
                    </div>
                    <div class="col-lg-4 col-md-8 mt-lg-0 mt-5 banner-form">
                        <h5><i class="fas mr-2 fa-laptop"></i> @lang('common.register')</h5>
                        <form action="{{ route('register') }}" class="mt-4" method="post">
                            @csrf
                            @honeypot

                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" placeholder="{{ trans('common.name') }}" required="" />
                            @error('name')
                                <span class="error-message invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" id="email" placeholder="{{ trans('common.email') }}" required="" />
                            @error('email')
                                <span class="error-message invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" id="password" placeholder="{{ trans('common.password') }}" required="" />
                            @error('password')
                                <span class="error-message invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" id="password_confirmation" placeholder="{{ trans('login.confirmPassword') }}" required="" />
                            @error('password_confirmation')
                                <span class="error-message invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <input class="form-control text-capitalize" type="submit" value="{{ trans('common.register') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- //banner -->
    </header>
    <!-- //header -->
        <!-- process -->
        <section class="process py-5" id="overview">
            <div class="container py-md-5">
                <div class="row process-grids">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                        <h4 class="mb-4">@lang('landing.what', ['name' => config('app.name')])</h4>
                        <p class="mb-3">
                            @lang('landing.paragraphs.about.one', ['name' => config('app.name')])
                        </p>
                        <p>
                            @lang('landing.paragraphs.about.two')
                        </p>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 text-center">
                        <img src="{{ asset('images/b1.jpg') }}" alt="" class="img-fluid rounded"/>
                    </div>
                </div>
                <div class="row process-grids mt-5">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 text-center">
                        <img src="{{ asset('images/b2.jpg') }}" alt="" class="img-fluid rounded"/>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                        <h4 class="mb-4">
                            @lang('landing.philosophy')
                        </h4>
                        <p class="mb-3">
                            @lang('landing.paragraphs.philosophy.one', ['name' => config('app.name')])
                        </p>
                        <p>
                            @lang('landing.paragraphs.philosophy.two')
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- //process -->
        <!-- why choose us -->
        <section class="choose py-5" id="choose">
            <div class="container py-md-3">
                <h3 class="heading mb-5 text-center"> @lang('landing.why.choose')</h3>
                <div class="feature-grids row">
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-6 mt-lg-0 mt-4">
                        <div class="f1 icon1 p-4">
                            <i class="fas fa-cubes"></i>
                            <h3 class="my-3">
                                @lang('landing.why.components.title')
                            </h3>
                            <p>
                                @lang('landing.why.components.description')
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-6 mt-lg-0 mt-4">
                        <div class="f1 icon2 p-4">
                            <i class="fas fa-shield-alt"></i>
                            <h3 class="my-3">@lang('landing.why.security.title')</h3>
                            <p>
                                @lang('landing.why.security.description')
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-6 mt-lg-0 mt-4">
                        <div class="f1 icon3 p-4">
                            <i class="fas fa-palette"></i>
                            <h3 class="my-3">@lang('landing.why.design.title')</h3>
                            <p>
                                @lang('landing.why.design.description')
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- //why choose us -->
        <!-- quote -->
        <section class="quote bg-light py-5">
            <div class="container py-md-3">
                <h4>
                    @lang('landing.advantage')
                </h4>
                <div class="start text-right mt-4">
                    <a href="#contact" class="scroll">@lang('landing.contact')</a>
                </div>
                <div class="clearfix"></div>
            </div>
        </section>
        <!-- //quote -->
        <!-- banner bottom -->
        <section class="banner-bottom py-5">
            <div class="container py-md-3">
                <h4 class="text-center">
                    @lang('landing.technologies')
                </h4>
                <ul class="list-unstyled pt-5 partners-icon text-center">
                    <li>
                        <i class="fab fa-laravel clr2"></i>
                    </li>
                    <li>
                        <i class="fab fa-vuejs clr5"></i>
                    </li>
                    <li>
                        <i class="fab fa-html5 clr4"></i>
                    </li>
                    <li>
                        <i class="fab fa-css3-alt clr3"></i>
                    </li>
                    <li>
                        <i class="fab fa-bootstrap clr6"></i>
                    </li>
                    <li>
                        <i class="fab fa-linux clr1"></i>
                    </li>
                </ul>
            </div>
        </section>
        <!-- //banner bottom -->
        <!--/pricing -->
        <section class="pricing bg-light py-5" id="pricing">
            <div class="container py-lg-3">
                <div class="inner-sec">
                    <h3 class="heading mb-5 text-center">
                        @lang('plans.prices')
                    </h3>
                    <div class="price-right">
                        <div class="menu-grids">
                            <div class="row t-in">
                                @foreach ($plans as $plan)
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 price-main-info mt-lg-0 mt-4">
                                        <div class="price-inner card box-shadow p-4">
                                            <div class="card-body">
                                                <h4 class="">{{ trans('plans.type.' . $plan->getType()) }}</h4>
                                                <h5 class="card-title pricing-card-title">
                                                    <span class=""></span>$ {{ number_format($plan->price, 0, '.', ',') }}<span>/{{ $plan->months }} @choice('common.months', $plan->months)</span>
                                                </h5>

                                                @includeFirst([
                                                    'app.plans.types.' . $plan->getType(),
                                                    'app.plans.types.default'
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- //pricing -->
        <!-- faqs -->
        <section class="faq-w3l py-5" id="faq">
            <div class="container py-lg-3">
                <h3 class="heading mb-5 text-center">
                    @lang('landing.faq.title')
                </h3>
                <div class="row faq-info">
                    <div class=" col-md-6 faq-w3agile">
                        <ul class="faq pl-sm-4 pl-3">
                            <li class="item1 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>
                                    @lang('landing.faq.support.question')
                                </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p>
                                            @lang('landing.faq.support.answer')
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li class="item2 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>
                                    @lang('landing.faq.payment.question')
                                </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p>
                                            @lang('landing.faq.payment.answer')
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li class="item3 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>
                                    @lang('landing.faq.devices.question')
                                </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p>
                                            @lang('landing.faq.devices.answer')
                                        </p>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class=" col-md-6 faq-w3agile">
                        <ul class="faq pl-sm-4 pl-3">
                            <li class="item5 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>
                                    @lang('landing.faq.register.question')
                                </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p>
                                            @lang('landing.faq.register.answer')
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li class="item6 item mt-sm-4 mt-3 pt-3 pl-2"></li>
                                <h4>
                                    @lang('landing.faq.dinning.question')
                                </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p>
                                            @lang('landing.faq.dinning.answer')
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li class="item7 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>
                                    @lang('landing.faq.modules.question')
                                </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p>
                                            @lang('landing.faq.modules.answer')
                                        </p>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!-- //faqs -->
        <!-- testimonials -->
        {{-- <section class="testimonials bg-light py-5" id="testimonials">
            <div class="container py-lg-3">
                <h3 class="heading mb-5 text-center"> Nuestros clientes</h3>
                <div class="row test-grids">
                    <div class="col-md-4 col-sm-7 col-9">
                        <img src="{{ asset('images/b3.jpg') }}" alt="" class="img-fluid rounded one-edge-shadow" />
                    </div>
                    <div class="col-md-8">
                        <div class="callbacks_container">
                            <ul class="rslides" id="slider3">
                                <li>
                                    <div class="testi-pos">
                                        <h4>john watson</h4>
                                        <span class="">- congue leo</span>
                                        <ul class="d-flex mt-2">
                                            <li><i class="fas mr-1 fa-star"></i></li>
                                            <li><i class="fas mr-1 fa-star"></i></li>
                                            <li><i class="fas mr-1 fa-star"></i></li>
                                            <li><i class="fas mr-1 fa-star"></i></li>
                                            <li><i class="fas fa-star"></i></li>
                                        </ul>
                                    </div>
                                    <div class="testi-agile">
                                        <p>
                                            <i class="fa fa-quote-left pr-3"></i>Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Nulla
                                            quis lorem ut libero malesuada feugiat.Nulla quis lorem ut libero malesuada feugiat.
                                            Donec rutrum congue leo eget malesuada. Lorem ipsum dolor sit amet, consectetur adipiscing
                                            elit.
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="testi-pos">
                                        <h4>Paul walkner</h4>
                                        <span class="">- lacinia eget</span>
                                        <ul class="d-flex mt-2">
                                            <li><i class="fa mr-1 fa-star"></i></li>
                                            <li><i class="fa mr-1 fa-star"></i></li>
                                            <li><i class="fa mr-1 fa-star"></i></li>
                                            <li><i class="fa mr-1 fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                        </ul>
                                    </div>
                                    <div class="testi-agile">
                                        <p>
                                            <i class="fas fa-quote-left pr-3"></i>Donec rutrum congue leo eget consectetur sed, convallis at tellus. Nulla quis
                                            lorem ut libero malesuada feugiat.Nulla quis lorem ut libero malesuada feugiat. Donec
                                            rutrum congue leo eget malesuada. Lorem ipsum dolor sit amet, consectetur adipiscing
                                            elit
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="testi-pos">
                                        <h4>Anderson</h4>
                                        <span class="">- Donec rutru</span>
                                        <ul class="d-flex mt-2">
                                            <li><i class="fas mr-1 fa-star"></i></li>
                                            <li><i class="fas mr-1 fa-star"></i></li>
                                            <li><i class="fas mr-1 fa-star"></i></li>
                                            <li><i class="fas mr-1 fa-star"></i></li>
                                            <li><i class="fas fa-star"></i></li>
                                        </ul>
                                    </div>
                                    <div class="testi-agile">
                                        <p>
                                            <i class="fas fa-quote-left pr-3"></i>Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Nulla
                                            quis lorem ut libero malesuada feugiat.Nulla quis lorem ut libero malesuada feugiat.
                                            Donec rutrum congue leo eget malesuada. Lorem ipsum dolor sit amet, consectetur adipiscing
                                            elit
                                        </p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section> --}}
        <!-- //testimonials -->
        <!-- contact -->
        <section class="contact py-5" id="contact">
            <div class="container py-lg-3">
                <h3 class="heading mb-5 text-center"> @lang('landing.contact')</h3>
                <form action="{{ route('message') }}" method="post">
                    @csrf
                    @honeypot

                    <div class="row">
                        <div class="col-md-6 styled-input mt-0">
                            <input type="text" name="contact_name" value="{{ old('contact_name') }}" placeholder="{{ trans('common.name') }}" required="" min="3" max="100" pattern="[a-zA-Z]+">

                            @error('contact_name')
                                <span class="error-message invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6 styled-input mt-md-0">
                            <input type="text" name="contact_lastname" value="{{ old('contact_lastname') }}" placeholder="{{ trans('common.lastname') }}" required="" min="3" max="100" pattern="[a-zA-Z]+">

                            @error('contact_lastname')
                                <span class="error-message invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 styled-input">
                            <input type="email" name="contact_email" value="{{ old('contact_email') }}" placeholder="{{ trans('common.email') }}" required="">

                            @error('contact_email')
                                <span class="error-message invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6 styled-input">
                            <input type="tel" name="contact_phone" value="{{ old('contact_phone') }}" placeholder="{{ trans('common.mobile') }}" required="">

                            @error('contact_phone')
                                <span class="error-message invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="styled-input">
                        <textarea name="contact_message" value="{{ old('contact_message') }}" placeholder="{{ trans('notes.content') }}" required="" minlength="20" maxlength="500"></textarea>

                        @error('contact_message')
                            <span class="error-message invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="click text-center mt-3">
                        <input type="submit" value="{{ trans('email.send') }}">
                    </div>
                </form>
            </div>
        </section>
        <!-- //contact -->
        <!--footer -->
        <footer>
            <section class="footer footer_w3layouts_section_1its py-5">
                <div class="container py-md-4">
                    <div class="footer-grid_section text-center">
                        <div class="footer-title mb-3">
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('images/blue-logo.png') }}" alt="{{ config('app.name') }}">
                                @include('partials.name')
                            </a>
                        </div>
                        <div class="footer-text">
                            <p>
                                @lang('landing.slogan.section.one') @lang('landing.slogan.section.two')
                            </p>
                        </div>
                        <ul class="social_section_1info">
                            <li class="mb-2 facebook">
                                <a href="https://www.facebook.com/HellenSuite" target="_blank" rel="noopener noreferrer">
                                    <i class="fab mr-1 fa-facebook-f"></i>facebook
                                </a>
                            </li>
                            <li class="mb-2 twitter">
                                <a href="https://twitter.com/@Hellen_Suite" target="_blank" rel="noopener noreferrer">
                                    <i class="fab mr-1 fa-twitter"></i>twitter
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="row footer-top mt-md-5 mt-4">
                        <div class="col-lg-4 col-md-6 footer-grid_section_1its_w3">
                            <div class="footer-title">
                                <h3>@lang('common.address')</h3>
                            </div>
                            <div class="footer-text">
                                <p>@lang('common.address') : Bucaramanga - Santander</p>
                                <p>@lang('common.mobile') : <i class="fab fa-whatsapp"></i> <a href="tel:+57{{ config('settings.tel') }}">{{ config('settings.tel') }}</a></p>
                                <p>@lang('common.emailOnly') : <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a></p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-md-0 mt-4 footer-grid_section_1its_w3">
                            <div class="footer-title">
                                <h3>Enlaces rápidos</h3>
                            </div>
                            <div class="row">
                                <ul class="col-6 links">
                                    <li><a href="#choose" class="scroll">@lang('landing.why.choose')</a></li>
                                    <li><a href="#overview" class="scroll">@lang('landing.about')</a></li>
                                    <li><a href="#pricing" class="scroll">@lang('plans.prices')</a></li>
                                    <li><a href="#faq" class="scroll">@lang('landing.questions')</a></li>
                                    {{-- <li><a href="#testimonials" class="scroll">@lang('landing.testimonials.title') </a></li> --}}
                                    <li><a href="#contact" class="scroll">@lang('landing.contact') </a></li>
                                </ul>
                                <ul class="col-6 links">
                                    <li><a href="#">@lang('landing.privacy')</a></li>
                                    <li><a href="#">@lang('landing.terms')</a></li>
                                    <li><a href="#">@lang('landing.tutorials')</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12 mt-lg-0 mt-4 col-sm-12 footer-grid_section_1its_w3"  style="max-width: 100%;">
                            <div class="footer-title">
                                <h3>@lang('landing.subscribers.title')</h3>
                            </div>
                            <div class="footer-text">
                                <p>@lang('landing.subscribe')</p>
                                <form action="{{ route('subscribe') }}" method="post">
                                    @csrf
                                    @honeypot

                                    <input type="email" name="email" placeholder="{{ trans('common.email') }}" required="">
                                    <button class="btn1" type="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                                    <div class="clearfix"> </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </footer>
        <!-- //footer -->
        <!-- copyright -->
        <div class="cpy-right text-center py-3">
            <p class="">© {{ date('Y') }} {{ config('app.name') }}. {{ trans('landing.copyright') }} | {{ trans('landing.design') }}
                <a href="http://w3layouts.com"> W3layouts.</a>
            </p>
        </div>
        <!-- //copyright -->
    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="{{ asset('js/landing.js') }}"></script>
    <script>
		$(() => {
            //On Scroll Functionality
            $(window).scroll(() => {
                var windowTop = $(window).scrollTop();
                windowTop> 100 ? $('nav').addClass('navShadow') : $('nav').removeClass('navShadow');
                windowTop> 100 ? $('ul.nav-agile').css('top', '50px') : $('ul.nav-agile').css('top', '160px');
            });

            //Click Logo To Scroll To Top
            $('#logo').on('click', () => {
                $('html,body').animate({
                    scrollTop: 0
                }, 500);
            });

            //Toggle Menu
            $('#menu-toggle').on('click', () => {
                $('#menu-toggle').toggleClass('closeMenu');
                $('ul#menu-list').toggleClass('showMenu');

                $('li').on('click', () => {
                    $('ul#menu-list').removeClass('showMenu');
                    $('#menu-toggle').removeClass('closeMenu');
                });
            });
		});

        $(document).ready(function ($) {
            $(".scroll").click(function (event) {
                event.preventDefault();

                $('html,body').animate({
                    scrollTop: $(this.hash).offset().top
                }, 900);
            });
        });

        $(document).ready(function () {
            $().UItoTop({
                easingType: 'easeOutQuart'
            });

        });

        $('#flash-overlay-modal').modal();
    </script>
    <script type="application/ld+json" async>
        {
            "@context": "http://schema.org/",
            "@type": "WebSite",
            "name": "{{ config('app.name') }}",
            "alternateName": "{{ config('app.name') }} - {{ trans('landing.slogan.short') }}",
            "url": "{{ config('app.url') }}",
            "image": "{{ asset('images/brand.png') }}",
            "description": "{{ trans('landing.meta.description') }}"
        }
    </script>
    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "Organization",
            "name": "{{ config('app.name') }}",
            "url": "{{ config('app.url') }}",
            "sameAs": [
                "https://twitter.com/Hellen_Suite",
                "https://www.facebook.com/HellenSuite"
            ]
        }
    </script>
</body>
</html>
