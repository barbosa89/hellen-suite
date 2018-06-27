@extends('layouts.public')

@section('content')
    <div class="main">
        <a class="unstyle-link" href="{{ url('/') }}"><h1>{{ config('app.name', 'Welkome') }}</h1></a>
        <div class="main-w3lsrow">
            <!-- login form -->
            <div class="login-form login-form-left"> 
                <div class="agile-row">
                    <div class="head">
                        <h2>@lang('login.reset')</h2>
                        <span class="fa fa-lock"></span>
                    </div>					
                    <div class="clear"></div>
                    <div class="login-agileits-top"> 	   
                        <form action="{{ route('password.email') }}" method="post"> 
                            @csrf
                            <div class="form-group{{ $errors->has('email') ? ' is-invalid' : '' }}">
                                <input type="email" class="name" name="email" placeholder="@lang('login.email')" required="" value="{{ old("email") }}"/>
                                
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <input type="submit" value="{{ trans('login.sendLink') }}"> 
                        </form> 	
                    </div> 
                    <div class="login-agileits-bottom"> 
                        <h6><a href="{{ route('login') }}">@lang('login.identification')</a></h6>
                    </div>
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
