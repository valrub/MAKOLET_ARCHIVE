@extends('layouts.admin')

@section('content')
<div class="container spark-screen">
    <div id="profile-form" class="section row">
        <div class="col-md-10 col-md-offset-1">
            
            <h3>איזור אישי</h3>

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

            {!! Form::model($customer, ['class' => 'form-horizontal col-md-8 col-md-offset-2', 'method' => 'PATCH', 'action' => ['Admin\CustomerController@update', $customer]]) !!}

                <fieldset>
                    
                    <legend><b>פרטים אישיים</b>:</legend>

                    <div class="col col-md-5">
                        <label for="">{{ trans('lang.first_name') }}</label>
                        <input type="text" name="first_name" value="{{ $customer->first_name }}" />
                    </div>

                    <div class="col col-md-7">
                        <label for="">שם&nbsp;משפחה</label>
                        <input type="text" name="last_name" value="{{ $customer->last_name }}" />
                    </div>

                    <div class="col col-md-12">
                        <label for="">מספר&nbsp;טלפון</label>
                        <input type="text" name="phone" value="{{ $customer->phone }}" />
                    </div>

                    <div class="col col-md-12">
                        <label for="">{{ trans('lang.email') }}</label>
                        <input type="text" name="email" value="{{ $customer->user->email }}" readonly />
                    </div>

                    <div class="col col-md-7">
                        <label for="">{{ trans('lang.password') }}</label>
                        <input type="text" name="password" />
                    </div>

                    <div class="col col-md-5">
                        <a class="btn btn-default" style="display: block; padding-left: 10px; padding-right: 10px; border-width: 1px;" onclick="generatePassword()"><i class="fa fa-key" aria-hidden="true"></i> Generate new password</a>
                    </div>

                    <legend><b>{{ trans('lang.address') }}</b>:</legend>

                    <div class="col col-md-12">
                        <label>{{ trans('lang.city') }}</label>
                        <input type="text" name="city" id="city-field" value="{{ $customer->city }}" />
                    </div>

                    <div class="col col-md-12">
                        <label>{{ trans('lang.street') }}</label>
                        <input type="text" name="street" id="street-field" value="{{ $customer->street }}" />
                    </div>

                    <div class="col col-md-4">
                        <label>{{ trans('lang.building') }}</label>
                        <input type="text" name="building" id="building-field" value="{{ $customer->building }}" />
                    </div>

                    <div class="col col-md-4">
                        <label>{{ trans('lang.entrance') }}</label>
                        <input type="text" name="entrance" value="{{ $customer->entrance }}" />
                    </div>

                    <div class="col col-md-4">
                        <label>{{ trans('lang.apartment') }}</label>
                        <input type="text" name="apartment" value="{{ $customer->apartment }}" />
                    </div>

                    <div class="col col-md-12 hidden" id="map-container">
                        <div id="map-canvas" class="map-canvas" style="width: 100%; height: 250px;"></div>
                        <input id="latitude-field" class="hidden" type="text" name="latitude" value="{{ $customer->latitude }}" hidden>
                        <input id="longitude-field" class="hidden" type="text" name="longitude" value="{{ $customer->longitude }}" hidden>
                    </div>

                    <legend><b>פרטי כרטיס אשראי</b>:</legend>

                    <div class="col col-md-12">
                        <label>סוג&nbsp;הכרטיס</label>
                        <select class="full-width">
                            <option disabled selected>בחר סוג הכרטיס</option>
                        </select>
                    </div>

                    <div class="col col-md-12">
                        <label>מספר&nbsp;כרטיס</label>
                        <input type="text" name="" />
                    </div>

                    <div class="col col-md-12">
                        <label>תוקף</label>
                        <select class="half-width">
                            <option disabled selected>שנה</option>
                        </select>
                        <span class="spacer"></span>
                        <select class="half-width">
                            <option disabled selected>חודש</option>
                        </select>
                    </div>

                    <div class="col col-md-12">
                        <label>קוד&nbsp;הבטחה</label>
                        <input type="text" name="" class="half-width" />
                        <a class="btn-round btn-secondary">?</a>
                    </div>

                    <div class="col col-md-12">
                        <button type="submit" class="btn btn-lg btn-primary">עדכן פרטים אישיים</button>
                    </div>

                </fieldset>

            {!! Form::close() !!}

        </div>
    </div>

</div>
<script src="https://maps.googleapis.com/maps/api/js"></script>
<script>

    // Get the user location
    var userLatitude = "{{ $customer->latitude }}";
    var userLongitude = "{{ $customer->longitude }}";

    // Specify the user type
    var userType = 'customer';

</script>
<script src="{{ url('js/map.js') }}"></script>
@endsection
