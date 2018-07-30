@extends('layouts.admin')

@section('content')
<div class="container spark-screen">
    <div class="section row">
        <div class="col-md-10 col-md-offset-1">

            <h3>
                <a href="{{ route('admin.shops.index') }}" class="btn btn-default btn-sm pull-left">Back <i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                <a href="{{ route('admin.shops.edit', $shop) }}" class="btn btn-primary btn-sm pull-left">Edit <i class="fa fa-pencil" aria-hidden="true"></i></a>
                <button class="btn btn-primary btn-sm pull-left" data-toggle="modal" data-target="#note-modal">Add a note <i class="fa fa-file-text" aria-hidden="true"></i></button>
                <a href="{{ route('admin.shops.feedback', $shop) }}" class="btn btn-primary btn-sm pull-left">Feedback <i class="fa fa-comment-o" aria-hidden="true"></i></a>
                {!! Form::open(['method' => 'delete', 'route' => ['admin.shops.destroy', $shop->id]]) !!}
                @if ($shop->trashed())
                <button class="btn btn-success btn-sm pull-left">Restore <i class="fa fa-check-circle" aria-hidden="true"></i></button>
                @else
                <button class="btn btn-warning btn-sm pull-left">Suspend <i class="fa fa-ban" aria-hidden="true"></i></button>
                @endif
                {!! Form::close() !!}
                <i class="fa fa-shopping-cart" aria-hidden="true"></i> {{ $shop->name }}
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
                            <input name="shop_id" value="{{ $shop->id }}" hidden>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

            @if (count($shop->comments) >= 1)

            <hr>

            @foreach ($shop->comments as $comment)

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

                <div class="col-md-6">
                    
                    <h4>Details</h4>

                    <b>Name:</b> {{ $shop->name }}
                    <br>
                    <b>Company name:</b> {{ $shop->company_name }}
                    <br>
                    <b>Company ID:</b> {{ $shop->company_id }}
                    <br>
                    <b>Contact person:</b> {{ $shop->first_name }} {{ $shop->last_name }}
                    <br>
                    <b>E-mail:</b> {{ $shop->user->email }}
                    <br>
                    <b>Phone:</b> {{ $shop->phone }}
                    <br>
                    <b>Mobile phone:</b> {{ $shop->mobile }}
                    <br>
                    <b>Address:</b> {{ $shop->city }}, {{ $shop->street }} {{ $shop->building }}

                </div>

                <div class="col-md-6">
                    
                    <h4>Statistics</h4>

                    <b>Registered:</b> {{ date('d/m/Y H:i', strtotime($shop->created_at)) }}
                    <br>
                    <b>Last updated:</b> {{ date('d/m/Y H:i', strtotime($shop->updated_at)) }}
                    <br>
                    <b>Num. of orders:</b> {{ count($shop->proposalsAll) }}
                    <br>
                    <b>Num. of feedbacks:</b> {{ count($shop->feedbacks) }}
                    <br>
                    <b>Average rating:</b> @if (count($shop->feedbacks) > 0) {{ $shop->feedbacks->avg('score') }} / 5 @else N/A @endif

                </div>

            </div>   

            <hr>

            <h4>Orders</h4>

            @if (count($proposals) > 0)

                <div class="row list list-head">
                    <div class="col col-xs-1">#</div>
                    <div class="col col-xs-1">Status</div>
                    <div class="col col-xs-5">Delivery Address</div>
                    <div class="col col-xs-2">Proposals</div>
                    <div class="col col-xs-3">Created</div>
                </div>

                @foreach ($proposals as $proposal)

                @if ($proposal->order)

                <a href="{{ route('admin.orders.show', $proposal->order) }}" class="row list list-items">
                    <div class="col col-xs-1">{{ $proposal->order->id }}</div>
                    <div class="col col-xs-1">{{ $proposal->status }}</div>
                    <div class="col col-xs-5">{{ $proposal->order->city }}, {{ $proposal->order->street }} {{ $proposal->order->building }}</div>
                    <div class="col col-xs-2">{{ count($proposal->order->proposals) }}</div>
                    <div class="col col-xs-3">{{ date('d/m/Y H:i', strtotime($proposal->order->created_at)) }}</div>
                </a>

                @endif

                @endforeach

                {!! $proposals->render(); !!}

            @else
            <center>No orders were found</center>
            @endif

        </div>
    </div>
</div>
@endsection
