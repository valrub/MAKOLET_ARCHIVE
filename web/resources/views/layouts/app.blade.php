<!DOCTYPE html>
<html lang="{{ trans('lang.lang') }}" dir="{{ trans('lang.dir') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Makolet</title>

    <script src="{{ url('/js/jquery.min.js') }}"></script>
    <script src="{{ url('/js/jquery-ui.min.js') }}"></script>

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
@if (Auth::check())
<body id="app-layout" class="app-user">
@else
<body id="app-layout" class="app-guest">
@endif
    <script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');ga('create','UA-86318241-1','auto');ga('send','pageview');</script>
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
                    @if (Auth::check())
                    <img src="{{ url('/img/logo-small.png') }}" />
                    @else
                    <img src="{{ url('/img/logo-medium.png') }}" />
                    @endif
                </a>
            </div>

            <div class="collapse navbar-collapse" id="spark-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    @if (Auth::guest())
                        <!-- <li><a href="{{ url('/login') }}">{{ trans('lang.login') }}</a></li> -->
                        <!-- <li><a href="{{ url('/register') }}">{{ trans('lang.register') }}</a></li> -->
                        <li><a href="{{ url('/home#how-it-works') }}">{{ trans('lang.how_it_works') }}</a></li>
                        <li><a href="{{ url('/home#stores') }}">{{ trans('lang.stores') }}</a></li>
                        <li><a href="{{ url('/home#benefits') }}">{{ trans('lang.benefits_for_groceries') }}</a></li>
                        <li><a href="{{ url('/home#questions') }}">{{ trans('lang.questions_and_answers') }}</a></li>
                        <li><a href="{{ url('/contact-us') }}">{{ trans('lang.contact_us') }}</a></li>
                        <li><a href="{{ url('/join') }}" class="btn btn-warning">{{ trans('lang.bussines_owner_join') }}</a></li>
                        <li><a href="{{ url('/login') }}" class="btn btn-transparent">{{ trans('lang.login') }}</a></li>
                    @elseif (Auth::user()->type == 1)
                        <li><a href="{{ url('/orders/create') }}">{{ trans('lang.order') }}</a></li>
                        <li><a href="{{ url('/map') }}">{{ trans('lang.map') }}</a></li>
                        <li><a href="{{ url('/profile') }}">{{ trans('lang.profile') }}</a></li>
                        <li><a href="{{ url('/orders') }}">{{ trans('lang.orders') }}</a></li>
                        <li><a href="{{ url('/home') }}">{{ trans('lang.how_it_works') }}</a></li>
                    @elseif (Auth::user()->type == 2)
                        <li><a href="{{ url('/orders/summary') }}">רכישות</a></li>
                        <li><a href="{{ url('/shops/' . Auth::user()->shop->id . '/edit') }}">{{ trans('lang.profile') }}</a></li>
                        <li><a href="{{ url('/orders') }}">ריכוז הזמנות</a></li>
                        <li><a href="{{ url('/home#how-it-works') }}">{{ trans('lang.how_it_works') }}</a></li>
                    @endif
                </ul>

                <!-- Right Side Of Navbar -->
                @if (Auth::check())
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <span class="user-name">
                        @if (Auth::user()->type == 1)
                        Hello, {{ Auth::user()->customer->first_name }}
                        @elseif (Auth::user()->type == 2)
                        Hello, {{ Auth::user()->shop->name }}
                        @endif
                        </span>
                    </li>
                    <li>
                        <a href="{{ url('/logout') }}" class="btn btn-transparent">
                            {{ trans('lang.logout') }}
                        </a>
                    </li>
                </ul>
                @endif

            </div>
        </div>
    </nav>

    @yield('content')

    <footer>
        <center>
            <a href="{{ url('/terms-of-use') }}" style="display: inline-block; margin: 10px; font-size: 13px; color: white;">{{ trans('lang.terms_of_use') }}</a>
            <a href="{{ url('/contact-us') }}" style="display: inline-block; margin: 10px; font-size: 13px; color: white;">{{ trans('lang.contact_us') }}</a>
        </center>
    </footer>

    <div id="adv-modal" class="modal adv-modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <ol>
                        <li>קבלו מאיתנו ערכה להחתמת מכולת</li>
                        <li>תסבירו איך זה עובד ותחתימו בעל העסק</li>
                        <li>תעבירו אלינו מסמכים חתומים</li>
                        <li>100 ש"ח זיכוי יופיעו בחשבונכם בישומון</li>
                    </ol>
                    <p>תחזרו על זה עוד פעם ועוד פעם, וקבלו כל פעם 100ש"ח זיכוי על כל מכולת חדשה.</p>
                    <hr>
                    <p>לקבלת ההנחיות או בכל שאלה צרו קשר:</p>
                    <form class="form-inline" role="form" method="POST" action="#">
                        <div class="form-group">
                            <label>שם:</label>
                            <input type="text" class="form-control">
                            &nbsp;&nbsp;&nbsp;
                        </div>
                        <div class="form-group">
                            <label>טלפון:</label>
                            <input type="text" class="form-control">
                            &nbsp;&nbsp;&nbsp;
                        </div>
                        <a class="btn btn-warning">שלח</a>
                    </form>
                    <hr>
                    <p>לקבלת ההנחיות או בכל שאלה צרו קשר: <span class="orange">054-761894</span></p>
                    <p>תודה ובהצלחה!</p>
                </div>
            </div>
        </div>
    </div>

    @if (Auth::check() && Auth::user()->notifications->count() > 0)
    <a href="{{ url('orders/' . Auth::user()->notifications->first()->order_id) }}" id="notifier" data-toggle="tooltip" data-placement="top" data-notification="{{ Auth::user()->notifications->first()->id }}" title="{{ Auth::user()->notifications->first()->message }}"><span class="badge">{{ Auth::user()->notifications->count() }}</span><i class="fa fa-bell-o" aria-hidden="true"></i></a>
    @endif

    <!-- JavaScripts -->
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
    <script src="{{ url('/js/bootstrap.min.js') }}"></script>
    <script src="{{ url('/js/main.js') }}"></script>
</body>
</html>
