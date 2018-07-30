@extends('layouts.admin')

@section('content')
<div class="container spark-screen">
    <div class="section row">
        <div class="col-md-10 col-md-offset-1">

            <h3>
                <a href="{{ route('admin.shops.show', $shop) }}" class="btn btn-default btn-sm pull-left">Back <i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                <i class="fa fa-shopping-cart" aria-hidden="true"></i> {{ $shop->name }}
            </h3>

            <hr>

            <h4>Feedback</h4>

            @if (count($feedbacks) > 0)

                <div class="row list list-head">
                    <div class="col col-xs-1">#</div>
                    <div class="col col-xs-2">Customer</div>
                    <div class="col col-xs-2">Rating</div>
                    <div class="col col-xs-4">Comment</div>
                    <div class="col col-xs-2">Created</div>
                    <div class="col col-xs-1">Actions</div>
                </div>

                @foreach ($feedbacks as $feedback)

                <div class="row list list-items">
                    <div class="col col-xs-1">{{ $feedback->id }}</div>
                    <div class="col col-xs-2"><a href="{{ route('admin.customers.show', $feedback->customer) }}" target="_blank">{{ $feedback->customer->first_name }} {{ $feedback->customer->last_name }}</a></div>
                    <div class="col col-xs-2">
                        <span class="shop-stars" title="{{ $feedback->score }}">
                            @if ($feedback->score > 0) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                            @if ($feedback->score > 1) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                            @if ($feedback->score > 2) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                            @if ($feedback->score > 3) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                            @if ($feedback->score > 4) <i class="fa fa-star"></i> @else <i class="fa fa-star-o"></i> @endif
                        </span>
                    </div>
                    <div class="col col-xs-4">{{ $feedback->comment }}</div>
                    <div class="col col-xs-2">{{ date('d/m/Y H:i', strtotime($feedback->created_at)) }}</div>
                    <div class="col col-xs-1">
                        {!! Form::open(['method' => 'delete', 'route' => ['admin.feedback.destroy', $feedback]]) !!}
                        <button class="btn btn-danger btn-xs pull-left" style="padding: 0 5px;">Delete <i class="fa fa-times" aria-hidden="true"></i></button>
                        {!! Form::close() !!}
                    </div>
                </div>

                @endforeach

                {!! $feedbacks->render(); !!}

            @else
            <center>No feedbacks were found</center>
            @endif

        </div>
    </div>
</div>
@endsection
