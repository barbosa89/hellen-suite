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
    
    <script>
        addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>

    <!-- Icon -->
    <link href="{{ asset('images/icon.png') }}" rel="shortcut icon" type="image/x-icon">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/welkome.css') }}" rel="stylesheet">

	<!-- web font --> 
	<link href="//fonts.googleapis.com/css?family=Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&amp;subset=latin-ext" rel="stylesheet">
	<!-- //web font --> 

</head>

<body>
    <div id="app">
        <!-- main -->
        <div class="main">
            <h1>{{ config('app.name', 'Welkome') }}</h1>
            <div class="main-w3lsrow">
                <!-- login form -->
                <div class="login-form login-form-left"> 
                    <div class="agile-row">
                        @if(Auth::guest())
                            <div class="head">
                                <h2>@lang('login.identification')</h2>
                                <span class="fa fa-lock"></span>
                            </div>					
                            <div class="clear"></div>
                            <div class="login-agileits-top"> 	   
                                <form action="{{ route('login') }}" method="post"> 
                                    @csrf
                                    <div class="form-group{{ $errors->has('email') ? ' is-invalid' : '' }}">
                                        <input type="email" class="name" name="email" Placeholder="@lang('login.email')" required="" value="{{ old("email") }}"/>
                                        
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('password') ? ' is-invalid' : '' }}">
                                        <input type="password" class="password" name="password" Placeholder="@lang('login.password')" required=""/>
                                    
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <input type="submit" value="@lang('login.signin')"> 
                                </form> 	
                            </div> 
                            <div class="login-agileits-bottom"> 
                                <h6><a href="#">@lang('login.reset')</a></h6>
                            </div> 
                        @else
                            <div class="head">
                                <h2>@lang('login.hi'), {{ auth()->user()->name }}</h2>
                                <span class="fa fa-lock"></span>
                            </div>
                            <p class="active-session-msn text-center">
                                @lang('login.active')
                            </p>
                            <div class="login-agileits-bottom">
                                <a href="{{ route('home') }}" class="w-btn unstyle-link">@lang('login.back')</a>
                            </div>
                        @endif
                    </div>  
                </div>  
            </div>
            <!-- //login form -->
            
            <div class="login-agileits-bottom1"> 
                <h3>@lang('login.support')</h3>
            </div>
            
            <!-- social icons -->
            <div class="social_icons agileinfo">
                <ul class="top-links">
                    <li><a href="https://www.twitter.com/@Omar_Andres_Bar" target="_blank" class="twitter"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="#" data-toggle="tooltip" title="contacto@omarbarbosa.com" class="vimeo"><i class="fa fa-envelope"></i></a></li>
                </ul>
            </div>
            <!-- //social icons -->
            
            <div class="login-agileits-bottom1"> 
                <h3><a class="unstyle-link" href="https://www.omarbarbosa.com">www.omarbarbosa.com</a></h3>
            </div>

            <!-- copyright -->
            <div class="copyright">
            <p> © {{ date("Y") }} {{ config('app.name', 'Welkome') }}. @lang('login.license') | @lang('login.design') <a href="http://w3layouts.com/" target="_blank">@lang('login.designer')</a></p>
            </div>
            <!-- //copyright --> 
        </div>	
        <!-- //main -->
    </div>
    
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
</body>

</html>
