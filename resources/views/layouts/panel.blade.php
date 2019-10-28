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
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/welkome.css') }}" rel="stylesheet">
</head>
<body id="page-top">
    <div id="app">
        @include('templates.navbar')

        <main>
            <div id="wrapper">
                    @include('templates.sidebar')

                    <div id="content-wrapper">
                        <div class="container-fluid">
                            <!-- Breadcrumbs-->
                            @yield('breadcrumbs')

                            @yield('content')
                        </div>
                        <!-- /.container-fluid -->
                        <!-- Sticky Footer -->
                        <footer class="sticky-footer">
                            <div class="container my-auto">
                                <div class="copyright text-center my-auto">
                                    <span>{{ trans('landing.copyright') }} © {{ config('app.name') }} {{ date('Y') }}</span>
                                </div>
                            </div>
                        </footer>
                    </div>
                    <!-- /.content-wrapper -->
                </div>
                <!-- /#wrapper -->

                <!-- Scroll to Top Button-->
                <a class="scroll-to-top rounded" href="#page-top">
                <i class="fa fa-angle-up"></i>
                </a>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        @auth
            window.Permissions = {!! json_encode(Auth::user()->allPermissions, true) !!};
        @else
            window.Permissions = [];
        @endauth
    </script>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ mix('js/welkome.js') }}"></script>
    @yield('scripts')
</body>
</html>
