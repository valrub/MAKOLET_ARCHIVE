@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div id="shop-details" class="section row">
        <div class="col-md-10 col-md-offset-1">
            
            <h3>תגובות</h3>

            <hr>

            <div class="row shop-details">
                <div class="col col-md-5 shop-info">
                    <span class="shop-name">{{ $shop->name }}</span>
                    <span class="shop-address"><i class="fa fa-map-marker" aria-hidden="true"></i> {{ $shop->city }}, {{ $shop->street }} {{ $shop->building }}</span>
                    <span class="shop-address"><i class="fa fa-phone" aria-hidden="true"></i> {{ $shop->mobile }} &nbsp; <i class="fa fa-phone" aria-hidden="true"></i> {{ $shop->phone }}</span>
                    <span class="shop-stars" title="{{ $shop->rating }}">
                        @if ($shop->rating > 0) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                        @if ($shop->rating > 1) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                        @if ($shop->rating > 2) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                        @if ($shop->rating > 3) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                        @if ($shop->rating > 4) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                    </span>
                </div>
                <div class="col col-md-7 shop-map">
                    <div id="map-canvas" class="map-canvas"></div>
                </div>
            </div>

            @forelse($shop->feedbacks as $feedback)
            <div class="feedback-entry">
                <h4 class="feedback-date">{{ $feedback->created_at->format('d.m.Y') }}</h4>
                <div class="feedback-rating">
                    <span class="feedback-label">דירוג:</span>
                    <span class="feedback-stars">
                        <i class="fa fa-star"></i>
                        @if ($feedback->score > 1) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                        @if ($feedback->score > 2) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                        @if ($feedback->score > 3) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                        @if ($feedback->score > 4) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                    </span>
                </div>
                <div class="feedback-comment">
                    <span class="feedback-label">תגובה:</span>
                    <span class="feedback-text">
                        {{ $feedback->comment }}
                    </span>
                </div>
            </div>
            @empty
            <center>No feedback yet</center>
            @endforelse
            
        </div>
    </div>

</div>

<script src="https://maps.googleapis.com/maps/api/js"></script>
<script>

    function initialize() {

        var mapCanvas = document.getElementById('map-canvas');

        var mapOptions = {
          center: new google.maps.LatLng({{ $shop->latitude }}, {{ $shop->longitude }}),
          zoom: 16,
          minZoom: 3,
          scrollwheel: false,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        map = new google.maps.Map(mapCanvas, mapOptions);

        var shop{{ $shop->id }} = new google.maps.Marker({
            map: map,
            position: new google.maps.LatLng({{ $shop->latitude }}, {{ $shop->longitude }}),
            icon: @if ($shop->type == 1) '../img/cart-icon-red.png' @else '../img/donkey-icon.png' @endif,
            title: "{{ $shop->name }}"
        });

    }

    google.maps.event.addDomListener(window, 'load', initialize);

</script>

@endsection
