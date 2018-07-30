@extends('layouts.admin')

@section('content')
<div class="container spark-screen">
    <div id="shops-list" class="section row">
        <div class="col-md-10 col-md-offset-1">

            <h3>
                <a href="{{ route('admin.shops.create') }}" class="btn btn-primary btn-sm pull-left">Create a shop <i class="fa fa-plus-circle" aria-hidden="true"></i></a>
                <div class="btn-group pull-left">
                    <a href="#" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Extract <i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('admin/shops/extract') }}">Everything</a></li>
                        <li><a href="{{ url('admin/extract') }}">Current page</a></li>
                    </ul>
                </div>
                <button class="btn btn-default btn-sm pull-left search-button">Search <i class="fa fa-bars" aria-hidden="true"></i></button>
                Shops
            </h3>

            <form action="{{ route('admin.shops.index') }}" method="GET" class="well search-well" @if (Request::get('name') || Request::get('phone') || Request::get('email')) style="display: block;" @endif>
                <div class="col col-md-4">
                    <label for="search-name">Name:</label>
                    <input type="text" name="name" class="form-control" id="search-name" value="{{ Request::get('name') }}">
                </div>
                <div class="col col-md-4">
                    <label for="search-phone">Phone:</label>
                    <input type="text" name="phone" class="form-control" id="search-phone" value="{{ Request::get('phone') }}">
                </div>
                <div class="col col-md-4">
                    <label for="search-email">Email:</label>
                    <input type="text" name="email" class="form-control" id="search-email" value="{{ Request::get('email') }}">
                </div>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fa fa-search" aria-hidden="true"></i> Search
                </button>
                @if (Request::get('name') || Request::get('phone') || Request::get('email'))
                <a href="{{ route('admin.shops.index') }}" class="btn btn-sm btn-default">
                    <i class="fa fa-ban" aria-hidden="true"></i> Clear
                </a>
                @endif
            </form>

            <hr>

            @if (count($shops) > 0)

            	<div class="row list list-head">
                    <div class="col col-xs-1">#</div>
                    <div class="col col-xs-2">Name</div>
                    <div class="col col-xs-2">Company</div>
                    <div class="col col-xs-2">City</div>
                    <div class="col col-xs-2">Address</div>
                    <div class="col col-xs-2">Joined</div>
                    <div class="col col-xs-1 center">Active</div>
                </div>

                @foreach ($shops as $shop)

                <a href="{{ url('/admin/shops/' . $shop->id) }}" class="row list list-items">
                    <div class="col col-xs-1">{{ $shop->id }}</div>
                    <div class="col col-xs-2"><b>{{ $shop->name }}</b></div>
                    <div class="col col-xs-2">{{ $shop->company_name }}</div>
                    <div class="col col-xs-2">{{ $shop->city }}</div>
                    <div class="col col-xs-2">{{ $shop->street }} {{ $shop->building }}</div>
                    <div class="col col-xs-2">{{ date('d/m/Y H:i', strtotime($shop->created_at)) }}</div>
                    <div class="col col-xs-1 center">
                        @if ($shop->trashed())
                        <i class="fa fa-minus-square txt-warning" aria-hidden="true"></i>
                        @else
                        <i class="fa fa-check-square txt-success" aria-hidden="true"></i>
                        @endif
                    </div>
                </a>

                @endforeach

                {!! $shops->render(); !!}

            @else
            <center>No shops were found</center>
            @endif
        </div>
    </div>
</div>
@endsection
