@extends('layouts.admin')

@section('content')
<div class="container spark-screen">
    <div id="profile-form" class="section row">
        <div class="col-md-10 col-md-offset-1">

            <h3>
                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-default btn-sm pull-left">Back <i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                <i class="fa fa-truck" aria-hidden="true"></i> Order #{{ $order->id }}
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

            {!! Form::model($order, ['class' => 'form-horizontal col-md-8 col-md-offset-2', 'method' => 'PATCH', 'action' => ['Admin\OrderController@update', $order]]) !!}

                <fieldset>
                    
                    <legend><b>Order</b></legend>

                    <div class="col col-md-12">
                        <label for="order-status">Status:</label>
                        <select id="order-status" name="status" style="width: 100%;">
                            <option value="1" @if ($order->status == 1) selected @endif>1 - New</option>
                            <option value="2" @if ($order->status == 2) selected @endif>2 - Proposal in process</option>
                            <option value="3" @if ($order->status == 3) selected @endif>3 - Order in process</option>
                            <option value="4" @if ($order->status == 4) selected @endif>4 - Declined</option>
                            <option value="5" @if ($order->status == 5) selected @endif>5 - Closed</option>
                            <option value="6" @if ($order->status == 6) selected @endif>6 - Paid</option>
                            <option value="7" @if ($order->status == 7) selected @endif>7 - Cancelled</option>
                            <option value="8" @if ($order->status == 8) selected @endif>8 - Dispute</option>
                        </select>
                    </div>

                    <legend><b>Proposals</b></legend>

                    @if (count($order->proposals) > 0)

                        <div class="row list list-head">
                            <div class="col col-xs-5 no-margin">Status</div>
                            <div class="col col-xs-4 no-margin">Shop</div>
                            <div class="col col-xs-3 no-margin">Last update</div>
                        </div>

                        @foreach ($order->proposals as $proposal)

                        <a class="row list list-items">
                            <div class="col col-xs-5 no-margin">
                                
                                <select id="order-status" name="proposals[{{ $proposal->id }}]" style="width: 80%; padding: 1px 3px; color: #333;">
                                    <option value="1" @if ($proposal->status == 1) selected @endif>1 - New</option>
                                    <option value="2" @if ($proposal->status == 2) selected @endif>2 - Proposal in process</option>
                                    <option value="3" @if ($proposal->status == 3) selected @endif>3 - Order in process</option>
                                    <option value="4" @if ($proposal->status == 4) selected @endif>4 - Declined</option>
                                    <option value="5" @if ($proposal->status == 5) selected @endif>5 - Closed</option>
                                    <option value="6" @if ($proposal->status == 6) selected @endif>6 - Paid</option>
                                    <option value="7" @if ($proposal->status == 7) selected @endif>7 - Cancelled</option>
                                    <option value="8" @if ($proposal->status == 8) selected @endif>8 - Dispute</option>
                                </select>

                            </div>
                            <div class="col col-xs-4 no-margin">
                                {{ $proposal->shop->name }}
                                @if ($proposal->id == $order->proposal_id)
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                @endif
                            </div>
                            <div class="col col-xs-3 no-margin">{{ date('d/m/Y H:i', strtotime($proposal->updated_at)) }}</div>
                        </a>

                        @endforeach

                    @else
                    <center>No proposals were found</center>
                    @endif

                    <br>

                    <div class="col col-md-12">
                        <button type="submit" class="btn btn-lg btn-primary">Submit</button>
                    </div>

                </fieldset>

            {!! Form::close() !!}

        </div>
    </div>
</div>
@endsection
