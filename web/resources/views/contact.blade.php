@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div id="contact-us" class="section">
        <div class="section-body">
            <div class="row">
            	<div class="col col-md-6 col-md-offset-3">
            		<h3 class="center">צור קשר</h3>
            	</div>
            	<hr>
            	<div class="col col-md-6 col-md-offset-3">
            		<div class="row">
	            		<div class="col col-md-8 col-md-offset-2">
	            			<h4 class="center">מלאו פרטים ליצירת קשר:</h4>
	            		</div>
            		</div>
                    @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                        <p class="alert alert-{{ $msg }}" style="margin-top: 0; font-size: 14px;">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                        @endif
                    @endforeach
                    </div>
            		<form method="POST" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="input-name" class="col col-lg-2 control-label">שם:</label>
                            <div class="col col-lg-8">
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" id="input-name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-email" class="col col-lg-2 control-label">דוא״ל:</label>
                            <div class="col col-lg-8">
                                <input type="text" class="form-control" name="email" value="{{ old('email') }}" id="input-email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-phone" class="col col-lg-2 control-label">טלפון:</label>
                            <div class="col col-lg-8">
                                <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" id="input-phone">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-content" class="col col-lg-2 control-label">תוכן הפניה:</label>
                            <div class="col col-lg-8">
                                <textarea type="text" class="form-control" name="content" value="{{ old('content') }}" id="input-content"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col col-lg-8 col-lg-offset-2">
                                <button type="submit" class="btn btn-warning">שלח הודעה</button>
                            </div>
                        </div>
                    </form>
            	</div>
            	<hr>
            	<div class="col col-md-6 col-md-offset-3">
            		<div class="row">
	            		<div class="col col-md-10 col-md-offset-2">
	            			<h4>או התקשרו 050-7422797</h4>
	            		</div>
            		</div>
            	</div>
            	<hr>
            	<div class="col col-md-6 col-md-offset-3">
            		<div class="row">
	            		<div class="col col-md-10 col-md-offset-2">
	            			<h4>9548315‎ ירושלים (א.ס.סיגל) 12 בית הדפוס</h4>
	            		</div>
            		</div>
            	</div>
            	<div class="row no-margin">
            		<div class="col-md-12">
            			<div id="contact-map"></div>
            		</div>
            	</div>
            </div>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js"></script>
<script>

  function initialize() {

    var mapCanvas = document.getElementById('contact-map');
    var mapOptions = {
      center: new google.maps.LatLng(31.786064, 35.188533),
      zoom: 15,
      minZoom: 6,
      scrollwheel: false,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(mapCanvas, mapOptions);

    new google.maps.Marker({
      map: map,
      position: new google.maps.LatLng(31.786064,35.188533),
      icon: '../img/location-icon-blue.png'
    });
    
  }

  google.maps.event.addDomListener(window, 'load', initialize);

</script>

@endsection
