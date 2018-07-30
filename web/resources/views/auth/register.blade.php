@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div id="profile-form" class="section row register">
        <div class="col-md-6 col-md-offset-3">
            
            <h3>{{ trans('lang.register') }}</h3>

            <hr>

            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">

                {!! csrf_field() !!}

                <fieldset class="col-md-12">
                    
                    <legend><b>פרטים אישיים</b>:</legend>

                    <div class="col col-md-5">
                        <label for="">{{ trans('lang.first_name') }}</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" />
                    </div>

                    <div class="col col-md-7">
                        <label for="">שם&nbsp;משפחה</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" />
                    </div>

                    <div class="col col-md-12">
                        <label for="">מספר&nbsp;טלפון</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" />
                    </div>

                    <div class="col col-md-12">
                        <label for="">{{ trans('lang.email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" />
                    </div>

                    <div class="col col-md-12">
                        <label for="">{{ trans('lang.password') }}</label>
                        <input type="password" name="password" />
                    </div>

                </fieldset>

                <hr>

                <fieldset class="col-md-12">
                    
                    <legend><b>{{ trans('lang.address') }}</b> (ניתן להחליפה בביצוע הזמנה):</legend>

                    <div class="col col-md-12">
                        <label>{{ trans('lang.city') }}</label>
                        <input type="text" name="city" id="city-field" value="{{ old('city') }}" />
                    </div>

                    <div class="col col-md-12">
                        <label>{{ trans('lang.street') }}</label>
                        <input type="text" name="street" id="street-field" value="{{ old('street') }}" />
                    </div>

                    <div class="col col-md-4">
                        <label>{{ trans('lang.building') }}</label>
                        <input type="text" name="building" id="building-field" value="{{ old('building') }}" />
                    </div>

                    <div class="col col-md-4">
                        <label>{{ trans('lang.entrance') }}</label>
                        <input type="text" name="entrance" value="{{ old('entrance') }}" />
                    </div>

                    <div class="col col-md-4">
                        <label>{{ trans('lang.apartment') }}</label>
                        <input type="text" name="apartment" value="{{ old('apartment') }}" />
                    </div>

                    <div class="col col-md-12 hidden" id="map-container">
                        <div id="map-canvas" class="map-canvas" style="width: 100%; height: 250px;"></div>
                        <input id="latitude-field" class="hidden" type="text" name="latitude" value="" hidden>
                        <input id="longitude-field" class="hidden" type="text" name="longitude" value="" hidden>
                    </div>

                </fieldset>

                <hr>

                <fieldset class="col-md-12">
                    <div class="col col-md-12 agree-terms">
                        <input id="terms-checkbox" name="terms" type="checkbox" />
                        <label for="terms-checkbox">קראתי ומסכים עם <a href="{{ url('/terms-of-use') }}" target="_blank">תנאי שימוש</a></label> 
                    </div>
                    <div class="col col-md-12">
                        <button type="submit" class="btn btn-success">שמור</button>
                    </div>
                </fieldset>

            </form>

        </div>
    </div>

</div>
<script src="https://maps.googleapis.com/maps/api/js"></script>
<script>

    var userLatitude, userLongitude;

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            userLatitude = parseFloat(position.coords.latitude).toFixed(6);
            userLongitude = parseFloat(position.coords.longitude).toFixed(6);
        });
    }

    // Specify the user type
    var userType = 'customer';

</script>

<script src="{{ url('js/map.js') }}"></script>

@endsection
