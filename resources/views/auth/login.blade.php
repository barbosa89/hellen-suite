@extends('layouts.public')

@section('content')
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
@endsection
