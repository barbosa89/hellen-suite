<!--
Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <title>{{ config('app.name') }}</title>
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
                                <ul id="menu-list" class="text-center nav-agile">
                                    <li>
                                        <a href="{{ url('/') }}" class="active">Inicio</a>
                                    </li>
                                    <li>
                                        <a href="#choose" class="scroll">Por qué elegirnos</a>
                                    </li>
                                    <li>
                                        <a href="#overview" class="scroll">La aplicación</a>
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
                                        @if (auth()->check())
                                            <a href="{{ url('/home') }}" class="btn w3ls-btn" href="#contact" class="scroll">Panel</a>
                                        @else
                                            <a href="{{ url('/login') }}" class="btn w3ls-btn" href="#contact" class="scroll">Iniciar sesión</a>
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
                                La suite más completa
                            </h2>
                        </div>
                        <h3 class="txt-w3_agile">para administrar tu hotel de manera fácil.</h3>
                        <a class="btn mt-4 mr-2 text-capitalize"  href="#overview" role="button">Leer más</a>
                        <a class="btn mt-4 text-capitalize"  href="#" data-toggle="modal" data-target="#exampleModal" role="button">Ver vídeo<i class="fa fa-play-circle"></i></a>
                    </div>
                    <div class="col-lg-4 col-md-8 mt-lg-0 mt-5 banner-form">
                        <h5><i class="fas mr-2 fa-laptop"></i> Regístrate</h5>
                        <form action="{{ route('register') }}" class="mt-4" method="post">
                            @csrf
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" placeholder="Nombre" required="" />
                            @error('name')
                                <span class="error-message invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" id="email" placeholder="Correo electrónico" required="" />
                            @error('email')
                                <span class="error-message invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" id="password" placeholder="Contraseña" required="" />
                            @error('password')
                                <span class="error-message invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirma contraseña" required="" />
                            @error('password_confirmation')
                                <span class="error-message invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

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
                <h4 class="text-center">Con el respaldo de las mejores tecnologías</h4>
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
        <!-- why choose us -->
        <section class="choose py-5" id="choose">
            <div class="container py-md-3">
                <h3 class="heading mb-5 text-center"> Por qué elegirnos</h3>
                <div class="feature-grids row">
                    <div class="col-lg-4 col-md-6">
                        <div class="f1 icon1 p-4">
                            <i class="fas fa-cubes"></i>
                            <h3 class="my-3">Componentes</h3>
                            <p>Puedes administrar inventarios de productos, activos, utilería, asignación y reservación de habitaciones, sedes, empleados, vehículos y más.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="f1 icon2 p-4">
                            <i class="fas fa-shield-alt"></i>
                            <h3 class="my-3">Velocidad & seguridad</h3>
                            <p>La estructura de la aplicación es simple y potente. Toda la información está asegurada por múltiples capas para garantizar su disponibilidad.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mt-lg-0 mt-4">
                        <div class="f1 icon3 p-4">
                            <i class="fas fa-palette"></i>
                            <h3 class="my-3">Diseño moderno</h3>
                            <p>Adaptable a cualquier dispositivo, interfaz elegante e intuitiva, integra un panel de control, gráficas e informes, para que tomes decisiones basadas en datos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- //why choose us -->
        <!-- quote -->
        <section class="quote bg-light py-5">
            <div class="container py-md-3">
                <h4>Todas las ventajas de la web en una suite para apoyar las tareas de administración. Fácil, potente y desde cualquier lugar.</h4>
                <div class="start text-right mt-4">
                    <a href="#contact" class="scroll">Contacto</a>
                </div>
                <div class="clearfix"></div>
            </div>
        </section>
        <!-- //quote -->
        <!-- process -->
        <section class="process py-5" id="overview">
            <div class="container py-md-5">
                <div class="row process-grids">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                        <h4 class="mb-4">¿Qué es {{ config('app.name') }}?</h4>
                        <p class="mb-3">{{ config('app.name') }} es una aplicación web para la administración de hoteles y modelos de negocio similares,
                            está orientada a facilitar controles operativos, y desarrollo de funciones administrativas y de gerencia.
                        </p>
                        <p>
                            Está compuesta por diferentes módulos especializados y también comunes de la empresa, como lo son facturación,
                            inventarios y demás. Entre los módulos especializados, se encuentran el potente paquete de bitácora y el de utilería,
                            con los cuales tendrás información valiosa, y en tiempo real, de los acontecimientos del día a día.
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
                        <h4 class="mb-4">Filosofía</h4>
                        <p class="mb-3">
                                La logística hotelera demanda el manejo de una gran cantidad de recursos con el fin de alcanzar alto grado
                                de satisfacción en sus clientes; {{ config('app.name') }}, permite la optimización de todos los procesos, y asegura la
                                continuidad operativa, con el único objetivo que la administración sea literalmente fácil.
                        </p>
                        <p>Para ello, existen diferentes planes de acceso a la plataforma, puedes elegir el plan básico y después
                            abonarte a un mejor plan con todas las funcionalidades.
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
                    <h3 class="heading mb-5 text-center"> Precios de los planes</h3>
                    <div class="price-right">
                        <div class="tabs">
                            <ul class="nav nav-pills my-md-5 my-3 justify-content-center" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Web</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Escritorio</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                    <div class="menu-grids">
                                        <div class="row t-in">
                                            <div class="col-lg-3 col-md-6 col-sm-6 col-12 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Básico</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class=""></span>Gratis<span>/siempre</span>
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li><i class="fas fa-check"></i> 1 hotel</li>
                                                            <li><i class="fas fa-check"></i> 3 usuarios</li>
                                                            <li><i class="fas fa-check"></i> 30 habitaciones</li>
                                                            <li><i class="fas fa-times"></i> Módulos</li>
                                                            <li><i class="fas fa-check"></i> Soporte por email</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Seleccionar</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-sm-6 col-12 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Intermedio</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class="">$</span>80.000<span>/semestre</span>
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li><i class="fas fa-infinity"></i> Sedes</li>
                                                            <li><i class="fas fa-infinity"></i> Usuarios</li>
                                                            <li><i class="fas fa-infinity"></i> Habitaciones</li>
                                                            <li><i class="fas fa-times"></i> Módulos</li>
                                                            <li><i class="fas fa-check"></i> 24/7 soporte</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Seleccionar</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-sm-6 col-12 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Estándar</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class="">$</span>120.000<span>/anual</span>
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li><i class="fas fa-infinity"></i> Sedes</li>
                                                            <li><i class="fas fa-infinity"></i> Usuarios</li>
                                                            <li><i class="fas fa-infinity"></i> Habitaciones</li>
                                                            <li><i class="fas fa-times"></i> Módulos</li>
                                                            <li><i class="fas fa-check"></i> 24/7 soporte</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Seleccionar</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-sm-6 col-12 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Completo</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class="">$</span>240.000<span>/anual</span>
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li><i class="fas fa-infinity"></i> Sedes</li>
                                                            <li><i class="fas fa-infinity"></i> Usuarios</li>
                                                            <li><i class="fas fa-infinity"></i> Habitaciones</li>
                                                            <li><i class="fas fa-check"></i> Módulos</li>
                                                            <li><i class="fas fa-check"></i> 24/7 soporte</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Seleccionar</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                    <div class="menu-grids">
                                        <div class="row t-in">
                                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Un equipo</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class="">$</span>500.000<span>/siempre</span>
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li><i class="fas fa-infinity"></i> Sedes</li>
                                                            <li><i class="fas fa-infinity"></i> Usuarios</li>
                                                            <li><i class="fas fa-infinity"></i> Habitaciones</li>
                                                            <li><i class="fas fa-infinity"></i> Módulos</li>
                                                            <li><i class="fas fa-check"></i> 24/7 soporte</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Seleccionar</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Notas</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            Un equipo
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li><i class="fas fa-check-circle"></i> Se realizará una única instalación</li>
                                                            <li><i class="fas fa-check-circle"></i> El costo indicado es por instalación realizada</li>
                                                            <li><i class="fas fa-check-circle"></i> No hay garantías por pérdidas de la información</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Red local</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            <span class="">$</span>1.000.000<span>/siempre</span>
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li><i class="fas fa-infinity"></i> Sedes</li>
                                                            <li><i class="fas fa-infinity"></i> Usuarios</li>
                                                            <li><i class="fas fa-infinity"></i> Habitaciones</li>
                                                            <li><i class="fas fa-infinity"></i> Módulos</li>
                                                            <li><i class="fas fa-check"></i> 24/7 soporte</li>
                                                        </ul>
                                                        <div class="log-in mt-md-3 mt-2">
                                                            <a class="btn scroll" href="#contact">Seleccionar</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 price-main-info">
                                                <div class="price-inner card box-shadow p-4">
                                                    <div class="card-body">
                                                        <h4 class="">Notas</h4>
                                                        <h5 class="card-title pricing-card-title">
                                                            Red local
                                                        </h5>
                                                        <ul class="list-unstyled mt-3 mb-4">
                                                            <li><i class="fas fa-check-circle"></i> No incluye el costo del montaje, servidor, cableado o equipos</li>
                                                            <li><i class="fas fa-check-circle"></i> Sólo incluye la configuración de servidor y el despliegue de la aplicación</li>
                                                        </ul>
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
                <h3 class="heading mb-5 text-center"> Preguntas frecuentes</h3>
                <div class="row faq-info">
                    <div class=" col-md-6 faq-w3agile">
                        <ul class="faq pl-sm-4 pl-3">
                            <li class="item1 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>Cuál es el soporte del plan básico? </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p> Se proporciona un correo de soporte técnico, a través del cual se solventan los inconvenientes en la plataforma.
                                            Las respuestas se darán en un plazo no mayor a 24 horas.
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li class="item2 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>Cuáles medios de pago están soportados? </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p> Tarjetas de crédito, débito, Efecty, Baloto, consignación, PSE, Daviplata, Puntored. Estos medios son los más comunes que ofrece el botón de pago de Epayco.
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li class="item3 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>Puedo administrar desde el teléfono o tableta? </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p> El diseño de sistema está orientado a funcionar en cualquer dispositivo, un característica conocida como diseño web responsivo.
                                        </p>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class=" col-md-6 faq-w3agile">
                        <ul class="faq pl-sm-4 pl-3">
                            <li class="item5 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>Como puedo iniciar? </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p> Puedes registrarte con algún plan, el correo usado será el de la cuenta principal, luego puedes crear hoteles y sedes, agregar colaboradores. Haz click aquí para <a href="#">ver el tutorial de registro</a>.
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li class="item6 item mt-sm-4 mt-3 pt-3 pl-2"></li>
                                <h4>De qué se trata el servicio de comedor? </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p> Los hoteles pueden tener servicios de comedor o restaurante, puedes asociar los consumos de tus clientes a través de este módulo con una simple huella o un pin.
                                        </p>
                                    </li>
                                </ul>
                            </li>
                            <li class="item7 item mt-sm-4 mt-3 pt-3 pl-2">
                                <h4>De que se traran los módulos? </h4>
                                <ul>
                                    <li class="subitem1 mt-3">
                                        <p>
                                            Es en conjunto de herramientas administrativas que comprenden control de productos, servicios, activos y utilería.
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
                <h3 class="heading mb-5 text-center"> Contacto</h3>
                <form action="#" method="post">
                    <div class="row">
                        <div class="col-md-6 styled-input mt-0">
                            <input type="text" name="Name" placeholder="Nombre" required="">
                        </div>
                        <div class="col-md-6 styled-input mt-md-0">
                            <input type="text" name="Name" placeholder="Apellido" required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 styled-input">
                            <input type="email" name="Email" placeholder="Correo electrónico" required="">
                        </div>
                        <div class="col-md-6 styled-input">
                            <input type="text" name="phone" placeholder="Número telefónico" required="">
                        </div>
                    </div>
                    <div class="styled-input">
                        <textarea name="Message" placeholder="Mensaje" required=""></textarea>
                    </div>
                    <div class="click text-center mt-3">
                        <input type="submit" value="Enviar">
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
                            <p>
                                La suite para que la administración de tu hotel sea fácil
                            </p>
                        </div>
                        <ul class="social_section_1info">
                            <li class="mb-2 facebook"><a href="#"><i class="fab mr-1 fa-facebook-f"></i>facebook</a></li>
                            <li class="mb-2 twitter"><a href="#"><i class="fab mr-1 fa-twitter"></i>twitter</a></li>
                            <li class="google"><a href="#"><i class="fab mr-1 fa-google-plus-g"></i>google</a></li>
                            <li class="linkedin"><a href="#"><i class="fab mr-1 fa-linkedin-in"></i>linkedin</a></li>
                        </ul>
                    </div>
                    <div class="row footer-top mt-md-5 mt-4">
                        <div class="col-lg-4 col-md-6 footer-grid_section_1its_w3">
                            <div class="footer-title">
                                <h3>Dirección</h3>
                            </div>
                            <div class="footer-text">
                                <p>Dirección : Bucaramanga - Santander</p>
                                <p>Móvil : <i class="fab fa-whatsapp"></i> +57 310 650 2285</p>
                                <p>Correo : <a href="mailto:contacto@omarbarbosa.com">contacto@omarbarbosa.com</a></p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mt-md-0 mt-4 footer-grid_section_1its_w3">
                            <div class="footer-title">
                                <h3>Quick Links</h3>
                            </div>
                            <div class="row">
                                <ul class="col-6 links">
                                    <li><a href="#choose" class="scroll">Por qué elergirnos</a></li>
                                    <li><a href="#overview" class="scroll">La aplicación </a></li>
                                    <li><a href="#pricing" class="scroll">Precios</a></li>
                                    <li><a href="#faq" class="scroll">Preguntas</a></li>
                                    <li><a href="#testimonials" class="scroll">Testimonios </a></li>
                                    <li><a href="#contact" class="scroll">Contacto </a></li>
                                </ul>
                                <ul class="col-6 links">
                                    <li><a href="#">Políticas de privacidad </a></li>
                                    <li><a href="#">Términos de uso </a></li>
                                    <li><a href="#faq" class="scroll">Preguntas</a></li>
                                    <li><a href="#">Tutoriales </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12 mt-lg-0 mt-4 col-sm-12 footer-grid_section_1its_w3"  style="max-width: 100%;">
                            <div class="footer-title">
                                <h3>Boletines</h3>
                            </div>
                            <div class="footer-text">
                                <p>Recibe por correo electrónico, todas las novedades del sitio.</p>
                                <form action="#" method="post">
                                    <input type="email" name="Email" placeholder="Ingresa tu correo.." required="">
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
            <p class="">© {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados | Diseñado por
                <a href="http://w3layouts.com"> W3layouts.</a>
            </p>
        </div>
        <!-- //copyright -->
        <!-- video Modal Popup -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Aprende sobre {{ config('app.name') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body video">
                        <iframe src="https://player.vimeo.com/video/43982091"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
