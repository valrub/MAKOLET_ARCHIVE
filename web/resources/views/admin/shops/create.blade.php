@extends('layouts.admin')

@section('content')
<div class="container spark-screen">
    <div id="profile-form" class="section row">
        <div class="col-md-10 col-md-offset-1">
            
            <h3>
                <a href="{{ route('admin.shops.index') }}" class="btn btn-default btn-sm pull-left">Back <i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                איזור אישי
            </h3>

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

            <form class="form-horizontal col-md-8 col-md-offset-2" method="POST" action="{{ route('admin.shops.store') }}">

                {!! csrf_field() !!}

                <fieldset>
                    
                    <legend><b>פרטי העסק:</b></legend>

                    <div class="col col-md-5">
                        <label for="">{{ trans('lang.first_name') }}</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}">
                    </div>

                    <div class="col col-md-7">
                        <label for="">שם&nbsp;משפחה</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}">
                    </div>

                    <div class="col col-md-12">
                        <label for="">שם&nbsp;העסק</label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}">
                    </div>

                    <div class="col col-md-12">
                        <label for="">ח.פ&nbsp;או&nbsp;מספר&nbsp;עסק&nbsp;מורשה</label>
                        <input type="text" name="company_id" value="{{ old('company_id') }}">
                    </div>

                    <div class="col col-md-6">
                        <label for="">שם&nbsp;חנות</label>
                        <input type="text" name="name" value="{{ old('name') }}">
                    </div>

                    <div class="col col-md-6">
                        <label for="">Type</label>
                        <select name="type" class="full-width">
                            <option value="1">Shop</option>
                            <option value="2">Alte Zachen</option>
                        </select>
                    </div>

                    <div class="col col-md-12">
                        <label>{{ trans('lang.city') }}</label>
                        <input type="text" name="city" value="{{ old('city') }}">
                    </div>

                    <div class="col col-md-8">
                        <label>{{ trans('lang.street') }}</label>
                        <input type="text" name="street" value="{{ old('street') }}">
                    </div>

                    <div class="col col-md-4">
                        <label>{{ trans('lang.building') }}</label>
                        <input type="text" name="building" value="{{ old('building') }}">
                    </div>

                    <div class="col col-md-12 hidden" id="map-container">
                        <div id="map-canvas" class="map-canvas" style="width: 100%; height: 250px;"></div>
                        <input id="latitude-field" class="hidden" type="text" name="latitude">
                        <input id="longitude-field" class="hidden" type="text" name="longitude">
                    </div>

                    <div class="col col-md-12">
                        <label>טלפון&nbsp;נייד</label>
                        <input type="text" name="phone" value="{{ old('phone') }}">
                    </div>

                    <div class="col col-md-12">
                        <label>טלפון&nbsp;בעסק</label>
                        <input type="text" name="mobile" value="{{ old('mobile') }}">
                    </div>

                    <div class="col col-md-12">
                        <label for="">{{ trans('lang.email') }}</label>
                        <input type="text" name="email" value="{{ old('email') }}">
                    </div>

                    <div class="col col-md-8">
                        <label for="">{{ trans('lang.password') }}</label>
                        <input type="text" name="password" />
                    </div>                    

                    <div class="col col-md-4">
                        <a class="btn btn-default" style="display: block; padding-left: 10px; padding-right: 10px; border-width: 1px;" onclick="generatePassword()"><i class="fa fa-key" aria-hidden="true"></i> Generate password</a>
                    </div>
                    
                    <legend><b>פרטי החשבון:</b></legend>

                    <div class="col col-md-12">
                        <label for="">מספר&nbsp;חשבון</label>
                        <input name="bank_account_number" type="text" value="{{ old('bank_account_number') }}">
                    </div>

                    <div class="col col-md-6">
                        <label for="">בנק</label>
                        <input name="bank_name" type="text" value="{{ old('bank_name') }}">
                    </div>

                    <div class="col col-md-6">
                        <label for="">סניף</label>
                        <input name="bank_branch" type="text" value="{{ old('bank_branch') }}">
                    </div>

                    <div class="col col-md-12">
                        <button type="submit" class="btn btn-lg btn-primary">עדכן פרטים</button>
                    </div>

                </fieldset>

            </form>

        </div>
    </div>

</div>
<script src="https://maps.googleapis.com/maps/api/js"></script>
<script>

    function initialize() {

        // Ask the user for geolocation
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
                maxZoom: 18,
                scrollwheel: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }

            mapContainer.className = mapContainer.className.replace("hidden", "");

            // Create map object
            var map = new google.maps.Map(mapCanvas, mapOptions);

            // User marker
            var user = new google.maps.Marker({
                map: map,
                draggable: true,
                position: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
                icon: "{{ url('img/cart-icon-red.png') }}", // null = default icon
                animation: google.maps.Animation.DROP
            });

            // Center the map once we have the user's coordinates
            map.setCenter(user.getPosition());

            latitudeField.setAttribute('value', parseFloat(user.getPosition().lat()).toFixed(6));
            longitudeField.setAttribute('value', parseFloat(user.getPosition().lng()).toFixed(6));

            // Listen for change in the marker's location
            user.addListener('mouseup', function() {
                latitudeField.setAttribute('value', parseFloat(user.getPosition().lat()).toFixed(6));
                longitudeField.setAttribute('value', parseFloat(user.getPosition().lng()).toFixed(6));
            });

            // Listen for click on the store's marker
            user.addListener('click', function() {
                var infowindow = new google.maps.InfoWindow({
                    content: "Your shop is not here? Click and move the marker to the right possition!"
                });
                map.setCenter(user.getPosition());
                infowindow.open(map, user);
            });

        }

    }
    
    // Initialize the map
    google.maps.event.addDomListener(window, 'load', initialize);

</script>
@endsection
