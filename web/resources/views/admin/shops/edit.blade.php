@extends('layouts.admin')

@section('content')
<div class="container spark-screen">
    <div id="profile-form" class="section row">
        <div class="col-md-10 col-md-offset-1">
            
            <h3>
                <a href="{{ route('admin.shops.index') }}" class="btn btn-default btn-sm pull-left">Back <i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                איזור אישי
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

            {!! Form::model($shop, ['class' => 'form-horizontal col-md-8 col-md-offset-2', 'method' => 'PATCH', 'action' => ['Admin\ShopController@update', $shop]]) !!}

                <fieldset>
                    
                    <legend><b>פרטי העסק:</b></legend>

                    <div class="col col-md-5">
                        <label for="">{{ trans('lang.first_name') }}</label>
                        <input type="text" name="first_name" value="{{ $shop->first_name }}" />
                    </div>

                    <div class="col col-md-7">
                        <label for="">שם&nbsp;משפחה</label>
                        <input type="text" name="last_name" value="{{ $shop->last_name }}" />
                    </div>

                    <div class="col col-md-12">
                        <label for="">שם&nbsp;העסק</label>
                        <input type="text" name="company_name" value="{{ $shop->company_name }}" />
                    </div>

                    <div class="col col-md-12">
                        <label for="">ח.פ&nbsp;או&nbsp;מספר&nbsp;עסק&nbsp;מורשה</label>
                        <input type="text" name="company_id" value="{{ $shop->company_id }}" />
                    </div>

                    <div class="col col-md-6">
                        <label for="">שם&nbsp;חנות</label>
                        <input type="text" name="name" value="{{ $shop->name }}" />
                    </div>

                    <div class="col col-md-6">
                        <label for="">Type</label>
                        <select name="type" class="full-width">
                            <option value="1" @if ($shop->type == 1) selected @endif>Shop</option>
                            <option value="2" @if ($shop->type == 2) selected @endif>Alte Zachen</option>
                        </select>
                    </div>

                    <div class="col col-md-12">
                        <label>{{ trans('lang.city') }}</label>
                        <input type="text" name="city" id="city-field" value="{{ $shop->city }}" />
                    </div>

                    <div class="col col-md-8">
                        <label>{{ trans('lang.street') }}</label>
                        <input type="text" name="street" id="street-field" value="{{ $shop->street }}" />
                    </div>

                    <div class="col col-md-4">
                        <label>{{ trans('lang.building') }}</label>
                        <input type="text" name="building" id="building-field" value="{{ $shop->building }}" />
                    </div>

                    <div class="col col-md-12 hidden" id="map-container">
                        <div id="map-canvas" class="map-canvas" style="width: 100%; height: 250px;"></div>
                        <input id="latitude-field" class="hidden" type="text" name="latitude" value="{{ $shop->latitude }}" hidden>
                        <input id="longitude-field" class="hidden" type="text" name="longitude" value="{{ $shop->longitude }}" hidden>
                    </div>

                    <div class="col col-md-12">
                        <label>טלפון&nbsp;נייד</label>
                        <input type="text" name="phone" value="{{ $shop->phone }}" />
                    </div>

                    <div class="col col-md-12">
                        <label>טלפון&nbsp;בעסק</label>
                        <input type="text" name="mobile" value="{{ $shop->mobile }}" />
                    </div>

                    <div class="col col-md-12">
                        <label for="">{{ trans('lang.email') }}</label>
                        <input type="text" name="email" value="{{ $shop->user->email }}" readonly />
                    </div>

                    <div class="col col-md-7">
                        <label for="">{{ trans('lang.password') }}</label>
                        <input type="text" name="password" />
                    </div>

                    <div class="col col-md-5">
                        <a class="btn btn-default" style="display: block; padding-left: 10px; padding-right: 10px; border-width: 1px;" onclick="generatePassword()"><i class="fa fa-key" aria-hidden="true"></i> Generate new password</a>
                    </div>
                    
                    <legend><b>פרטי החשבון:</b></legend>

                    <div class="col col-md-12">
                        <label for="">מספר&nbsp;חשבון</label>
                        <input name="bank_account_number" type="text" value="{{ $shop->bank_account_number }}" />
                    </div>

                    <div class="col col-md-6">
                        <label for="">בנק</label>
                        <input name="bank_name" type="text" value="{{ $shop->bank_name }}" />
                    </div>

                    <div class="col col-md-6">
                        <label for="">סניף</label>
                        <input name="bank_branch" type="text" value="{{ $shop->bank_branch }}" />
                    </div>

                    <div class="col col-md-12">
                        <button type="submit" class="btn btn-lg btn-primary">עדכן פרטים</button>
                    </div>

                </fieldset>

            {!! Form::close() !!}

        </div>
    </div>

</div>
<script src="https://maps.googleapis.com/maps/api/js"></script>
<script>

    // Get the user location
    var userLatitude = "{{ $shop->latitude }}";
    var userLongitude = "{{ $shop->longitude }}";

    // Specify the user type
    var userType = 'shop';
    var shopType = {{ $shop->type }};

</script>
<script src="{{ url('js/map.js') }}"></script>
@endsection
