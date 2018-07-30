@extends('layouts.admin')

@section('content')
<div class="container spark-screen">
    <div class="section row">
        <div class="col-md-10 col-md-offset-1">

            <style>
                a.col {
                    width: 29%;
                    margin: 2%;
                    height: 300px;
                    line-height: 30px;
                    text-align: center;
                    font-size: 24px;
                    border: 1px solid #ccc;
                    border-radius: 10px;
                    text-decoration: none;
                    box-sizing: border-box;
                    transition: all .1s linear;
                    box-shadow: 0 3px 3px #e0e0e0;
                }

                a.col i {
                    transition: all .1s linear;
                    font-size: 64px;
                    display: block;
                    margin-top: 100px;
                    margin-bottom: 10px;
                }

                a.col:hover i {
                    margin-top: 90px;
                    font-size: 72px;
                }

                a.col:hover {
                    font-size: 28px;
                }

            </style>

            <a href="{{ route('admin.shops.index') }}" class="col col-md-4">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                Shops
            </a>

            <a href="{{ route('admin.customers.index') }}" class="col col-md-4">
                <i class="fa fa-user" aria-hidden="true"></i>
                Customers
            </a>
            
            <a href="{{ route('admin.orders.index') }}" class="col col-md-4">
                <i class="fa fa-truck" aria-hidden="true"></i>
                Orders
            </a>

        </div>
    </div>
</div>
@endsection
