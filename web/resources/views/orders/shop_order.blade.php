@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div id="new-order" class="section row">
        <div class="col-md-10 col-md-offset-1">
            
            <h3 class="center">
                <span class="order-number">{{ trans('lang.order_number') }} {{ $proposal->order->id }}</span>
                <span class="order-number">{{ $proposal->order->created_at->format('d.m.Y') }}</span>
                {{ $proposal->order->created_at->format('H:i') }}
            </h3>

            <hr>

            <div class="customer-details">
                מאת:
                <span class="customer-address" title="{{ $proposal->order->city }}, {{ $proposal->order->street }} {{ $proposal->order->building }} @if ($proposal->order->entrance) ,כניסה {{ $proposal->order->entrance }} @endif @if ($proposal->order->apartment) ,דירה {{ $proposal->order->apartment }} @endif">{{ $proposal->order->city }}, {{ $proposal->order->street }} <span>{{ $proposal->order->building }}</span><!--
                                -->@if ($proposal->order->entrance) ,כניסה <span>{{ $proposal->order->entrance }}</span> @endif<!--
                                -->@if ($proposal->order->apartment) ,דירה <span>{{ $proposal->order->apartment }}</span> @endif</span>
                <span class="customer-phone">{{ $proposal->order->customer->phone }}</span>
                <span class="customer-name">{{ $proposal->order->customer->first_name }} {{ $proposal->order->customer->last_name }}</span>
            </div>

            <div class="goods-list">
                @foreach ($proposal->order->goods as $good)
                <div class="goods-item">
                    <span class="goods-item-name">{{ $good->name }}</span>
                    <!--<span class="goods-item-quantity">{{ $good->quantity }}</span>-->
                </div>
                @endforeach
            </div>

            <div class="customer-notes">
                <span>!</span>
                הערות מהלקוח:
                {{ $proposal->order->customer_notes }}
            </div>

            <div id="delivery-map" class="map-canvas"></div>

            <div class="order-status">
                @if ($proposal->order->status == 1 || ($proposal->order->status == 2 && $proposal->status == 1))
                <form class="col col-md-9 proposal-form" action="{{ url('/proposals/propose') }}" method="POST">
                    {!! csrf_field() !!}
                    @if ($errors->has('delivery_time'))
                    <input type="number" name="delivery_time" class="col col-md-5 has-error" placeholder="הקלד זמן משלוח... (דקות)" />
                    <span class="help-block">
                        <strong>{{ $errors->first('delivery_time') }}</strong>
                    </span>
                    @else
                    <input type="number" name="delivery_time" class="col col-md-5" placeholder="הקלד זמן משלוח... (דקות)" />
                    @endif
                    <input type="hidden" name="proposal" value="{{ $proposal->id }}" />
                    <textarea name="shop_notes" class="col col-md-5" placeholder="הקלד הערות..."></textarea>
                    <button type="submit" class="btn btn-warning col col-md-5">שלחו</button>
                </form>
                <div class="col col-md-3 center">
                    
                </div>
                @elseif ($proposal->order->status == 2)
                <div class="col col-md-9">
                    <div class="statuses-list">
                        סטטוס ההזמנה:
                        <div class="statuses-item">{{ date('H:i', strtotime($proposal->proposed_at)) }} - נשלחה תגובה ללקוח</div>
                        <div class="statuses-item">&nbsp;</div>
                    </div>
                    <div>
                        <button class="btn btn-warning col col-md-5 disabled">מבצע הזמנה</button>
                        <input type="text" name="price" class="col col-md-5 disabled" readonly value="הקלד סכום..." />
                        <input type="text" name="delivery_price" class="col col-md-5 disabled" readonly value="Delivery price..." />
                    </div>
                </div>
                <div class="col col-md-3 center">
                    <span class="status-stamp">
                        ממתין<br>לאישור לקוח
                    </span>
                </div>
                @elseif ($proposal->order->status >= 3)
                <div class="col col-md-9">
                    <div class="statuses-list">
                        סטטוס ההזמנה:
                        <div class="statuses-item">{{ date('H:i', strtotime($proposal->proposed_at)) }} - נשלחה תגובה ללקוח</div>
                        <div class="statuses-item">{{ date('H:i', strtotime($proposal->accepted_at)) }} - ההזמנה אושרה</div>
                    </div>
                    @if ($proposal->order->status == 3)
                    <form action="{{ url('orders/'.$proposal->order->id.'/close') }}" method="POST">
                        {!! csrf_field() !!}
                        <button type="submit" class="btn btn-warning col col-md-5">מבצע הזמנה</button>
                        <input type="text" name="price" class="col col-md-5{{ $errors->has('price') ? ' has-error' : '' }}" placeholder="הקלד סכום..." />
                        <input type="text" name="delivery_price" class="col col-md-5{{ $errors->has('delivery_price') ? ' has-error' : '' }}" placeholder="Delivery price..." />
                        @if ($errors->has('price'))
                            <span class="help-block">
                                <strong>{{ $errors->first('price') }}</strong>
                            </span>
                        @endif
                    </form>
                    @else
                    <div class="status-price">@if ($proposal->price) {{ $proposal->price }} @else &minus; @endif &#8362; - סכום</div>
                    <div class="status-price">@if ($proposal->delivery_price) {{ $proposal->delivery_price }} @else &minus; @endif &#8362; - מחיר המשלוח</div>
                    <div class="status-price-total">{{ $proposal->price + $proposal->delivery_price }} &#8362; - מחיר כולל</div>
                    @endif
                </div>
                <div class="col col-md-3 center">
                    <span class="status-stamp stamp-warning">
                        ההזמנה<br>אושרה {{ date('H:i', strtotime($proposal->accepted_at)) }}
                    </span>
                    <div class="stamp-subtext">משלוח עד {{ date('H:i', strtotime($proposal->delivery_time)) }}</div>
                </div>
                @endif
            </div>

        </div>

    </div>

