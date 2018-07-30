@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div id="register-business" class="section">
        <div class="section-body">
            <div class="row">
                <div class="col col-md-7 col-left">
                    <h1>בעל עסק?</h1>
                    <h2>הצטרף אלינו ותרוויח</h2>
                    <h3>בעל עסק,</h3>
                    <ul>
                        <li><span>הרשתות הגדולות לוקחות לך את הפרנסה? </span></li>
                        <li><span>הסיפור הזה של הקניות באינטרנט גדול עליך?</span></li>
                        <li><span>רוצה לחזור ולהרוויח כמו שהרווחת פעם?</span></li>
                    </ul>
                    <p>הצטרף עכשיו ל"מכולת", אפליקציית הקניות המחברת אותך לאלפי לקוחות פוטנציאליים באופן הכי בטוח שיש – והכל בלחיצת כפתור!</p>
                    <h3>עשרות עסקים הצטרפו אלינו וקיבלו</h3>
                    <ul>
                        <li><span>גישה לאלפי לקוחות חדשים</span></li>
                        <li><span>אחריות מלאה על כל עסקה</span></li>
                        <li><span>תשלום מרוכז בסוף כל חודש</span></li>
                    </ul>
                    <p><b>למה אתה מחכה, הצטרף עכשיו ותחזור להרוויח כמו פעם!</b></p>
                  </div>
                <div class="col col-md-5 col-right">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">מלאו טופס בקשה להצטרפות</h3>
                        </div>
                        <div class="panel-body">
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
                                    <label for="input-first-name" class="col col-lg-4 control-label">שם:</label>
                                    <div class="col col-lg-8">
                                        <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" id="input-first-name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="input-last-name" class="col col-lg-4 control-label">שם משפחה:</label>
                                    <div class="col col-lg-8">
                                        <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" id="input-last-name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="input-phone" class="col col-lg-4 control-label">טלפון:</label>
                                    <div class="col col-lg-8">
                                        <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" id="input-phone">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="input-email" class="col col-lg-4 control-label">דוא״ל:</label>
                                    <div class="col col-lg-8">
                                        <input type="text" class="form-control" name="email" value="{{ old('email') }}" id="input-email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="input-city" class="col col-lg-4 control-label">עיר:</label>
                                    <div class="col col-lg-8">
                                        <input type="text" class="form-control" name="city" value="{{ old('city') }}" id="input-city">
                                    </div>
                                </div>
                                <div class="form-group no-margin">
                                    <div class="col col-lg-8 col-lg-offset-4">
                                        <button type="submit" class="btn btn-warning">צרפו אותי למכולת</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
