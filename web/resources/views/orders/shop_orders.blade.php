@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div id="shop-orders" class="section row">
        <div class="col-md-10 col-md-offset-1">
            
            <h3>ריכוז הזמנות</h3>

            <hr>

            <h4 class="filters-label">סנן לפי:</h4>
            <div class="row filters-tabs">
                <a href="{{ url('/orders') }}" class="btn col-md-3 @if (!app('request')->input('status')) active @endif" title="All">הכל</a>
                <a href="{{ url('/orders?status=1') }}" class="btn col-md-3 @if (app('request')->input('status') == 1) active @endif" title="Waiting for proposal">ממתין להצעה</a>
                <a href="{{ url('/orders?status=2') }}" class="btn col-md-3 @if (app('request')->input('status') == 2) active @endif" title="Proposal sent">ממתין לאישור</a>
                <a href="{{ url('/orders?status=3') }}" class="btn col-md-3 @if (app('request')->input('status') == 3) active @endif" title="Proposal accepted">ממתין לביצוע</a>
            </div>

            @if (count($proposals) > 0)
                <table class="table table-orders">
                    <thead>
                        <tr>
                            <th class="col-md-1">#</th>
                            <th class="col-md-2">התקבל</th>
                            <th class="col-md-6">מאת</th>
                            <th class="col-md-3">סטטוס</th>
                        </tr>
                    </thead>
                    <tbody>
                @foreach ($proposals as $proposal)
                    <tr>
                        <td>{{ $proposal->order->id }}</td>
                        <td title="{{ $proposal->created_at }}">{{ date('H:i', strtotime($proposal->created_at)) }}</td>
                        <td>
                            <span class="customer-name pull-right">{{ $proposal->order->customer->first_name }} {{ $proposal->order->customer->last_name }}</span>
                            <span class="customer-phone pull-right">{{ $proposal->order->customer->phone }}</span>
                            <span class="customer-address pull-right">{{ $proposal->order->city }}, {{ $proposal->order->building }} {{ $proposal->order->street }}</span>
                        </td>
                        <td>
                            <a href="{{ url('/orders/' . $proposal->order->id) }}">
                            @if ($proposal->status == 1)
                                ממתין להצעה
                            @elseif ($proposal->status == 2)
                                ממתין לאישור
                            @elseif ($proposal->status == 3)
                                ממתין לביצוע
                            @endif
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <center>No orders were found</center>
            @endif
        </div>
    </div>
</div>
@endsection
