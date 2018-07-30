@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div id="new-order" class="section row">
        <div class="col-md-10 col-md-offset-1">
            
            <h3 class="center">
                <span class="order-number">{{ trans('lang.order_number') }} {{ $order->id }}</span>
                {{ $order->created_at->format('d.m.Y') }}
                @if ($order->status <= 3)
                {{ Form::open(['route' => ['orders.destroy', $order->id], 'method' => 'delete', 'class' => 'cancel-order']) }}
                    <button class="btn btn-link" type="submit" data-toggle="tooltip" data-placement="left" title="Cancel order"><i class="fa fa-times-circle" aria-hidden="true"></i></button>
                {{ Form::close() }}
                @endif
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

            <ul class="nav nav-tabs">
                <li class="completed"><a href="#goods" data-toggle="tab" aria-expanded="false">{{ trans('lang.goods_list') }}</a></li>
                <li class="completed"><a href="#shops" data-toggle="tab" aria-expanded="false">{{ trans('lang.address_and_shops') }}</a></li>
                @if ($order->status > 2)
                <li class="completed"><a href="#proposals" data-toggle="tab" aria-expanded="false">{{ trans('lang.proposals') }}</a></li>
                <li class="completed active"><a href="#status" data-toggle="tab" aria-expanded="true">{{ trans('lang.order_status') }}</a></li>
                @else
                <li class="active"><a href="#proposals" data-toggle="tab" aria-expanded="true">{{ trans('lang.proposals') }}</a></li>
                <li class="inactive"><a aria-expanded="false">{{ trans('lang.order_status') }}</a></li>
                @endif
            </ul>

            <div class="tab-content">

                <div class="tab-pane fade" id="goods">

                    <table>
                        <thead>
                            <tr>
                                <th>מוצר</th>
                                <!--<th class="center">כמות</th>-->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->goods as $good)
                            <tr>
                                <td><input type="text" value="{{ $good->name }}" readonly /></td>
                                <!--<td><input type="number" value="{{ $good->quantity }}" readonly /></td>-->
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <textarea name="notes" placeholder="הערות:" readonly>{{ $order->customer_notes }}</textarea>

                </div>

                <div class="tab-pane fade" id="shops">

                   <div class="row">
                        <div class="col col-md-12 delivery-address">
                            <label>כתובת משלוח:</label>
                            <i class="fa fa-map-marker"></i>
                            <span title="{{ $order->city }}, {{ $order->street }} {{ $order->building }} @if ($order->entrance) ,כניסה {{ $order->entrance }} @endif @if ($order->apartment) ,דירה {{ $order->apartment }} @endif">{{ old('city', $order->city) }}, {{ old('street', $order->street) }} <span>{{ old('building', $order->building) }}</span><!--
                                -->@if ($order->entrance) ,כניסה <span>{{ old('entrance', $order->entrance) }}</span> @endif<!--
                                -->@if ($order->apartment) ,דירה <span>{{ old('apartment', $order->apartment) }}</span> @endif</span>
                        </div>
                    </div>

                    <div id="shops-map">
                        
                    </div>

                    <div class="row">
                        <div class="col col-md-12">
                            <label>חנויות נבחרות:</label>
                        </div>
                    </div>

                    @foreach ($order->proposals as $proposal)
                    <div class="row shops-list">
                        <div class="col col-md-5 shop-name">
                            <label class="checkbox-on"></label>
                            {{ $proposal->shop->city }}, {{ $proposal->shop->street }} {{ $proposal->shop->building }}, 
                            <b>{{ $proposal->shop->name }}</b>
                        </div>
                        <div class="col col-md-2 shop-stars">
                            @if ($proposal->shop->feedbacks->avg('score') > 0) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                            @if ($proposal->shop->feedbacks->avg('score') > 1) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                            @if ($proposal->shop->feedbacks->avg('score') > 2) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                            @if ($proposal->shop->feedbacks->avg('score') > 3) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                            @if ($proposal->shop->feedbacks->avg('score') > 4) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                        </div>
                        <div class="col col-md-2 shop-reviews right">
                            יש {{ count($proposal->shop->feedbacks) }} חוות דעת
                        </div>
                        <div class="col col-md-3 right">
                            <a href="{{ route('shops.show', $proposal->shop) }}" target="_blank" class="btn btn-secondary @if ($proposal->shop->trashed()) disabled @endif">פרטים</a>
                        </div>
                    </div>
                    @endforeach

                </div>

                @if ($order->status > 2)
                <div class="tab-pane fade" id="proposals">
                @else
                <div class="tab-pane fade active in" id="proposals">
                @endif
                    <p class="proposals-counter">You asked {{ count($order->proposals) }} shops for proposal</p>
                    @forelse ($order->proposals as $proposal)
                        @if ($proposal->status >= 2 && $proposal->status != 4)
                        <div class="row proposal display-table">
                            <div class="col col-md-4">
                                <span class="proposal-recieved">{{ $proposal->created_at->format('H:i') }}</span>
                                <span class="proposal-shop">{{ $proposal->shop->name }}</span>
                            </div>
                            <div class="col col-md-4">
                                <div class="proposal-delivery">
                                    שעת הספקה 
                                    @if ($proposal->delivery_time)
                                    {{ date('H:i', strtotime($proposal->delivery_time)) }}
                                    @else
                                    -
                                    @endif
                                </div>
                                <div class="proposal-notes">
                                    הערות:
                                    @if ($proposal->shop_notes)
                                    {{ $proposal->shop_notes }}
                                    @else
                                    -
                                    @endif
                                </div>
                            </div>
                            <div class="col col-md-4 right">
                            @if ($order->status == 2)
                                @if ($proposal->status == 1)
                                <span>Waiting for proposal</span>
                                @elseif ($proposal->status == 2)
                                <button class="btn btn-success" data-proposal-accept="1" data-proposal="{{ $proposal->id }}">לקבל הצעה</button>
                                <button class="btn btn-danger" data-proposal-accept="0" data-proposal="{{ $proposal->id }}">למחוק מהרשימה</button>
                                @elseif ($proposal->status == 3)
                                <span>Proposal accepted</span>
                                @elseif ($proposal->status == 4)
                                <span>Proposal declined</span>
                                @endif
                            @else
                                @if ($proposal->status == 1)
                                <span>Waiting for proposal</span>
                                @elseif ($proposal->status == 2)
                                <span>Proposal recieved</span>
                                @elseif ($proposal->status == 3 || $order->proposal_id == $proposal->id)
                                <span>Proposal accepted</span>
                                @elseif ($proposal->status == 4)
                                <span>Proposal declined</span>
                                @endif
                            @endif
                            </div>
                        </div>
                        @endif
                    @empty
                    <p>No proposals</p>
                    @endforelse
                </div>

                @if ($order->status > 2)
                <div class="tab-pane fade active in" id="status">
                    <div class="row status-details">
                        <div class="col col-md-9">
                            <div class="status-entry">
                                <span class="status-label status-created">הרשימה נשלחה</span>
                                <span class="status-datetime">
                                    {{ date('d.m.Y', strtotime($order->created_at)) }}
                                    &nbsp;&nbsp;&nbsp;
                                    {{ date('H:i', strtotime($order->created_at)) }}
                                </span>
                            </div>
                            <div class="status-entry">
                                <span class="status-label status-recieved">ההזמנה התקבלה</span>
                                <span class="status-datetime">
                                    {{ date('d.m.Y', strtotime($order->proposal->proposed_at)) }}
                                    &nbsp;&nbsp;&nbsp;
                                    {{ date('H:i', strtotime($order->proposal->proposed_at)) }}
                                </span>
                            </div>
                            <div class="status-entry">
                                <span class="status-label status-accepted">ההצעה אושרה</span>
                                <span class="status-datetime">
                                    {{ date('d.m.Y', strtotime($order->proposal->accepted_at)) }}
                                    &nbsp;&nbsp;&nbsp;
                                    {{ date('H:i', strtotime($order->proposal->accepted_at)) }}
                                </span>
                            </div>
                            @if ($order->status >= 5)
                            <div class="status-entry">
                                <span class="status-label status-closed">ההצעה מתבצעת</span>
                                <span class="status-datetime">
                                    {{ date('d.m.Y', strtotime($order->proposal->processed_at)) }}
                                    &nbsp;&nbsp;&nbsp;
                                    {{ date('H:i', strtotime($order->proposal->processed_at)) }}
                                </span>
                            </div>
                            <br>
                            <div class="status-price">@if ($order->proposal->price) {{ $order->proposal->price }} @else &minus; @endif &#8362; - סכום</div>
                            <div class="status-price">@if ($order->proposal->delivery_price) {{ $order->proposal->delivery_price }} @else &minus; @endif &#8362; - מחיר המשלוח</div>
                            <div class="status-price-total">{{ $order->proposal->price + $order->proposal->delivery_price }} &#8362; - מחיר כולל</div>
                            @endif
                        </div>
                        <div class="col col-md-3 center">
                            <span class="status-stamp stamp-success">
                                ההזמנה<br>מתבצעת {{ date('H:i', strtotime($order->proposal->accepted_at)) }}
                            </span>
                            <br><br><span style="font-size: 18px; color: #78c848; font-weight: bold;">מועד משלוח {{ date('H:i', strtotime($order->proposal->delivery_time)) }}</span>
                        </div>
                    </div>
                    <div class="row feedback-form">
                        @if (!$order->feedback)
                        {!! Form::model($order, ['method' => 'POST', 'action' => ['FeedbackController@store']]) !!}
                            <div class="col col-md-3 center">
                                דרג את החנות:
                                <br>מכולת יוסי
                                <br>ז׳בוטינסקי 45 רמת -גן
                                <div class="feedback-stars">
                                @if (!$order->feedback)
                                    <input type="radio" value="1" name="rating" id="feedback-star-1" data-rating="1" /><label for="feedback-star-1" data-rating="1"><i class="fa fa-star star-white"></i></label><input type="radio" value="2" name="rating" id="feedback-star-2" data-rating="2" /><label for="feedback-star-2" data-rating="2"><i class="fa fa-star star-white"></i></label><input type="radio" value="3" name="rating" id="feedback-star-3" data-rating="3" /><label for="feedback-star-3" data-rating="3"><i class="fa fa-star star-white"></i></label><input type="radio" value="4" name="rating" id="feedback-star-4" data-rating="4" /><label for="feedback-star-4" data-rating="4"><i class="fa fa-star star-white"></i></label><input type="radio" value="5" name="rating" id="feedback-star-5" data-rating="5" /><label for="feedback-star-5" data-rating="5"><i class="fa fa-star star-white"></i></label>
                                @else
                                    <i class="fa fa-star"></i>@if ($order->feedback->score > 1)<i class="fa fa-star"></i>@else<i class="fa fa-star star-white"></i>@endif<!--
                                    -->@if ($order->feedback->score > 2)<i class="fa fa-star"></i>@else<i class="fa fa-star star-white"></i>@endif<!--
                                    -->@if ($order->feedback->score > 3)<i class="fa fa-star"></i>@else<i class="fa fa-star star-white"></i>@endif<!--
                                    -->@if ($order->feedback->score > 4)<i class="fa fa-star"></i>@else<i class="fa fa-star star-white"></i>@endif
                                @endif
                                </div>
                            </div>
                            <div class="col col-md-6">
                                @if (!$order->feedback)
                                <textarea name="comment" maxlength="255" placeholder="?אם קיבלת את המשלוח בזמן
