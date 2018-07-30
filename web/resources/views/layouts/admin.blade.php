<!DOCTYPE html>
<html lang="{{ trans('lang.lang') }}" dir="{{ trans('lang.dir') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Makolet</title>

    <script src="{{ url('/js/jquery.min.js') }}"></script>

    <!-- Fonts -->
    <link href="{{ url('/css/font-awesome.min.css') }}" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- LTR Styles -->
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
    <link href="{{ url('/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('/css/main.css') }}" rel="stylesheet">

    <!-- RTL Styles -->
    @if (trans('lang.dir') === 'rtl')
    <link href="{{ url('/css/bootstrap-rtl.css') }}" rel="stylesheet">
    <link href="{{ url('/css/main-rtl.css') }}" rel="stylesheet">
    @endif
</head>
<body id="admin-layout" class="app-admin">
    <nav class="navbar navbar-default">
        <div class="adv-header hidden">
            רוצים להזמין במכולת על חשבונינו?
            <button type="button" class="btn btn-primary btn-lg adv-button" data-toggle="modal" data-target="#adv-modal">
                גלו שזה פשוט!
            </button>
            <button class="adv-close pull-left"></button>
        </div>
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#spark-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ url('/img/logo-small.png') }}" />
                </a>
            </div>

            <div class="collapse navbar-collapse" id="spark-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <li><a href="{{ url('/admin/customers') }}">Customers</a></li>
                    <li><a href="{{ url('/admin/shops') }}">Shops</a></li>
                    <li><a href="{{ url('/admin/orders') }}">Orders</a></li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="{{ url('/logout') }}" class="btn btn-transparent">
                            {{ trans('lang.logout') }}
                        </a>
                    </li>
                </ul>

            </div>
        </div>
    </nav>

    @yield('content')

    <!-- JavaScripts -->
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
    <script src="{{ url('/js/bootstrap.min.js') }}"></script>
    <script src="{{ url('/js/main.js') }}"></script>
</body>
</html>
