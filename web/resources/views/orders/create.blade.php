@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div id="new-order" class="section row">
        <div class="col-md-10 col-md-offset-1">
            
            <h3 class="center"><?php echo date('d.m.Y'); ?></h3>

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

            <ul class="nav nav-tabs">
                <li class="active completed"><a href="#goods" data-toggle="tab" aria-expanded="true">{{ trans('lang.goods_list') }}</a></li>
                <li><a href="#shops" data-toggle="tab" aria-expanded="false">{{ trans('lang.address_and_shops') }}</a></li>
                <li class="inactive"><a aria-expanded="false">{{ trans('lang.proposals') }}</a></li>
                <li class="inactive"><a aria-expanded="false">{{ trans('lang.order_status') }}</a></li>
            </ul>

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/orders') }}">

                {!! csrf_field() !!}

                <div class="tab-content">

                    <div class="tab-pane fade active in" id="goods">

                        <table>
                            <thead>
                                <tr>
                                    <th>מוצר</th>
                                    <!--<th class="center">כמות</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" name="goods[]" placeholder="הקלד מוצר" value="{{ old('goods.0') }}" /></td>
                                    <!--<td><input type="number" name="quantities[]" min="1" max="99" value="{{ old('quantities.0', 1) }}" /></td>-->
                                </tr>
                                <tr>
                                    <td><input type="text" name="goods[]" value="{{ old('goods.1') }}" /></td>
                                    <!--<td><input type="number" name="quantities[]" min="1" max="99" value="{{ old('quantities.1', 1) }}" /></td>-->
                                </tr>
                                <tr>
                                    <td><input type="text" name="goods[]" value="{{ old('goods.2') }}" /></td>
                                    <!--<td><input type="number" name="quantities[]" min="1" max="99" value="{{ old('quantities.2', 1) }}" /></td>-->
                                </tr>
                                <tr>
                                    <td><input type="text" name="goods[]" value="{{ old('goods.3') }}" /></td>
                                    <!--<td><input type="number" name="quantities[]" min="1" max="99" value="{{ old('quantities.3', 1) }}" /></td>-->
                                </tr>
                                <tr>
                                    <td><input type="text" name="goods[]" value="{{ old('goods.4') }}" /></td>
                                    <!--<td><input type="number" name="quantities[]" min="1" max="99" value="{{ old('quantities.4', 1) }}" /></td>-->
                                </tr>
                                <tr>
                                    <td><input type="text" name="goods[]" value="{{ old('goods.5') }}" /></td>
                                    <!--<td><input type="number" name="quantities[]" min="1" max="99" value="{{ old('quantities.5', 1) }}" /></td>-->
                                </tr>
                            </tbody>
                        </table>

                        <div>
                            <button type="button" class="btn btn-primary add-line"><i class="fa fa-plus" aria-hidden="true"></i></button><!--
                            --><button type="button" class="btn btn-primary remove-line"><i class="fa fa-minus" aria-hidden="true"></i></button>
                        </div>

                        <textarea name="notes" placeholder="הערות:">{{ old('notes') }}</textarea>

                        <a href="#shops" data-toggle="tab" aria-expanded="false" class="btn col-md-4 col-md-offset-4">בחר חנות</a>

                    </div>

                    <div class="tab-pane fade" id="shops">

                        <div class="row">
                            <div class="col col-md-7 delivery-address">
                                <label>כתובת משלוח:</label>
                                <i class="fa fa-map-marker"></i>
                                <span title="{{ $customer->city }}, {{ $customer->street }} {{ $customer->building }} @if ($customer->entrance) ,כניסה {{ $customer->entrance }} @endif @if ($customer->apartment) ,דירה {{ $customer->apartment }} @endif">{{ old('city', $customer->city) }}, {{ old('street', $customer->street) }} <span>{{ old('building', $customer->building) }}</span><!--
                                -->@if ($customer->entrance) ,כניסה <span>{{ old('entrance', $customer->entrance) }}</span> @endif<!--
                                -->@if ($customer->apartment) ,דירה <span>{{ old('apartment', $customer->apartment) }}</span> @endif</span>
                            </div>
                            <div class="col col-md-3 change-address">
                                <a href="#" data-toggle="modal" data-target="#address-modal">לחץ לשינוי כתובת משלוח</a>
                            </div>
                            <div class="col col-md-2 update-map right">
                                <a class="btn btn-primary">עדכן מפה</a>
                            </div>
                        </div>

                        <div id="address-modal" class="modal address-modal fade" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title">כתובת משלוח</h4>
                                    </div>

                                    <div class="modal-body row">

                                        <div class="col col-xs-12">
                                            <label>{{ trans('lang.city') }}</label>
                                            <input type="text" name="city" id="city-field" value="{{ old('city', $customer->city) }}" />
                                        </div>

                                        <div class="col col-xs-12">
                                            <label>{{ trans('lang.street') }}</label>
                                            <input type="text" name="street" id="street-field" value="{{ old('street', $customer->street) }}" />
                                        </div>

                                        <div class="col col-xs-4">
                                            <label>{{ trans('lang.building') }}</label>
                                            <input type="text" name="building" id="building-field" value="{{ old('building', $customer->building) }}" />
                                        </div>

                                        <div class="col col-xs-4">
                                            <label>{{ trans('lang.entrance') }}</label>
                                            <input type="text" name="entrance" value="{{ old('entrance', $customer->entrance) }}" />
                                        </div>

                                        <div class="col col-xs-4">
                                            <label>{{ trans('lang.apartment') }}</label>
                                            <input type="text" name="apartment" value="{{ old('apartment', $customer->apartment) }}" />
                                        </div>

                                        <div class="col col-xs-12 hidden" id="map-container">
                                            <div id="map-canvas" class="map-canvas"></div>
                                            <small>* Move the <img src="{{ url('img/location-icon-blue.png') }}"> marker to the exact delivery address or <a href="#" id="detect-location">use your current location</a></small>
                                            <input id="latitude-field" class="hidden" type="text" name="latitude" value="{{ old('latitude', $customer->latitude) }}" hidden>
                                            <input id="longitude-field" class="hidden" type="text" name="longitude" value="{{ old('longitude', $customer->longitude) }}" hidden>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                        <div id="shops-map">
                            
                        </div>

                        <div class="row">
                            <div class="col col-md-12">
                                <label>בחר חנות:</label>
                            </div>
                        </div>

                        @forelse ($shops as $shop)
                        <div class="row shops-list" data-shop-id="{{ $shop->id }}">
                            <div class="col col-md-5 shop-name">
                                <input type="checkbox" name="shops[]" value="{{ $shop->id }}" id="shop-{{ $shop->id }}" checked />
                                <label for="shop-{{ $shop->id }}"></label>
                                {{ $shop->city }}, {{ $shop->street }} {{ $shop->building }}, 
                                <b>{{ $shop->name }}</b>
                            </div>
                            <div class="col col-md-2 shop-stars">
                                @if ($shop->feedbacks->avg('score') > 0) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                                @if ($shop->feedbacks->avg('score') > 1) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                                @if ($shop->feedbacks->avg('score') > 2) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                                @if ($shop->feedbacks->avg('score') > 3) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                                @if ($shop->feedbacks->avg('score') > 4) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                            </div>
                            <div class="col col-md-2 shop-reviews right">
                                יש {{ count($shop->feedbacks) }} חוות דעת
                            </div>
                            <div class="col col-md-3 right">
                                <a href="{{ route('shops.show', $shop) }}" target="_blank" class="btn btn-secondary">פרטים</a>
                            </div>
                        </div>
                        @empty
                        <p class="proposals-counter center">No shops were found</p>
                        @endforelse

                        <p class="proposals-counter center empty-aria" style="display:none;">No shops were found in this area</p>

                        <button type="submit" class="btn col-md-4 col-md-offset-4">שלח רשימת מכולת</button>

                    </div>

                </div>

            </form>

        </div>
    </div>