?האם המחיר היה לשביעות רצונף
?האם המוצרים הגיעו במצב טוב
?האם המשלוח תאם להזמנה"></textarea>
                                @else
                                <textarea name="comment" maxlength="255" value="{{ $order->feedback->comment }}" readonly></textarea>
                                @endif
                                <input type="hidden" name="order" value="{{ $order->id }}" />
                            </div>
                            <div class="col col-md-3 center">
                                @if (!$order->feedback)
                                <button type="submit" class="btn btn-secondary">שלח דירוג</button>
                                @else
                                <button type="submit" class="btn btn-secondary">שלח דירוג</button>
                                @endif
                                <div>
                                <!--
                                    <a class="btn-round btn-twitter"><i class="fa fa-twitter"></i></a>
                                    <a class="btn-round btn-facebook"><i class="fa fa-facebook"></i></a>
                                    <a class="btn-round btn-google-plus"><i class="fa fa-google-plus"></i></a>
                                -->
                                </div>
                            </div>
                        {!! Form::close() !!}
                        @else
                            <div class="col col-md-3 center">
                                דרג את החנות:
                                <br>מכולת יוסי
                                <br>ז׳בוטינסקי 45 רמת -גן
                                <div class="feedback-stars">
                                    <i class="fa fa-star"></i>@if ($order->feedback->score > 1)<i class="fa fa-star"></i>@else<i class="fa fa-star star-white"></i>@endif<!--
                                    -->@if ($order->feedback->score > 2)<i class="fa fa-star"></i>@else<i class="fa fa-star star-white"></i>@endif<!--
                                    -->@if ($order->feedback->score > 3)<i class="fa fa-star"></i>@else<i class="fa fa-star star-white"></i>@endif<!--
                                    -->@if ($order->feedback->score > 4)<i class="fa fa-star"></i>@else<i class="fa fa-star star-white"></i>@endif
                                </div>
                            </div>
                            <div class="col col-md-6">
                                <textarea name="comment" maxlength="255" readonly>{{ $order->feedback->comment }}</textarea>
                                <input type="hidden" name="order" value="{{ $order->id }}" />
                            </div>
                            <div class="col col-md-3 center">
                                <button type="submit" class="btn btn-secondary" disabled>שלח דירוג</button>
                                <div>
                                <!--
                                    <a class="btn-round btn-twitter"><i class="fa fa-twitter"></i></a>
                                    <a class="btn-round btn-facebook"><i class="fa fa-facebook"></i></a>
                                    <a class="btn-round btn-google-plus"><i class="fa fa-google-plus"></i></a>
                                -->
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                <div class="tab-pane fade" id="status">
                @endif
                </div>

            </div>

        </div>
    </div>

