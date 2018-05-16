<!--
Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <title>{{ config('app.name', 'Welkome') }}</title>
    <meta charset="utf-8">
    <meta name="keywords" content="Hotel" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
	<script type="application/x-javascript">
		addEventListener("load", function () {
			setTimeout(hideURLbar, 0);
		}, false);

		function hideURLbar() {
			window.scrollTo(0, 1);
		}
	</script>

    <!-- Icon -->
    <link href="{{ asset('images/icon-1.png') }}" rel="shortcut icon" type="image/x-icon">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/welkome.css') }}" rel="stylesheet">

	<!-- web font --> 
	<link href="//fonts.googleapis.com/css?family=Raleway:100,200i,300,300i,400,400i,500,500i,600,700,800" rel="stylesheet">
	<link href="//fonts.googleapis.com/css?family=Poppins:100i,200,200i,300,300i,400,400i,500" rel="stylesheet">
	<link href="//fonts.googleapis.com/css?family=Pacifico" rel="stylesheet">
	<!-- //web font --> 

</head>

<body>
    <div id="app">
        <!-- banner -->
        <div class="banner_nav_w3layouts">
            <nav class="navbar navbar-default">
                <div class="navbar-header navbar-left">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    <h1><a class="navbar-brand" href="{{ url('/') }}"><i class="fa fa-bed" aria-hidden="true"></i> {{ config('app.name') }}</a></h1>

                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
                    <nav>
                        <ul class="nav navbar-nav">
                            <li class="active"><a href="{{ url('/') }}" class="hvr-underline-from-center">Home</a></li>
                            <li><a href="#about" class="hvr-underline-from-center scroll">About</a></li>
                            <li><a href="#services" class="hvr-underline-from-center scroll">Services</a></li>
                            <li><a href="#clients" class="hvr-underline-from-center scroll">Clients</a></li>
                            <li><a href="#portfolio" class="hvr-underline-from-center scroll">Projects</a></li>
                            <li><a href="#contact" class="hvr-underline-from-center scroll">Contact</a></li>
                        </ul>
                    </nav>

                </div>
            </nav>

            <div class="clearfix"> </div>
        </div>

        <div class="banner" id="home">
            <div class="container">
                <div class="banner_info_w3ls_agile">
                    <h3><span>Hello</span> I’m Honey Leo</h3>
                    <h6>UI/UX Designer.</h6>
                    <p>Lorem ipsum dolor sit amet, Nam arcu mauris, tincidunt Cras sapien urna, malesuada ut varius consequat.</p>
                    <ul class="slide-up">
                        @if(Auth::check())
                            <li><a href="{{ route('home') }}" class="w3ls_more">@lang('dashboard.dashboard')</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="w3ls_more">@lang('login.identification')</a></li>
                            <li><a href="#"><i class="fa fa-angle-double-up" aria-hidden="true"></i> @lang('login.register')</a></li>
                        @endif
                    </ul>
                </div>

                <div class="banner-image-w3layouts">
                    <img src="{{ asset('images/men.png') }}" alt=" " class="img-responsive" />
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <!-- //banner -->
        
        <!-- /banner_bottom -->
        <div class="banner_bottom" id="about">
            <div class="container">
                <div class="inner_sec_top_aglieits">
                    <div class="banner_bottom_info">
                        <h4><img src="{{ asset('images/men_2.jpg') }}" alt=" " class="img-responsive"> Here you will find my resume...</h4>
                        <h6>I am 26 years old front-end developer from New York Times - in the business since 2011!</h6>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque nec congue risus. In non nulla lacus. Maecenas
                            eros sapien, eleifend quis augue eu, pellentesque tincidunt tellus</p>

                    </div>
                    <ul class="address">
                        <li><i class="fa fa-map-marker" aria-hidden="true"></i> Honey block,New York City.</li>
                        <li><i class="fa fa-envelope" aria-hidden="true"></i><a href="mailto:info@example.com">info@example.com</a></li>
                        <li><i class="fa fa-phone" aria-hidden="true"></i>+09187 8088 9436</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- //banner_bottom -->
        
        <!-- //services -->
        <div class="servies" id="services">
            <div class="container">
                <h3 class="tittle_w3layouts two">My Services</h3>
                <p class="sub_para two yellow">What I Love to Do</p>
                <div class="skill_info_wthree_agile">
                    <div class="col-md-4 banner_bottom_left">
                        <div class="banner_bottom_pos_w3ls">
                            <div class="banner_bottom_pos_grid">
                                <div class="col-xs-3 banner_bottom_grid_left">
                                    <div class="bottom_grid_left_grid">
                                        <i class="fa fa-laptop" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <div class="col-xs-9 banner_bottom_grid_right">
                                    <h4>Web Development</h4>
                                </div>
                                <div class="clearfix"> </div>
                                <p>Lorem ipsum dolor, consectetur adipiscing elit,morbi viverra lacus commodo felis semper.</p>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 banner_bottom_left">
                        <div class="banner_bottom_pos_w3ls">
                            <div class="banner_bottom_pos_grid">
                                <div class="col-xs-3 banner_bottom_grid_left">
                                    <div class="bottom_grid_left_grid">
                                        <i class="fa fa-paint-brush" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <div class="col-xs-9 banner_bottom_grid_right">
                                    <h4>Graphic Design</h4>
                                </div>
                                <div class="clearfix"> </div>
                                <p>Lorem ipsum dolor, consectetur adipiscing elit,morbi viverra lacus commodo felis semper.</p>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 banner_bottom_left">
                        <div class="banner_bottom_pos_w3ls">
                            <div class="banner_bottom_pos_grid">
                                <div class="col-xs-3 banner_bottom_grid_left">
                                    <div class="bottom_grid_left_grid">
                                        <i class="fa fa-bullhorn" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <div class="col-xs-9 banner_bottom_grid_right">
                                    <h4>Digital Marketing</h4>
                                </div>
                                <div class="clearfix"> </div>
                                <p>Lorem ipsum dolor, consectetur adipiscing elit,morbi viverra lacus commodo felis semper.</p>

                            </div>
                        </div>
                    </div>

                    <div class="clearfix"> </div>
                </div>
            </div>
        </div>
        <!-- //services -->

        <!-- stats -->
        <div class="stats" id="stats">
            <div class="container">
                <h3 class="tittle_w3layouts two">Satisfied Clients</h3>
                <p class="sub_para two ">What I Love to Do</p>
                <div class="skill_info_wthree_agile">
                    <div class="col-md-4 stats_left counter_grid">
                        <div class="icon">
                            <i class="fa fa-laptop" aria-hidden="true"></i>
                        </div>
                        <div class="icon_info_agileits">
                            <p class="counter">65</p>
                            <h3>Digital Market Place</h3>
                        </div>
                        <div class="clearfix"> </div>
                    </div>
                    <div class="col-md-4 stats_left counter_grid1">
                        <div class="icon">
                            <i class="fa fa-user" aria-hidden="true"></i>
                        </div>
                        <div class="icon_info_agileits">
                            <p class="counter">563</p>
                            <h3>Made for Elite Clients</h3>
                        </div>
                        <div class="clearfix"> </div>
                    </div>
                    <div class="col-md-4 stats_left counter_grid2">
                        <div class="icon">
                            <i class="fa fa-trophy" aria-hidden="true"></i>

                        </div>
                        <div class="icon_info_agileits">
                            <p class="counter">245</p>
                            <h3>Won On Contests</h3>
                        </div>
                        <div class="clearfix"> </div>
                    </div>
                    <div class="clearfix"> </div>
                </div>
            </div>
        </div>
        <!-- //stats -->
        
        <!-- /testimonials -->
        <div class="testimonials_section" id="clients">
            <div class="container">
                <h3 class="tittle_w3layouts two">My Clients </h3>
                <p class="sub_para two "> Clients think about me</p>
                <div class="skill_info_wthree_agile">
                    <div id="Carousel" class="carousel slide two">

                        <ol class="carousel-indicators second">
                            <li data-target="#Carousel" data-slide-to="0" class="active"></li>
                            <li data-target="#Carousel" data-slide-to="1"></li>
                            <li data-target="#Carousel" data-slide-to="2"></li>
                        </ol>

                        <!-- Carousel items -->
                        <div class="carousel-inner">

                            <div class="item active">
                                <div class="testimonials_grid_wthree">
                                    <img src="{{ asset('images/test1.jpg') }}" alt=" " class="img-responsive" />
                                    <h4><i class="fa fa-quote-left" aria-hidden="true"></i> Nam libero tempore, cum soluta nobis est eligendi optio cumque
                                        nihil impedit quo minus id quod maxime placeat facere possimus,Morbi viverra congue nisi vel pulvinar posuere sapien
                                        eros. omnis voluptas.</h4>
                                    <h5>Dan Adams</h5>

                                </div>

                            </div>
                            <!--.item-->
                            <div class="item">
                                <div class="testimonials_grid_wthree">
                                    <img src="{{ asset('images/test2.jpg') }}" alt=" " class="img-responsive" />
                                    <h4><i class="fa fa-quote-left" aria-hidden="true"></i> Nam libero tempore, cum soluta nobis est eligendi optio cumque
                                        nihil impedit quo minus id quod maxime placeat facere possimus,Morbi viverra congue nisi vel pulvinar posuere sapien
                                        eros. omnis voluptas.</h4>
                                    <h5>Jessica Doe</h5>

                                </div>
                            </div>
                            <!--.item-->

                            <div class="item">
                                <div class="testimonials_grid_wthree">
                                    <img src="{{ asset('images/test3.jpg') }}" alt=" " class="img-responsive" />
                                    <h4><i class="fa fa-quote-left" aria-hidden="true"></i> Nam libero tempore, cum soluta nobis est eligendi optio cumque
                                        nihil impedit quo minus id quod maxime placeat facere possimus,Morbi viverra congue nisi vel pulvinar posuere sapien
                                        eros. omnis voluptas.</h4>
                                    <h5>Michael Doe</h5>

                                </div>
                            </div>
                            <!--.item-->

                        </div>
                        <!--.carousel-inner-->

                    </div>
                    <!--.Carousel-->

                </div>
            </div>
        </div>
        <!-- //testimonials -->
        
        <!-- /portfolio-->
        <div class="portfolio-project" id="portfolio">
            <div class="container">
                <h3 class="tittle_w3layouts two con">My Projects</h3>
                <p class="sub_para two con">What I Love to Do</p>
                <div class="skill_info_wthree_agile">
                    <div class="agile_port_w3ls_info">
                        <div class="col-md-6 portfolio-grids_left">
                            <div class="col-md-6 portfolio-grids" data-aos="zoom-in">
                                <a href="{{ asset('images/g1.jpg') }}" class="b-link-stripe b-animate-go lightninBox" data-lb-group="1">
                                    <img src="{{ asset('images/g1.jpg') }}" class="img-responsive" alt=" " />
                                    <div class="b-wrapper two">
                                        
                                        
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 portfolio-grids" data-aos="zoom-in">
                                <a href="{{ asset('images/g2.jpg') }}" class="b-link-stripe b-animate-go lightninBox" data-lb-group="1">
                                    <img src="{{ asset('images/g2.jpg') }}" class="img-responsive" alt=" " />
                                    <div class="b-wrapper">
                                    
                                        
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 portfolio-grids" data-aos="zoom-in">
                                <a href="{{ asset('images/g3.jpg') }}" class="b-link-stripe b-animate-go lightninBox" data-lb-group="1">
                                    <img src="{{ asset('images/g3.jpg') }}" class="img-responsive" alt=" " />
                                    <div class="b-wrapper">
                                        
                                        
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 portfolio-grids" data-aos="zoom-in">
                            <a href="{{ asset('images/g10.jpg') }}" class="b-link-stripe b-animate-go lightninBox" data-lb-group="1">
                                    <img src="{{ asset('images/g10.jpg') }}" class="img-responsive" alt=" " />
                                    <div class="b-wrapper">
                                        
                                        
                                    </div>
                                </a>
                        </div>
                        <div class="col-md-6 portfolio-grids" data-aos="zoom-in">
                            <a href="{{ asset('images/g11.jpg') }}" class="b-link-stripe b-animate-go lightninBox" data-lb-group="1">
                                    <img src="{{ asset('images/g11.jpg') }}" class="img-responsive" alt=" " />
                                    <div class="b-wrapper">
                                        
                                        
                                    </div>
                                </a>
                        </div>
                        <div class="col-md-6 portfolio-grids_left">
                            <div class="col-md-6 portfolio-grids" data-aos="zoom-in">
                                <a href="{{ asset('images/g5.jpg') }}" class="b-link-stripe b-animate-go lightninBox" data-lb-group="1">
                                    <img src="{{ asset('images/g5.jpg') }}" class="img-responsive" alt=" " />
                                    <div class="b-wrapper two">
                                        
                                        
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 portfolio-grids" data-aos="zoom-in">
                                <a href="{{ asset('images/g4.jpg') }}" class="b-link-stripe b-animate-go lightninBox" data-lb-group="1">
                                    <img src="{{ asset('images/g4.jpg') }}" class="img-responsive" alt=" " />
                                    <div class="b-wrapper">
                                    
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 portfolio-grids" data-aos="zoom-in">
                                <a href="{{ asset('images/g6.jpg') }}" class="b-link-stripe b-animate-go lightninBox" data-lb-group="1">
                                    <img src="{{ asset('images/g6.jpg') }}" class="img-responsive" alt=" " />
                                    <div class="b-wrapper">
                                    
                                        
                                    </div>
                                </a>
                            </div>

                        </div>
                        <div class="clearfix"> </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- /contact -->
        <div class="contact_sec" id="contact">
            <div class="container">
                <h3 class="tittle_w3layouts two con">Contact Me</h3>
                <p class="sub_para two">What I Love to Do</p>
                <div class="contact-info skill_info">
                    <form action="#" method="post">
                        <input type="text" name="Your Name" placeholder="Name" required="">
                        <input type="text" name="Your Phone Number" placeholder="Your Phone Number" required="">
                        <input type="text" name="Your Subject" placeholder="Your Subject" required="">
                        <input type="email" name="Your Email" placeholder="Your Email" required="">
                        <textarea name="Message" placeholder="Message" required=""></textarea>
                        <div class="clearfix"> </div>
                        <input type="submit" value="Submit">
                    </form>

                </div>
                <ul class="address two">
                    <li><i class="fa fa-map-marker" aria-hidden="true"></i> Honey block,New York City.</li>
                    <li><i class="fa fa-envelope" aria-hidden="true"></i><a href="mailto:info@example.com">info@example.com</a></li>
                    <li><i class="fa fa-phone" aria-hidden="true"></i>+09187 8088 9436</li>
                </ul>
            </div>
        </div>
        <!-- //contact -->
        
        <!-- /footer -->
        <div class="footer">
            <div class="container">
            <p>© {{ date('Y') }} {{ config('app.name') }} . @lang('login.license') | @lang('login.design') <a href="http://w3layouts.com">W3layouts</a></p>
            </div>
        </div>
        <!-- //footer -->
    </div>
    
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/welkome.js') }}"></script>
    <script type="text/javascript">
        $('.counter').countUp();

		$(function () {
			$('.portfolio-grids a').Chocolat();
        });

		jQuery(document).ready(function ($) {
			$(".scroll").click(function (event) {
				event.preventDefault();
				$('html,body').animate({
					scrollTop: $(this.hash).offset().top
				}, 1000);
			});
        });

		$(document).ready(function () {
			/*
				var defaults = {
				containerID: 'toTop', // fading element id
				containerHoverID: 'toTopHover', // fading element hover id
				scrollSpeed: 1200,
				easingType: 'linear' 
				};
			*/
			$().UItoTop({
				easingType: 'easeOutQuart'
			});
		});
    </script>
    <div class="arr-w3ls">
        <a href="#home" id="toTop" style="display: block;"> <span id="toTopHover" style="opacity: 1;"> </span></a>
    </div>
</body>

</html>
