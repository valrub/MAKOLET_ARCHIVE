@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div id="orders-list" class="section row">
        <div class="col-md-10 col-md-offset-1">

            <h3>{{ trans('lang.orders') }}</h3>

            <hr>

            @if (count($proposals) > 0)

                @foreach ($proposals as $proposal)

                    <?php 

                        if (!isset($currentOrder)) {
                            $currentOrder = 1;
                        } else {
                            $currentOrder++;
                        }

                        $printTitle = false;

                        if (!isset($currentMonth)) {
                            $currentMonth = date('F', strtotime($proposal->created_at));
                            $printTitle = true;
                        }

                        if ($currentMonth != date('F', strtotime($proposal->created_at))) {
                            $printTitle = true;
                        }

                        $currentMonth = date('F', strtotime($proposal->created_at));
                        $currentYear = date('Y', strtotime($proposal->created_at));

                        if ($printTitle == true) {

                            $totalPrice = 0;

                            foreach ($proposals as $proposalJson) {
                                if ($currentMonth == date('F', strtotime($proposalJson->created_at))) {
                                    $totalPrice += $proposalJson->price;
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

                    <a href="{{ url('/orders/' . $proposal->order->id) }}" class="row list list-items">
                        <div class="col col-xs-1">{{ $proposal->order->id }}</div>
                        <div class="col col-xs-2">{{ date('d.m.Y', strtotime($proposal->created_at)) }}</div>
                        <div class="col col-xs-5 no-wrap">
                            <span class="customer-name pull-right">{{ $proposal->order->customer->first_name }} {{ $proposal->order->customer->last_name }}</span>
                            <span class="customer-phone pull-right">{{ $proposal->order->customer->phone }}</span>
                            <span class="customer-address pull-right">{{ $proposal->order->city }}, {{ $proposal->order->building }} {{ $proposal->order->street }}</span>
                        </div>
                        <div class="col col-xs-2">@if ($proposal->price) {{ $proposal->price }} @else &minus; @endif ₪</div>
                        <div class="col col-xs-2">
                        @if ($proposal->status == 6)
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