</div>

<script src="https://maps.googleapis.com/maps/api/js"></script>
<script>

    function initialize() {

        var mapCanvas = document.getElementById('shops-map');

        var mapOptions = {
          center: new google.maps.LatLng({{ $order->latitude }}, {{ $order->longitude }}),
          zoom: 14,
          minZoom: 3,
          scrollwheel: false,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        map = new google.maps.Map(mapCanvas, mapOptions);

        var customer = new google.maps.Marker({
            map: map,
            position: new google.maps.LatLng({{ $order->latitude }}, {{ $order->longitude }}),
            icon: '../img/location-icon-blue.png',
            title: 'You are here.'
        });

        @foreach ($order->proposals as $proposal)
        var shop{{ $proposal->shop->id }} = new google.maps.Marker({
            map: map,
            position: new google.maps.LatLng({{ $proposal->shop->latitude }}, {{ $proposal->shop->longitude }}),
            icon: @if ($proposal->shop->type == 1) '../img/cart-icon-red.png' @else '../img/donkey-icon.png' @endif,
            title: "{{ $proposal->shop->name }}"
        });
        @endforeach

    }

    google.maps.event.addDomListener(window, 'load', initialize);
    
    $('a[href="#shops"]').click(function() {
        setTimeout(function() {
            google.maps.event.trigger(map, 'resize');
            map.setCenter(new google.maps.LatLng({{ $order->latitude }}, {{ $order->longitude }}));
        }, 300);
    });

</script>
@endsection
