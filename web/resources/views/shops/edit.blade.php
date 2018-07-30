@extends('layouts.app')

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

            {!! Form::model($shop, ['class' => 'form-horizontal col-md-8 col-md-offset-2', 'method' => 'PATCH', 'action' => ['ShopController@update', $shop]]) !!}

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

                    <div class="col col-md-12">
                        <label for="">שם&nbsp;חנות</label>
                        <input type="text" name="name" value="{{ $shop->name }}" />
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

                    <div class="col col-md-12">
                        <label for="">{{ trans('lang.password') }}</label>
                        <input type="password" name="password" />
                    </div>

                    <div class="col col-md-12">
                        <button type="submit" class="btn btn-lg btn-primary">עדכן פרטים</button>
                    </div>

                </fieldset>

                <fieldset>
                    
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
                        <button type="submit" class="btn btn-lg btn-primary">עדכון פרטי חשבון</button>
                    </div>

                </fieldset>

                <fieldset>
                    
                    <legend><b>WORKING TIME:</b></legend>

                    <div class="row weekday">
                        <div class="col col-md-3 col-sm-6">
                            <label>
                                <input type="checkbox" name="workingDays[7]" value="workingDay[7]" id="working-day-7" checked />
                                <label for="working-day-7"></label>
                                יום ראשון
                            </label>
                        </div>
                        <div class="col col-md-1 col-sm-6 slider-close">17:00</div>
                        <div class="col col-md-7 col-sm-12">
                            <div class="slider-range"></div>
                        </div>
                        <div class="col col-md-1 col-sm-6 slider-open">09:00</div>
                        <input type="hidden" class="input-open" name="open[7]" value="09:00">
                        <input type="hidden" class="input-close" name="close[7]" value="17:00">
                    </div>

                    <div class="row weekday">
                        <div class="col col-md-3 col-sm-6">
                            <label>
                                <input type="checkbox" name="workingDays[1]" value="workingDay[1]" id="working-day-1" checked />
                                <label for="working-day-1"></label>
                                יום שני
                            </label>
                        </div>
                        <div class="col col-md-1 col-sm-6 slider-close">17:00</div>
                        <div class="col col-md-7 col-sm-12">
                            <div class="slider-range"></div>
                        </div>
                        <div class="col col-md-1 col-sm-6 slider-open">09:00</div>
                        <input type="hidden" class="input-open" name="open[1]" value="09:00">
                        <input type="hidden" class="input-close" name="close[1]" value="17:00">
                    </div>

                    <div class="row weekday">
                        <div class="col col-md-3 col-sm-6">
                            <label>
                                יום שלישי
                                <input type="checkbox" name="workingDays[2]" value="workingDay[2]" id="working-day-2" checked />
                                <label for="working-day-2"></label>
                            </label>
                        </div>
                        <div class="col col-md-1 col-sm-6 slider-close">17:00</div>
                        <div class="col col-md-7 col-sm-12">
                            <div class="slider-range"></div>
                        </div>
                        <div class="col col-md-1 col-sm-6 slider-open">09:00</div>
                        <input type="hidden" class="input-open" name="open[2]" value="09:00">
                        <input type="hidden" class="input-close" name="close[2]" value="17:00">
                    </div>

                    <div class="row weekday">
                        <div class="col col-md-3 col-sm-6">
                            <label>
                                יום רביעי
                                <input type="checkbox" name="workingDays[3]" value="workingDay[3]" id="working-day-3" checked />
                                <label for="working-day-3"></label>
                            </label>
                        </div>
                        <div class="col col-md-1 col-sm-6 slider-close">17:00</div>
                        <div class="col col-md-7 col-sm-12">
                            <div class="slider-range"></div>
                        </div>
                        <div class="col col-md-1 col-sm-6 slider-open">09:00</div>
                        <input type="hidden" class="input-open" name="open[3]" value="09:00">
                        <input type="hidden" class="input-close" name="close[3]" value="17:00">
                    </div>

                    <div class="row weekday">
                        <div class="col col-md-3 col-sm-6">
                            <label>
                                יום חמישי
                                <input type="checkbox" name="workingDays[4]" value="workingDay[4]" id="working-day-4" checked />
                                <label for="working-day-4"></label>
                            </label>
                        </div>
                        <div class="col col-md-1 col-sm-6 slider-close">17:00</div>
                        <div class="col col-md-7 col-sm-12">
                            <div class="slider-range"></div>
                        </div>
                        <div class="col col-md-1 col-sm-6 slider-open">09:00</div>
                        <input type="hidden" class="input-open" name="open[4]" value="09:00">
                        <input type="hidden" class="input-close" name="close[4]" value="17:00">
                    </div>

                    <div class="col col-md-12">
                        <button type="submit" class="btn btn-lg btn-primary">עדכון פרטי חשבון</button>
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

</script>
<script src="{{ url('js/map.js') }}"></script>
@endsection
