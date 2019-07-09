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
                <a class="navbar-brand js-scroll-trigger" href="#page-top">Welkome</a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fa fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="#projects">Projects</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="#signup">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Header -->
        <header class="masthead">
            <div class="container d-flex h-100 align-items-center">
                <div class="mx-auto text-center">
                    <h1 class="mx-auto my-0 text-uppercase">Welkome</h1>
                    <h2 class="text-white-50 mx-auto mt-2 mb-5">A free, responsive, one page Bootstrap theme created by Start Bootstrap.</h2>
                    <a href="#about" class="btn btn-primary js-scroll-trigger">Get Started</a>
                </div>
            </div>
        </header>
        <!-- About Section -->
        <section id="about" class="about-section text-center">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <h2 class="text-white mb-4">Built with Bootstrap 4</h2>
                        <p class="text-white-50">Welkome is a free Bootstrap theme created by Start Bootstrap. It can be yours right now, simply download the template on
                            <a href="http://startbootstrap.com/template-overviews/grayscale/">the preview page</a>. The theme is open source, and you can use it for any purpose, personal or commercial.
                        </p>
                    </div>
                </div>
                <img src="images/ipad.png" class="img-fluid" alt="">
            </div>
        </section>
        <!-- Projects Section -->
        <section id="projects" class="projects-section bg-light">
            <div class="container">
                <!-- Featured Project Row -->
                <div class="row align-items-center no-gutters mb-4 mb-lg-5">
                    <div class="col-xl-8 col-lg-7">
                        <img class="img-fluid mb-3 mb-lg-0" src="images/bg-masthead.jpg" alt="">
                    </div>
                    <div class="col-xl-4 col-lg-5">
                        <div class="featured-text text-center text-lg-left">
                            <h4>Shoreline</h4>
                            <p class="text-black-50 mb-0">Grayscale is open source and MIT licensed. This means you can use it for any project - even commercial projects! Download it, customize it, and publish your website!</p>
                        </div>
                    </div>
                </div>
                <!-- Project One Row -->
                <div class="row justify-content-center no-gutters mb-5 mb-lg-0">
                    <div class="col-lg-6">
                        <img class="img-fluid" src="images/demo-image-01.jpg" alt="">
                    </div>
                    <div class="col-lg-6">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-left">
                                    <h4 class="text-white">Misty</h4>
                                    <p class="mb-0 text-white-50">An example of where you can put an image of a project, or anything else, along with a description.</p>
                                    <hr class="d-none d-lg-block mb-0 ml-0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Project Two Row -->
                <div class="row justify-content-center no-gutters">
                    <div class="col-lg-6">
                        <img class="img-fluid" src="images/demo-image-02.jpg" alt="">
                    </div>
                    <div class="col-lg-6 order-lg-first">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-right">
                                    <h4 class="text-white">Mountains</h4>
                                    <p class="mb-0 text-white-50">Another example of a project with its respective description. These sections work well responsively as well, try this theme on a small screen!</p>
                                    <hr class="d-none d-lg-block mb-0 mr-0">
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
                        <h2 class="text-white mb-5">Subscribe to receive updates!</h2>
                        <form class="form-inline d-flex">
                            <input type="email" class="form-control flex-fill mr-0 mr-sm-2 mb-3 mb-sm-0" id="inputEmail" placeholder="Enter email address...">
                            <button type="submit" class="btn btn-primary mx-auto">Subscribe</button>
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
                                <h4 class="text-uppercase m-0">Address</h4>
                                <hr class="my-4">
                                <div class="small text-black-50">4923 Market Street, Orlando FL</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fa fa-envelope text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Email</h4>
                                <hr class="my-4">
                                <div class="small text-black-50">
                                    <a href="#">hello@yourdomain.com</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fa fa-mobile text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Phone</h4>
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
                Copyright &copy; Your Website 2019
            </div>
        </footer>
    </div>
    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/index.js') }}"></script>
</body>
</html>
