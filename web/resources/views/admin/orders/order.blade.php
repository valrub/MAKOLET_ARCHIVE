@extends('layouts.admin')

@section('content')
<div class="container spark-screen">
    <div class="section row">
        <div class="col-md-10 col-md-offset-1">

            <h3>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-default btn-sm pull-left">Back <i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary btn-sm pull-left">Edit <i class="fa fa-pencil" aria-hidden="true"></i></a>
                <button class="btn btn-primary btn-sm pull-left" data-toggle="modal" data-target="#note-modal">Add a note <i class="fa fa-file-text" aria-hidden="true"></i></button>
                <i class="fa fa-truck" aria-hidden="true"></i> Order #{{ $order->id }}
            </h3>

            <div class="modal fade" id="note-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        {!! Form::open(['method' => 'post', 'route' => ['admin.comments.store']]) !!}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">New note</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row no-margin">
                                <label>Importance</label>
                                <select name="level" id="note-level" class="col-xs-12">
                                    <option value="1">1 - Normal</option>
                                    <option value="2">2 - Important</option>
                                    <option value="3">3 - Critical</option>
                                </select>
                            </div>
                            <div class="form-group row no-margin">
                                <label for="note-comment">Note</label>
                                <textarea name="comment" id="note-comment" class="col-xs-12"></textarea>
                            </div>
                            <input name="order_id" value="{{ $order->id }}" hidden>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

            @if (count($order->comments) >= 1)

            <hr>

            @foreach ($order->comments as $comment)

            <div class="note @if ($comment->level == 3) note-critical @endif">
                {!! Form::open(['method' => 'delete', 'route' => ['admin.comments.destroy', $comment]]) !!}
                <button type="submit" class="close"><span aria-hidden="true">&times;</span></button>
                {!! Form::close() !!}
                <p>{{ $comment->comment }}</p>
                <abbr>
                    @if ($comment->level > 1) <i class="fa fa-exclamation-triangle" aria-hidden="true" style="margin-left: 5px;"></i> @endif
                    {{ date('d/m/Y H:i', strtotime($comment->created_at)) }}
                </abbr>
            </div>

            @endforeach

            @endif

            <hr>

            <div class="row">

                <div class="col-sm-4">
                    
                    <h4>Customer</h4>

                    <b>Name:</b> <a href="{{ route('admin.customers.show', $order->customer) }}">{{ $order->customer->first_name }} {{ $order->customer->last_name }}</a>
                    <br>
                    <b>E-mail:</b> {{ $order->customer->user->email }}
                    <br>
                    <b>Phone:</b> {{ $order->customer->phone }}
                    <br>
                    <b>Address:</b> {{ old('city', $order->city) }}, {{ old('street', $order->street) }} <span>{{ old('building', $order->building) }}</span><!--
                                -->@if ($order->entrance), entr. <span>{{ old('entrance', $order->entrance) }}</span> @endif<!--
                                -->@if ($order->apartment), ap. <span>{{ old('apartment', $order->apartment) }}</span> @endif

                </div>

                <div class="col-sm-4">
                    
                    <h4>Details</h4>

                    <b>Status:</b>

                        @if ($order->status == 1)
                            <span class="label label-primary">{{ $order->status }}</span> New
                        @elseif ($order->status == 2)
                            <span class="label label-primary">{{ $order->status }}</span> Proposal sent
                        @elseif ($order->status == 3)
                            <span class="label label-primary">{{ $order->status }}</span> Proposal accepted
                        @elseif ($order->status == 4)
                            <span class="label label-default">{{ $order->status }}</span> Proposal declined
                        @elseif ($order->status == 5)
                            <span class="label label-success">{{ $order->status }}</span> Closed
                        @elseif ($order->status == 6)
                            <span class="label label-success">{{ $order->status }}</span> Paid
                        @elseif ($order->status == 7)
                            <span class="label label-warning">{{ $order->status }}</span> Cancelled
                        @elseif ($order->status == 8)
                            <span class="label label-danger">{{ $order->status }}</span> Dispute
                        @else
                            N/A
                        @endif

                    <br>
                    <b>Created:</b> {{ date('d/m/Y H:i', strtotime($order->created_at)) }}
                    <br>
                    <b>Last updated:</b> {{ date('d/m/Y H:i', strtotime($order->updated_at)) }}
                    <br>
                    <b>Num. of proposals:</b> {{ count($order->proposals) }}

                </div>

                <div class="col-sm-4">

                    <h4>Status history</h4>

                    <b>Order created:</b> {{ date('d/m/Y H:i', strtotime($order->created_at)) }}
                    <br>
                    <b>Proposed sent:</b> @if ($order->proposal && $order->proposal->proposed_at) {{ date('d/m/Y H:i', strtotime($order->proposal->proposed_at)) }} @else &minus; @endif
                    <br>
                    <b>Proposal accepted:</b> @if ($order->proposal && $order->proposal->accepted_at) {{ date('d/m/Y H:i', strtotime($order->proposal->accepted_at)) }} @else &minus; @endif
                    <br>
                    <b>Order processed:</b> @if ($order->proposal && $order->proposal->processed_at) {{ date('d/m/Y H:i', strtotime($order->proposal->processed_at)) }} @else &minus; @endif

                </div>

            </div>  

            <hr>

            <h4>Goods</h4>

            <!--
            <div class="row list list-head">
                <div class="col col-xs-2">Quantity</div>
                <div class="col col-xs-12">Name</div>
            </div>
            -->

            @foreach ($order->goods as $goods)

            <a class="row list list-items">
                <!--<div class="col col-xs-2">{{ $goods->quantity }}</div>-->
                <div class="col col-xs-12">{{ $goods->name }}</div>
            </a>

            @endforeach

            <div class="customer-notes">
                <span>!</span>
                הערות מהלקוח:
                {{ $order->customer_notes }}
            </div>

            @if ($order->proposal)
            <div id="delivery-map" class="map-canvas"></div>
            @endif

            <hr>

            <h4>Proposals</h4>

            @if (count($order->proposals) > 0)

                <div class="row list list-head">
                    <div class="col col-xs-2">Status</div>
                    <div class="col col-xs-3">Shop</div>
                    <div class="col col-xs-3">Shop note</div>
                    <div class="col col-xs-2">Delivery time</div>
                    <div class="col col-xs-2">Last update</div>
                </div>

                @foreach ($order->proposals as $proposal)

                <a class="row list list-items">
                    <div class="col col-xs-2">

                        @if ($proposal->status == 1)
                            <span class="label label-primary">{{ $proposal->status }}</span> New
                        @elseif ($proposal->status == 2)
                            <span class="label label-primary">{{ $proposal->status }}</span> Proposal sent
                        @elseif ($proposal->status == 3)
                            <span class="label label-primary">{{ $proposal->status }}</span> Proposal accepted
                        @elseif ($proposal->status == 4)
                            <span class="label label-default">{{ $proposal->status }}</span> Proposal declined
                        @elseif ($proposal->status == 5)
                            <span class="label label-success">{{ $proposal->status }}</span> Closed
                        @elseif ($proposal->status == 6)
                            <span class="label label-success">{{ $proposal->status }}</span> Paid
                        @elseif ($proposal->status == 7)
                            <span class="label label-warning">{{ $proposal->status }}</span> Cancelled
                        @elseif ($proposal->status == 8)
                            <span class="label label-danger">{{ $proposal->status }}</span> Dispute
                        @else
                            N/A
                        @endif

                    </div>
                    <div class="col col-xs-3">
                        {{ $proposal->shop->name }}
                        @if ($proposal->id == $order->proposal_id)
                            <i class="fa fa-check" aria-hidden="true"></i>
                        @endif</div>
                    <div class="col col-xs-3">@if ($proposal->shop_note) {{ $proposal->shop_note }} @else - @endif</div>
                    <div class="col col-xs-2">@if ($proposal->delivery_time) {{ date('d/m/Y H:i', strtotime($proposal->delivery_time)) }} @else - @endif</div>
                    <div class="col col-xs-2">{{ date('d/m/Y H:i', strtotime($proposal->updated_at)) }}</div>
                </a>

                @endforeach

            @else
            <center>No proposals were found</center>
            @endif

        </div>
    </div>
</div>
@if ($order->proposal)
<script src="https://maps.googleapis.com/maps/api/js"></script>
<script>

    var center = new google.maps.LatLng({{ $order->latitude }}, {{ $order->longitude }});
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

        var start = new google.maps.LatLng({{ $order->proposal->shop->latitude }}, {{ $order->proposal->shop->longitude }});
        var end = new google.maps.LatLng({{ $order->latitude }}, {{ $order->longitude }});

        var request = {
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode.WALKING
        };

        directionsService.route(request, function(result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);
                var leg = result.routes[ 0 ].legs[ 0 ];
                makeMarker( leg.start_location, icons.start, "{{ $order->proposal->shop->name }}" );
                makeMarker( leg.end_location, icons.end, "{{ $order->customer->first_name }} {{ $order->customer->last_name }}" );
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
@endif
@endsection
