@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div id="orders-list" class="section row">
        <div class="col-md-10 col-md-offset-1">

            <h3>{{ trans('lang.orders') }}</h3>

            <hr>

            <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                @if(Session::has('alert-' . $msg))
                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                @endif
                @endforeach
            </div>

            @if (count($orders) > 0)

                @foreach ($orders as $order)

                    <?php 

                        if (!isset($currentOrder)) {
                            $currentOrder = 1;
                        } else {
                            $currentOrder++;
                        }

                        $printTitle = false;

                        if (!isset($currentMonth)) {
                            $currentMonth = date('F', strtotime($order->created_at));
                            $printTitle = true;
                        }

                        if ($currentMonth != date('F', strtotime($order->created_at))) {
                            $printTitle = true;
                        }

                        $currentMonth = date('F', strtotime($order->created_at));
                        $currentYear = date('Y', strtotime($order->created_at));

                        if ($printTitle == true) {

                            $totalPrice = 0;

                            foreach ($orders as $orderJson) {
                                if ($currentMonth == date('F', strtotime($orderJson->created_at))) {
                                    $totalPrice += $orderJson->proposal->price;
                                }
                            }

                    ?>

                    <div class="row list list-head">
                        <div class="col col-xs-3">{{ trans('lang.' . $currentMonth) }} {{ $currentYear }}</div>
                        <div class="col col-xs-5 col-xs-offset-2">סה״כ רכישות: {{ $totalPrice }} ₪</div>
                        <div class="col col-xs-2">שולם</div>
                    </div>
                    
                    <div class="row list list-subhead">
                        <div class="col col-xs-12">פירוט רכישות:</div>
                    </div>

                    <?php } ?>

                    <a href="{{ url('/orders/' . $order->id) }}" class="row list list-items">
                        <div class="col col-xs-1">{{ $order->id }}</div>
                        <div class="col col-xs-3">{{ date('d.m.Y', strtotime($order->created_at)) }}</div>
                        <div class="col col-xs-3">{{ $order->proposal->shop->name }}</div>
                        <div class="col col-xs-3">@if ($order->proposal->price) {{ $order->proposal->price }} @else &minus; @endif ₪</div>
                        <div class="col col-xs-2">
                        @if ($order->status == 6)
                            שלם
                        @else
                            לא שלם
                        @endif
                        </div>
                    </a>

                @endforeach

            @else
            <center>No orders were found</center>
            @endif
        </div>
    </div>
</div>
@endsection
