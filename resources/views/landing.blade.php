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
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/landing.css') }}" rel="stylesheet">

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
</head>
<body>
    <div id="app">
    <!-- header -->
    <header class="index-banner">
        <!-- nav -->
        <nav class="main-header">
            <div class="container-fluid mt-4">
                <div class="row">
                        <div class="col-8 col-sm-8 col-md-8 col-lg-4 col-xl-4">
                            <div id="brand">
                                <div id="logo">
                                    <a href="{{ url('/') }}">
                                        <i class="fa fa-bed"></i>
                                    </a>
                                </div>
                                <div id="word-mark">
                                    <h1>
                                        <a href="{{ url('/') }}">{{ config('app.name') }}</a>
                                    </h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-sm-4 col-md-4 col-lg-8 col-xl-8">
                            <div id="menu">
                                <div id="menu-toggle">
                                    <div id="menu-icon">
                                        <div class="bar"></div>
                                        <div class="bar"></div>
                                        <div class="bar"></div>
                                    </div>
                                </div>
                                <ul id="menu-list" class="text-center text-capitalize nav-agile">
                                    <li>
                                        <a href="{{ url('/') }}" class="active">Inicio</a>
                                    </li>
                                    <li>
                                        <a href="#choose" class="scroll">Por qué elegirnos</a>
                                    </li>
                                    <li>
                                        <a href="#overview" class="scroll">Vista previa</a>
                                    </li>
                                    <li>
                                        <a href="#pricing" class="scroll">Precios</a>
                                    </li>
                                    <li>
                                        <a href="#faq" class="scroll">Preguntas</a>
                                    </li>
                                    <li>
                                        <a href="#contact" class="scroll">Contacto</a>
                                    </li>
                                    <li>
                                        <a href="{{ url('/login') }}" class="btn w3ls-btn" href="#contact" class="scroll">Login</a>
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
                                Administración de hoteles
                            </h2>
                        </div>
                        <h3 class="txt-w3_agile">Una suite completa para administrar fácil.</h3>
                        <a class="btn mt-4 mr-2 text-capitalize"  href="#" data-toggle="modal" data-target="#exampleModalCenter1" role="button">read more</a>
                        <a class="btn mt-4 text-capitalize"  href="#" data-toggle="modal" data-target="#exampleModal" role="button">watch video <i class="fa fa-play-circle"></i></a>
                    </div>
                    <div class="col-lg-4 col-md-8 mt-lg-0 mt-5 banner-form">
                        <h5><i class="fa mr-2 fa-laptop"></i> Regístrate</h5>
                        <form action="#" class="mt-4" method="post">
                            <input class="form-control" type="text" name="Name" placeholder="Nombre" required="" />
                            <input class="form-control" type="email" name="Email" placeholder="Correo electrónico" required="" />
                            <input class="form-control" type="text" name="Number" placeholder="Teléfono" required="" />
                            <input class="form-control" type="password" name="Number" placeholder="Contraseña" required="" />
                            <input class="form-control text-capitalize" type="submit" value="Registrar cuenta">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- //banner -->
    </header>
    <!-- //header -->
        <!-- banner bottom -->
        <section class="banner-bottom py-5">
            <div class="container py-md-3">
                <h4 class="text-center">Con el respaldo de las mejores empresas en tecnología</h4>
                <ul class="list-unstyled pt-5 partners-icon text-center">
                    <li>
                        <i class="fa fa-google clr1"></i>
                    </li>
                    <li>
                        <i class="fa fa-facebook clr2"></i>
                    </li>
                    <li>
                        <i class="fa fa-twitter clr3"></i>
                    </li>
                    <li>
                        <i class="fa fa-instagram clr4"></i>
                    </li>
                    <li>
                        <i class="fa fa-linkedin clr5"></i>
                    </li>
                    <li>
                        <i class="fa fa-windows clr6"></i>
                    </li>
                </ul>
            </div>
        </section>
        <!-- //banner bottom -->
        <!-- why choose us -->
        <section class="choose py-5" id="choose">
            <div class="container py-md-3">
                <h3 class="heading mb-5 text-center"> Por qué elegirnos</h3>
                <div class="feature-grids row">
                    <div class="col-lg-4 col-md-6">
                        <div class="f1 icon1 p-4">
                            <i class="fa fa-bandcamp"></i>
                            <h3 class="my-3">Design & Branding</h3>
                            <p>Excepteur sint occaecat non proident, sunt in culpa quis. Phasellus lacinia id erat eu ullamcorper. Nunc id ipsum.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="f1 icon2 p-4">
                            <i class="fa fa-codepen"></i>
                            <h3 class="my-3">Safe & Secure</h3>
                            <p>Excepteur sint occaecat non proident, sunt in culpa quis. Phasellus lacinia id erat eu ullamcorper. Nunc id ipsum.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mt-lg-0 mt-4">
                        <div class="f1 icon3 p-4">
                            <i class="fa fa-bitcoin"></i>
                            <h3 class="my-3">Fresh Interfaces</h3>
                            <p>Excepteur sint occaecat non proident, sunt in culpa quis. Phasellus lacinia id erat eu ullamcorper. Nunc id ipsum.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- //why choose us -->
        <!-- quote -->
        <section class="quote bg-light py-5">
            <div class="container py-md-3">
                <h4>Todas las ventajas de la web en una sola suite para apoyar la tarea de administrar desde cualquier lugar.</h4>
                <div class="start text-right mt-4">
                    <a href="#contact" class="scroll">Inicia ya</a>
                </div>
                <div class="clearfix"></div>
            </div>
        </section>
        <!-- //quote -->
        <!-- process -->
        <section class="process py-5" id="overview">
            <div class="container py-md-5">
                <div class="row process-grids">
                    <div class="col-lg-6">
                        <h4 class="mb-4">Excepteur sint occaecat non lorem proident, sunt in culpa quis.</h4>
                        <p class="mb-3">Morbi tincidunt nisi tortor, iaculis maximus eros vestibulum at. Ut pulvinar tortor non augue fringilla, fermentum consequat
                            nisi rutrum. Orci varius natoque penatibus et magnis dis parturient montes, nascetur. Nullam luctus hendrerit sapien, sed dictum est.
                            mattis egestas.
                        </p>
                        <p>Morbi tincidunt nisi tortor, iaculis maximus eros vestibulum at. Ut pulvinar tortor non augue fringilla, fermentum consequat
                            nisi rutrum. Orci varius natoque penatibus et magnis dis parturient montes.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <img src="images/b1.jpg" alt="" class="img-fluid"/>
                    </div>
                    <div class="col-md-6 px-5 mt-5">
                        <img src="images/b2.jpg" alt="" class="img-fluid"/>
                    </div>
                    <div class="col-lg-6 mt-5">
                        <h4 class="mb-4">Excepteur sint occaecat non lorem proident, sunt in culpa quis.</h4>
                        <p class="mb-3">Morbi tincidunt nisi tortor, iaculis maximus eros vestibulum at. Ut pulvinar tortor non augue fringilla, fermentum consequat
                            nisi rutrum. Orci varius natoque penatibus et magnis dis parturient montes, nascetur. Nullam luctus hendrerit sapien, sed dictum est.
                            mattis egestas.
                        </p>
                        <p>Morbi tincidunt nisi tortor, iaculis maximus eros vestibulum at. Ut pulvinar tortor non augue fringilla, fermentum consequat
                            nisi rutrum. Orci varius natoque penatibus et magnis dis parturient montes.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- //process -->
        <!--/pricing -->
        <section class="pricing bg-light py-5" id="pricing">
            <div class="container py-lg-3">
                <div class="inner-sec">
                    <h3 class="heading mb-5 text-center"> Pricing plans</h3>
                    <div class="price-right">
                        <div class="tabs">
                            <ul class="nav nav-pills my-md-5 my-3 justify-content-center" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Weekly</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Monthly</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                    <div class="menu-grids">
                                        <div class="row t-in">
                                            <div class="col-lg-3 col-sm-6 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Basic</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class=""></span>Free
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li>100 MB Disk Space</li>
                                                            <li>2 Sub domains</li>
                                                            <li>5 Email Accounts</li>
                                                            <li>24/7 support</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Select</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 mt-md-0 mt-5 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Satandard</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class="">$</span>30<span>/mon</span>
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li>100 MB Disk Space</li>
                                                            <li>2 Sub domains</li>
                                                            <li>5 Email Accounts</li>
                                                            <li>24/7 support</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Select</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 mt-lg-0 mt-5 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Business</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class="">$</span>90<span>/mon</span>
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li>100 MB Disk Space</li>
                                                            <li>2 Sub domains</li>
                                                            <li>5 Email Accounts</li>
                                                            <li>24/7 support</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Select</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 mt-lg-0 mt-5 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Basic</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class="">$</span>120<span>/mon</span>
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li>100 MB Disk Space</li>
                                                            <li>2 Sub domains</li>
                                                            <li>5 Email Accounts</li>
                                                            <li>24/7 support</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Select</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                    <div class="menu-grids mt-4">
                                        <div class="row t-in">
                                            <div class="col-lg-3 col-sm-6 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Standard</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class="">$</span>60
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li>100 MB Disk Space</li>
                                                            <li>2 Sub domains</li>
                                                            <li>5 Email Accounts</li>
                                                            <li>24/7 support</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Select</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 mt-md-0 mt-5 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Business</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class="">$</span>80
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li>100 MB Disk Space</li>
                                                            <li>2 Sub domains</li>
                                                            <li>5 Email Accounts</li>
                                                            <li>24/7 support</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Select</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 mt-lg-0 mt-5 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Business</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class="">$</span>90
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li>100 MB Disk Space</li>
                                                            <li>2 Sub domains</li>
                                                            <li>5 Email Accounts</li>
                                                            <li>24/7 support</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Select</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 mt-lg-0 mt-5 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Advance</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class="">$</span>90
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li>100 MB Disk Space</li>
                                                            <li>2 Sub domains</li>
                                                            <li>5 Email Accounts</li>
                                                            <li>24/7 support</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Select</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                <h3 class="heading mb-5 text-center"> Frequently Asked Questions</h3>
                <div class="row faq-info">
                    <div class=" col-md-6 faq-w3agile">
                        <ul class="faq pl-sm-4 pl-3">
                            <li class="item1 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>Lorem ipsum dolor sit amet? </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut
                                            laoreet dolore.
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li class="item2 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>Lorem ipsum dolor sit amet? </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut
                                            laoreet dolore.
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li class="item3 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>Lorem ipsum dolor sit amet? </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut
                                            laoreet dolore.
                                        </p>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class=" col-md-6 faq-w3agile">
                        <ul class="faq pl-sm-4 pl-3">
                            <li class="item5 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>Lorem ipsum dolor sit amet? </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut
                                            laoreet dolore.
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li class="item6 item mt-sm-4 mt-3 pt-3 pl-2"></li>
                                <h4>Lorem ipsum dolor sit amet? </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut
                                            laoreet dolore.
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li class="item7 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>Lorem ipsum dolor sit amet? </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut
                                            laoreet dolore.
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
        <section class="testimonials bg-light py-5" id="testimonials">
            <div class="container py-lg-3">
                <h3 class="heading mb-5 text-center"> Honorable Clients</h3>
                <div class="row test-grids">
                    <div class="col-md-4 col-sm-7 col-9">
                        <img src="images/b3.png" alt="" class="img-fluid" />
                    </div>
                    <div class="col-md-8">
                        <div class="callbacks_container">
                            <ul class="rslides" id="slider3">
                                <li>
                                    <div class="testi-pos">
                                        <h4>john watson</h4>
                                        <span class="">- congue leo</span>
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
                                            <i class="fa fa-quote-left pr-3"></i>Donec rutrum congue leo eget consectetur sed, convallis at tellus. Nulla quis
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
                                            <li><i class="fa mr-1 fa-star"></i></li>
                                            <li><i class="fa mr-1 fa-star"></i></li>
                                            <li><i class="fa mr-1 fa-star"></i></li>
                                            <li><i class="fa mr-1 fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                        </ul>
                                    </div>
                                    <div class="testi-agile">
                                        <p>
                                            <i class="fa fa-quote-left pr-3"></i>Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Nulla
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
        </section>
        <!-- //testimonials -->
        <!-- contact -->
        <section class="contact py-5" id="contact">
            <div class="container py-lg-3">
                <h3 class="heading mb-5 text-center"> Get in touch</h3>
                <form action="#" method="post">
                    <div class="row">
                        <div class="col-md-6 styled-input mt-0">
                            <input type="text" name="Name" placeholder="First Name" required="">
                        </div>
                        <div class="col-md-6 styled-input mt-md-0">
                            <input type="text" name="Name" placeholder="Last Name" required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 styled-input">
                            <input type="email" name="Email" placeholder="Email" required=""> 
                        </div>
                        <div class="col-md-6 styled-input">
                            <input type="text" name="phone" placeholder="Phone Number" required="">
                        </div>
                    </div>
                    <div class="styled-input">
                        <textarea name="Message" placeholder="Message" required=""></textarea>
                    </div>
                    <div class="click text-center mt-3">
                        <input type="submit" value="Submit">
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
                            <a href="{{ url('/') }}"><i class="fa fa-bed mr-2"></i> {{ config('app.name') }}</a>
                        </div>
                        <div class="footer-text">
                            <p>Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Nulla quis lorem ipnut libero malesuada feugiat.
                                Lorem ipsum dolor sit amet, consectetur elit.
                            </p>
                        </div>
                        <ul class="social_section_1info">
                            <li class="mb-2 facebook"><a href="#"><i class="fa mr-1 fa-facebook-f"></i>facebook</a></li>
                            <li class="mb-2 twitter"><a href="#"><i class="fa mr-1 fa-twitter"></i>twitter</a></li>
                            <li class="google"><a href="#"><i class="fa mr-1 fa-google-plus-g"></i>google</a></li>
                            <li class="linkedin"><a href="#"><i class="fa mr-1 fa-linkedin-in"></i>linkedin</a></li>
                        </ul>
                    </div>
                    <div class="row footer-top mt-md-5 mt-4">
                        <div class="col-lg-4 col-md-6 footer-grid_section_1its_w3">
                            <div class="footer-title">
                                <h3>Address</h3>
                            </div>
                            <div class="footer-text">
                                <p>Address : 1234 lock, Charlotte, North Carolina, United States</p>
                                <p>Phone : +12 534894364</p>
                                <p>Email : <a href="mailto:info@example.com">info@example.com</a></p>
                                <p>Fax : +12 534894364</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-md-0 mt-4 footer-grid_section_1its_w3">
                            <div class="footer-title">
                                <h3>Quick Links</h3>
                            </div>
                            <div class="row">
                                <ul class="col-6 links">
                                    <li><a href="#choose" class="scroll">Why Choose Us </a></li>
                                    <li><a href="#overview" class="scroll">Overview </a></li>
                                    <li><a href="#pricing" class="scroll">Pricing Plans</a></li>
                                    <li><a href="#faq" class="scroll">Faq's </a></li>
                                    <li><a href="#testimonials" class="scroll">Testimonial </a></li>
                                    <li><a href="#contact" class="scroll">Contact </a></li>
                                </ul>
                                <ul class="col-6 links">
                                    <li><a href="#">Privacy Policy </a></li>
                                    <li><a href="#">General Terms </a></li>
                                    <li><a href="#faq" class="scroll">Faq's </a></li>
                                    <li><a href="#">Knowledge </a></li>
                                    <li><a href="#">Forum </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12 mt-lg-0 mt-4 col-sm-12 footer-grid_section_1its_w3"  style="max-width: 100%;">
                            <div class="footer-title">
                                <h3>Newsletter</h3>
                            </div>
                            <div class="footer-text">
                                <p>By subscribing to our mailing list you will always get latest news and updates from us.</p>
                                <form action="#" method="post">
                                    <input type="email" name="Email" placeholder="Enter your email..." required="">
                                    <button class="btn1"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
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
            <p class="">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved | Design by
                <a href="http://w3layouts.com"> W3layouts.</a>
            </p>
        </div>
        <!-- //copyright -->
        <!-- Vertically centered Modal -->
        <div class="modal fade" id="exampleModalCenter1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenter1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-capitalize text-center" id="exampleModalLongTitle"> <i class="fa fa-bed"></i> {{ config('app.name') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <img src="images/banner.jpg" class="img-fluid mb-3" alt="Modal Image" />
                        Vivamus eget est in odio tempor interdum. Mauris maximus fermentum arcu, ac finibus ante. Sed mattis risus at ipsum elementum,
                        ut auctor turpis cursus. Sed sed odio pharetra, aliquet velit cursus, vehicula enim. Mauris porta aliquet magna, eget laoreet ligula.
                        Sed mattis risus at ipsum elementum, ut auctor turpis cursus. Sed sed odio pharetra, aliquet.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- //Vertically centered Modal -->
        <!-- video Modal Popup -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Video Overview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body video">
                        {{-- <iframe src="https://player.vimeo.com/video/43982091"></iframe> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- //video Model Popup -->
    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ mix('js/landing.js') }}"></script>
    <script>
		// AOS.init({
        //     easing: 'ease-out-back',
        //     duration: 1000
        // });

        // You can also use"$(window).load(function() {"
        // $(function () {
        //     // Slideshow 4
        //     $("#slider3").responsiveSlides({
        //         auto: true,
        //         pager: true,
        //         nav: false,
        //         speed: 500,
        //         namespace: "callbacks",
        //         before: function () {
        //             $('.events').append("<li>before event fired.</li>");
        //         },
        //         after: function () {
        //             $('.events').append("<li>after event fired.</li>");
        //         }
        //     });

        // });

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

		 /*
		  //Smooth Scrolling Using Navigation Menu
		  $('a[href*="#"]').on('click', function (e) {
			$('html,body').animate({
			  scrollTop: $($(this).attr('href')).offset().top - 100
			}, 500);
			e.preventDefault();
		  });
		 */

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

        jQuery(document).ready(function ($) {
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
    </script>
</body>
</html>
