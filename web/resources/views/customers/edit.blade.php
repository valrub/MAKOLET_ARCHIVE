@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div id="profile-form" class="section row">
        <div class="col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
            
            <h3>איזור אישי</h3>

            <hr>

            <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))
                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                @endif
                @endforeach
            </div>

            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form class="form-horizontal col-md-12" role="form" method="POST" action="{{ url('/profile') }}">

                {!! csrf_field() !!}

                <fieldset>
                    
                    <legend><b>פרטים אישיים</b>:</legend>

                    <div class="col col-md-5 col-xs-12">
                        <label for="">{{ trans('lang.first_name') }}</label>
                        <input type="text" name="first_name" value="{{ $customer->first_name }}" />
                    </div>

                    <div class="col col-md-7 col-xs-12">
                        <label for="">שם&nbsp;משפחה</label>
                        <input type="text" name="last_name" value="{{ $customer->last_name }}" />
                    </div>

                    <div class="col col-xs-12">
                        <label for="">מספר&nbsp;טלפון</label>
                        <input type="text" name="phone" value="{{ $customer->phone }}" />
                    </div>

                    <div class="col col-xs-12">
                        <label for="">{{ trans('lang.email') }}</label>
                        <input type="text" name="email" value="{{ $customer->user->email }}" readonly />
                    </div>

                    <div class="col col-xs-12">
                        <label for="">{{ trans('lang.password') }}</label>
                        <input type="password" name="password" />
                    </div>

                    <div class="col col-xs-12">
                        <button type="submit" class="btn btn-lg btn-primary">עדכן פרטים אישיים</button>
                    </div>

                </fieldset>

                <fieldset>
                    
                    <legend><b>{{ trans('lang.address') }}</b> (ניתן להחליפה בביצוע הזמנה):</legend>

                    <div class="col col-xs-12">
                        <label>{{ trans('lang.city') }}</label>
                        <input type="text" name="city" id="city-field" value="{{ $customer->city }}" />
                    </div>

                    <div class="col col-xs-12">
                        <label>{{ trans('lang.street') }}</label>
                        <input type="text" name="street" id="street-field" value="{{ $customer->street }}" />
                    </div>

                    <div class="col col-md-4 col-xs-12">
                        <label>{{ trans('lang.building') }}</label>
                        <input type="text" name="building" id="building-field" value="{{ $customer->building }}" />
                    </div>

                    <div class="col col-md-4 col-xs-12">
                        <label>{{ trans('lang.entrance') }}</label>
                        <input type="text" name="entrance" value="{{ $customer->entrance }}" />
                    </div>

                    <div class="col col-md-4 col-xs-12">
                        <label>{{ trans('lang.apartment') }}</label>
                        <input type="text" name="apartment" value="{{ $customer->apartment }}" />
                    </div>

                    <div class="col col-xs-12 hidden" id="map-container">
                        <div id="map-canvas" class="map-canvas" style="width: 100%; height: 250px;"></div>
                        <input id="latitude-field" class="hidden" type="text" name="latitude" value="{{ $customer->latitude }}" hidden>
                        <input id="longitude-field" class="hidden" type="text" name="longitude" value="{{ $customer->longitude }}" hidden>
                    </div>

                    <div class="col col-xs-12">
                        <button type="submit" class="btn btn-lg btn-primary">עדכן כתובת למשלוח</button>
                    </div>

                </fieldset>

                <fieldset>

                    <legend><b>פרטי כרטיס אשראי</b>:</legend>

                    <div id="tranzila-box" class="col col-xs-12">
                        @if ($customer->expmonth && $customer->expyear && $customer->tranzilatk)
                        <label style="width: 100%; display: block; margin-bottom: 20px;">כרטיס האשראי שלך רשום.</label>
                        <button class="btn btn-lg btn-primary" data-customer-id="{{ $customer->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;לשנות פרטי כרטיס אשראי</button>
                        @else
                        <iframe id="iframe" width="100%" height="240" scrolling="no" frameborder="0" src="https://direct.tranzila.com/amsn2001/iframe.php?currency=1&buttonLabel=שמירת פרטי אשראי&lang=il&tranmode=VK&nologo=1&trButtonColor=00AEEF&trTextColor=444444&customer={{ $customer->id }}&sum=1&hidesum=1"></iframe>
                        @endif
                    </div>

                </fieldset>

            </form>

        </div>
    </div>

</div>
<script src="{{ url('js/tranzila.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js"></script>
<script>

    // Get the user location
    var userLatitude = "{{ $customer->latitude }}";
    var userLongitude = "{{ $customer->longitude }}";

    // Specify the user type
    var userType = 'customer';

</script>
<script src="{{ url('js/map.js') }}"></script>
@endsection