</div>

<script src="https://maps.googleapis.com/maps/api/js"></script>

<script>

    // Get the user location
    var userLatitude = "{{ old('latitude', $customer->latitude) }}";
    var userLongitude = "{{ old('longitude', $customer->longitude) }}";

    if (!userLatitude || !userLongitude) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                userLatitude = parseFloat(position.coords.latitude).toFixed(6);
                userLongitude = parseFloat(position.coords.longitude).toFixed(6);
            });
        } else {
            alert("You don't have location specified.");
        }
    }

    // Specify the user type
    var userType = 'customer';

    var shopsMap;
    var shopMarkers = [];

    function initializeShopsMap() {

        var shopsMapCanvas = document.getElementById('shops-map');
        var shopsMapOptions = {
          center: new google.maps.LatLng(userLatitude, userLongitude),
          zoom: 14,
          minZoom: 3,
          scrollwheel: false,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        shopsMap = new google.maps.Map(shopsMapCanvas, shopsMapOptions);

        var shopsCustomer = new google.maps.Marker({
            map: shopsMap,
            position: new google.maps.LatLng(userLatitude, userLongitude),
            icon: '../img/location-icon-blue.png',
            title: 'Your delivery address'
        });

        @foreach ($shops as $shop)
        shopMarkers[{{ $shop->id }}] = new google.maps.Marker({
            map: shopsMap,
            position: new google.maps.LatLng({{ $shop->latitude }}, {{ $shop->longitude }}),
            icon: @if ($shop->type == 1) '../img/cart-icon-red.png' @else '../img/donkey-icon.png' @endif,
            title: "{{ $shop->name }}"
        });
        @endforeach

        $('a[href="#shops"]').click(function() {
            setTimeout(function() {
                google.maps.event.trigger(shopsMap, 'resize');
                shopsMap.setCenter(new google.maps.LatLng($('#latitude-field').attr('value'), $('#longitude-field').attr('value')));
                updateShopsList(500);
            }, 300);
        });

        $('#address-modal').on('hidden.bs.modal', function () {
            var deliveryAddress = $('input[name=city]').val() + ', ' + $('input[name=street]').val() + ' ' + $('input[name=building]').val();
            $('.delivery-address span').html(deliveryAddress);
            var lat = $('#latitude-field').attr('value');
            var lon = $('#longitude-field').attr('value');
            var latlng = new google.maps.LatLng(lat, lon);
            shopsCustomer.setPosition(latlng);
            shopsMap.setCenter(latlng);
            updateShopsList();
        });

        google.maps.event.addListenerOnce(shopsMap, 'idle', function() {
            updateShopsList();
        });

        google.maps.event.addListener(shopsMap, 'dragend', function() {
            updateShopsList(1000);
        });

        google.maps.event.addListener(shopsMap, 'zoom_changed', function() {
            updateShopsList();
        });

    }

    google.maps.event.addDomListener(window, 'load', initializeShopsMap);

    $('.update-map a.btn').click(function() {
        updateShopsList();
    });

    function updateShopsList(timeout = 0) {
        setTimeout(function() {
            $('.shops-list').each(function() {
                //$(this).slideUp();
                //$(this).find('input').prop('disabled', true);
            });
            var bounds = shopsMap.getBounds();
            var displayed = false;
            for (var i = 0; i < shopMarkers.length; i++) {
                if (shopMarkers[i] && bounds.contains(shopMarkers[i].position)) {
                    $('.shops-list[data-shop-id=' + i + ']').slideDown();
                    $('.shops-list[data-shop-id=' + i + '] input').prop('disabled', false);
                    displayed = true;
                } else {
                    $('.shops-list[data-shop-id=' + i + ']').slideUp();
                    $('.shops-list[data-shop-id=' + i + '] input').prop('disabled', true);
                }
            }
            if (!displayed) {
                $('.empty-aria').show();
            } else {
                $('.empty-aria').hide();
            }
        }, timeout);
    };

</script>

<script>

    $('.change-address a').click(function() {
        setTimeout(function() {
            google.maps.event.trigger(map, 'resize');
            map.setCenter(new google.maps.LatLng($('#latitude-field').attr('value'), $('#longitude-field').attr('value')));
        }, 500);
    });

    $('#detect-location').click(function() {
        // Ask the customer for geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                latitudeField.setAttribute('value', parseFloat(position.coords.latitude).toFixed(6));
                longitudeField.setAttribute('value', parseFloat(position.coords.longitude).toFixed(6));
                customer.setPosition(latlng);
                map.setCenter(latlng);
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    });

</script>

<script src="{{ url('js/map.js') }}"></script>

@endsection
