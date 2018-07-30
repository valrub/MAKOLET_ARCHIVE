@extends('layouts.admin')

@section('content')
<div class="container spark-screen">
    <div class="section row">
        <div class="col-md-10 col-md-offset-1">

            <h3>
                Orders
                <div class="btn-group pull-left">
                    <a href="#" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Extract <i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('admin/orders/extract') }}">Everything</a></li>
                        <li><a href="{{ url('admin/extract') }}">Current page</a></li>
                    </ul>
                </div>
                <button class="btn btn-default btn-sm pull-left search-button">Search <i class="fa fa-bars" aria-hidden="true"></i></button>
            </h3>

            <form action="{{ route('admin.orders.index') }}" method="GET" class="well search-well" @if (Request::get('search')) style="display: block;" @endif>
                <input type="checkbox" name="status[]" value="1" id="status-1"> <label for="status-1">New</label><br>
                <input type="checkbox" name="status[]" value="2" id="status-2"> <label for="status-2">Proposal in process</label><br>
                <input type="checkbox" name="status[]" value="3" id="status-3"> <label for="status-3">Order in process</label><br>
                <input type="checkbox" name="status[]" value="4" id="status-4"> <label for="status-4">Proposal declined</label><br>
                <input type="checkbox" name="status[]" value="5" id="status-5"> <label for="status-5">Closed</label><br>
                <input type="checkbox" name="status[]" value="6" id="status-6"> <label for="status-6">Paid</label><br>
                <input type="checkbox" name="status[]" value="7" id="status-7"> <label for="status-7">Cancelled</label><br>
                <input type="checkbox" name="status[]" value="8" id="status-8"> <label for="status-8">Dispute</label><br>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fa fa-search" aria-hidden="true"></i> Search
                </button>
                @if (Request::get('search'))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-default">
                    <i class="fa fa-ban" aria-hidden="true"></i> Clear
                </a>
                @endif
            </form>

            <hr>

            @if (count($orders) > 0)

            	<div class="row list list-head">
                    <div class="col col-xs-1">#</div>
                    <div class="col col-xs-1">Status</div>
                    <div class="col col-xs-3">Customer</div>
                    <div class="col col-xs-3">Delivery Address</div>
                    <div class="col col-xs-2">Proposals</div>
                    <div class="col col-xs-2">Created</div>
                </div>

                @foreach ($orders as $order)

                <a href="{{ url('/admin/orders/' . $order->id) }}" class="row list list-items">
                    <div class="col col-xs-1">{{ $order->id }}</div>
                    <div class="col col-xs-1">{{ $order->status }}</div>
                    <div class="col col-xs-3">{{ $order->customer->first_name }} {{ $order->customer->last_name }}</div>
                    <div class="col col-xs-3">{{ $order->city }}, {{ $order->street }} {{ $order->building }}</div>
                    <div class="col col-xs-2">{{ count($order->proposals) }}</div>
                    <div class="col col-xs-2">{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</div>
                </a>

                @endforeach

                {!! $orders->render(); !!}

            @else
            <center>No orders were found</center>
            @endif
        </div>
    </div>
</div>
@endsection
