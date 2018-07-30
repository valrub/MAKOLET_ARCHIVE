@extends('layouts.admin')

@section('content')
<div class="container spark-screen">
    <div class="section row">
        <div class="col-md-10 col-md-offset-1">

            <h3>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-default btn-sm pull-left">Back <i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary btn-sm pull-left">Edit <i class="fa fa-pencil" aria-hidden="true"></i></a>
                <button class="btn btn-primary btn-sm pull-left" data-toggle="modal" data-target="#note-modal">Add a note <i class="fa fa-file-text" aria-hidden="true"></i></button>
                {!! Form::open(['method' => 'delete', 'route' => ['admin.customers.destroy', $customer->id]]) !!}
                @if ($customer->trashed())
                <button class="btn btn-success btn-sm pull-left">Restore <i class="fa fa-check-circle" aria-hidden="true"></i></button>
                @else
                <button class="btn btn-warning btn-sm pull-left">Suspend <i class="fa fa-ban" aria-hidden="true"></i></button>
                @endif
                {!! Form::close() !!}
                <i class="fa fa-user" aria-hidden="true"></i> {{ $customer->first_name }} {{ $customer->last_name }}
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
                            <input name="customer_id" value="{{ $customer->id }}" hidden>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

            @if (count($customer->comments) >= 1)

            <hr>

            @foreach ($customer->comments as $comment)

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
                
                <div class="col col-md-6">

                    <h4>Details</h4>

                    <b>Name:</b> {{ $customer->first_name }} {{ $customer->last_name }}
                    <br>
                    <b>E-mail:</b> {{ $customer->user->email }}
                    <br>
                    <b>Phone:</b> {{ $customer->phone }}
                    <br>
                    <b>Address:</b> {{ $customer->city }}, {{ $customer->street }} {{ $customer->building }}
                    
                </div>

                <div class="col col-md-6">

                    <h4>Statistics</h4>

                    <b>Registered:</b> {{ date('d/m/Y H:i', strtotime($customer->created_at)) }}
                    <br>
                    <b>Last updated:</b> {{ date('d/m/Y H:i', strtotime($customer->updated_at)) }}
                    <br>
                    <b>Num. of orders:</b> {{ count($customer->orders) }}

                </div>

            </div>

            <hr>

            <h4>Orders</h4>

            @if (count($orders) > 0)

                <div class="row list list-head">
                    <div class="col col-xs-1">#</div>
                    <div class="col col-xs-1">Status</div>
                    <div class="col col-xs-5">Delivery Address</div>
                    <div class="col col-xs-2">Proposals</div>
                    <div class="col col-xs-3">Created</div>
                </div>

                @foreach ($orders as $order)

                <a href="{{ url('/admin/orders/' . $order->id) }}" class="row list list-items">
                    <div class="col col-xs-1">{{ $order->id }}</div>
                    <div class="col col-xs-1">{{ $order->status }}</div>
                    <div class="col col-xs-5">{{ $order->city }}, {{ $order->street }} {{ $order->building }}</div>
                    <div class="col col-xs-2">{{ count($order->proposals) }}</div>
                    <div class="col col-xs-3">{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</div>
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
