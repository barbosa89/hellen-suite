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

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/welkome.css') }}" rel="stylesheet">
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
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Overview</li>
                            </ol>

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
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/welkome.js') }}"></script>
</body>
</html>