</div>

<script src="https://maps.googleapis.com/maps/api/js"></script>
<script>

    var center = new google.maps.LatLng({{ $proposal->order->latitude }}, {{ $proposal->order->longitude }});
    var directionsDisplay;
    var directionsService = new google.maps.DirectionsService();
    var map;

    var icons = {
        start: new google.maps.MarkerImage(
            // URL
            "{{ url('/img/cart-icon-red.png') }}",
            // (width,height)
            new google.maps.Size( 24, 24 ),
            // The origin point (x,y)
            new google.maps.Point( 0, 0 ),
            // The anchor point (x,y)
            new google.maps.Point( 12, 24 )
        ),
        end: new google.maps.MarkerImage(
            // URL
            "{{ url('/img/location-icon-blue.png') }}",
            // (width,height)
            new google.maps.Size( 24, 24 ),
            // The origin point (x,y)
            new google.maps.Point( 0, 0 ),
            // The anchor point (x,y)
            new google.maps.Point( 12, 24 )
        )
    };

    function initialize() {

        directionsDisplay = new google.maps.DirectionsRenderer();
        var mapCanvas = document.getElementById('delivery-map');

        var mapOptions = {
          zoom: 14,
          minZoom: 3,
          scrollwheel: false,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        map = new google.maps.Map(mapCanvas, mapOptions);
        directionsDisplay.setMap(map);
        directionsDisplay.setOptions({
            suppressMarkers: true
        });
        Route();

    }

    function Route() {

        var start = new google.maps.LatLng({{ Auth::user()->shop->latitude }}, {{ Auth::user()->shop->longitude }});
        var end = new google.maps.LatLng({{ $proposal->order->latitude }}, {{ $proposal->order->longitude }});

        var request = {
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode.WALKING
        };

        directionsService.route(request, function(result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);
                var leg = result.routes[ 0 ].legs[ 0 ];
                makeMarker( leg.start_location, icons.start, "{{ Auth::user()->shop->name }}" );
                makeMarker( leg.end_location, icons.end, "{{ $proposal->order->customer->first_name }} {{ $proposal->order->customer->last_name }}" );
            }
        });

    }

    function makeMarker( position, icon, title ) {
        new google.maps.Marker({
            position: position,
            map: map,
            icon: icon,
            title: title
        });
    }

    google.maps.event.addDomListener(window, 'load', initialize);

</script>
@endsection
