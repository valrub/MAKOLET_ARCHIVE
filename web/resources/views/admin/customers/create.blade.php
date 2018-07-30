@extends('layouts.admin')

@section('content')
<div class="container spark-screen">
    <div id="profile-form" class="section row register">
        <div class="col-md-10 col-md-offset-1">
            
            <h3><a href="{{ route('admin.customers.index') }}" class="btn btn-default btn-sm pull-left">Back <i class="fa fa-chevron-left" aria-hidden="true"></i></a>{{ trans('lang.register') }}</h3>

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

            <form class="form-horizontal col-md-8 col-md-offset-2" method="POST" action="{{ route('admin.customers.store') }}">

                {!! csrf_field() !!}

                <fieldset>
                    
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

                    <div class="col col-md-8">
                        <label for="">{{ trans('lang.password') }}</label>
                        <input type="text" name="password" />
                    </div>

                    <div class="col col-md-4">
                        <a class="btn btn-default" style="display: block; padding-left: 10px; padding-right: 10px; border-width: 1px;" onclick="generatePassword()"><i class="fa fa-key" aria-hidden="true"></i> Generate password</a>
                    </div>

                </fieldset>

                <hr>

                <fieldset>
                    
                    <legend><b>{{ trans('lang.address') }}</b>:</legend>

                    <div class="col col-md-12">
                        <label>{{ trans('lang.city') }}</label>
                        <input type="text" name="city" value="{{ old('city') }}" />
                    </div>

                    <div class="col col-md-12">
                        <label>{{ trans('lang.street') }}</label>
                        <input type="text" name="street" value="{{ old('street') }}" />
                    </div>

                    <div class="col col-md-4">
                        <label>{{ trans('lang.building') }}</label>
                        <input type="text" name="building" value="{{ old('building') }}" />
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

                <fieldset>
                    
                    <legend><b>פרטי כרטיס אשראי</b>:</legend>

                    <div class="col col-md-12">
                        <label>סוג&nbsp;הכרטיס</label>
                        <select class="full-width">
                            <option disabled selected>בחר סוג הכרטיס</option>
                        </select>
                    </div>

                    <div class="col col-md-12">
                        <label>מספר&nbsp;כרטיס</label>
                        <input type="text" name="" />
                    </div>

                    <div class="col col-md-12">
                        <label>תוקף</label>
                        <select class="half-width">
                            <option disabled selected>שנה</option>
                        </select>
                        <span class="spacer"></span>
                        <select class="half-width">
                            <option disabled selected>חודש</option>
                        </select>
                    </div>

                    <div class="col col-md-12">
                        <label>קוד&nbsp;הבטחה</label>
                        <input type="text" name="" class="half-width" />
                        <a class="btn-round btn-secondary">?</a>
                    </div>

                </fieldset>

                <hr>

                <fieldset class="col-md-6">
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

    function initialize() {

        // Ask the customer for geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            console.log("Geolocation is not supported by this browser.");
        }

        function showPosition(position) {

            // Select the map container
            var mapCanvas = document.getElementById('map-canvas');
            var mapContainer = document.getElementById('map-container');
            var latitudeField = document.getElementById('latitude-field');
            var longitudeField = document.getElementById('longitude-field');

            // Map options
            var mapOptions = {
                center: new google.maps.LatLng(42.695189, 23.319827),
                zoom: 16,
                scrollwheel: false,
                maxZoom: 18,
                minZoom: 12,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }

            mapContainer.className = mapContainer.className.replace("hidden", "");

            // Create map object
            var map = new google.maps.Map(mapCanvas, mapOptions);

            // Customer marker
            var customer = new google.maps.Marker({
                map: map,
                draggable: true,
                position: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
                icon: "{{ url('img/location-icon-blue.png') }}", // null = default icon
                title: "You are here",
                animation: google.maps.Animation.DROP
            });

            // Center the map once we have the customer's coordinates
            map.setCenter(customer.getPosition());

            latitudeField.setAttribute('value', parseFloat(customer.getPosition().lat()).toFixed(6));
            longitudeField.setAttribute('value', parseFloat(customer.getPosition().lng()).toFixed(6));

            // Listen for change in the marker's location
            customer.addListener('mouseup', function() {
                latitudeField.setAttribute('value', parseFloat(customer.getPosition().lat()).toFixed(6));
                longitudeField.setAttribute('value', parseFloat(customer.getPosition().lng()).toFixed(6));
            });

            // Listen for click on the store's marker
            customer.addListener('click', function() {
                var infowindow = new google.maps.InfoWindow({
                    content: "You are not here?<br>Move the marker to select your place."
                });
                map.setCenter(customer.getPosition());
                infowindow.open(map, customer);
            });

        }

    }
    
    // Initialize the map
    google.maps.event.addDomListener(window, 'load', initialize);

</script>
@endsection
