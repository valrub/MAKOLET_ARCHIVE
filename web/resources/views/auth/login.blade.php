@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div id="login" class="section row">
        <div class="col-md-10 col-md-offset-1">

            <h3>{{ trans('lang.login') }}</h3>

            <hr>

            <div class="col col-md-6 col-customer">

                <h4>כניסה למכולת</h4>
                
                <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                
                    {!! csrf_field() !!}

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label class="col-md-3 control-label">{{ trans('lang.email') }}:</label>

                        <div class="col-md-7">
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}">

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label class="col-md-3 control-label">{{ trans('lang.password') }}:</label>

                        <div class="col-md-7">
                            <input type="password" class="form-control" name="password">

                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-7 col-md-offset-3">
                            <button type="submit" class="btn btn-secondary btn-login">{{ trans('lang.login') }}</button>                          
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-7 col-md-offset-3">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember"> {{ trans('lang.remember_me') }}
                                </label>
                                <a class="btn btn-link" href="{{ url('/password/reset') }}">{{ trans('lang.forgot_your_password') }}</a>
                            </div>
                        </div>
                    </div>

                </form>

            </div>

            <div class="col col-md-6 col-shop center">
                
                <h4>אין לך עדיין כרטסת במכולת? זה ייקח 2 דקות</h4>

                <a href="{{ url('register') }}" class="btn btn-success col-md-6 col-md-offset-3">אני קונה במכולת</a>

                <button class="btn btn-warning col-md-6 col-md-offset-3 collapsed" data-toggle="collapse" data-target="#shop-code">אני בעל עסק</button>

                <div id="shop-code" class="collapse col-md-6 col-md-offset-3 center">
                    <input type="text" placeholder="הקלד קוד" />
                    אין לך קוד? פנה למספר:
                    <span class="phone-number">050-7422797</span>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
