<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'VentSpace') }} @yield('title')</title>

    <link href="{{asset('partner_assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('partner_assets/css/style.css')}}" rel="stylesheet">


    <!-- HTML5 shim and Respond.js IE8 support of HTML5 -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
     @yield('styles', '')
</head>
<body>
    @yield('content')
</body>
    <script src="{{asset('partner_assets/js/bootstrap.bundle.min.js')}}" ></script>
    <script src="{{asset('partner_assets/js/jquery.min.js')}}" ></script>
    @yield('scripts', '')
</html>
