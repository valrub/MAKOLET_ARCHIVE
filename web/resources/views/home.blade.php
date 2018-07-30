@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div class="carousel">
        <div class="carousel-inner">
            <div class="col col-md-8 col-sm-12 col-left">
                <h1>קניות ב&quot;מַכֹּלֶת‎&quot;</h1>
                <h3>זה מוכר וזה קל</h3>
                <a href="https://itunes.apple.com/bg/app/makolet/id1100575810" target="_blank" class="carousel-app">
                    <img src="{{ url('/img/carousel-app-store.png') }}">
                </a>
                <a href="https://play.google.com/store/apps/details?id=com.makolet.app" target="_blank" class="carousel-app">
                    <img src="{{ url('/img/carousel-google-play.png') }}">
                </a>
            </div>
            <div class="col col-md-4 col-sm-12 col-right">
                @if (Auth::guest())
                <a href="{{ url('/join') }}" class="btn btn-warning btn-lg">
                    {{ trans('lang.bussines_owner_join') }}
                </a>
                <a href="{{ url('/login') }}" class="btn btn-transparent btn-lg">
                    {{ trans('lang.login') }}
                </a>
                @endif
            </div>
        </div>
    </div>
    <div id="how-it-works" class="section">
        <div class="section-header">{{ trans('lang.how_it_works') }}</div>
        <div class="section-body">
            <div class="row how-steps">
                <div class="col col-md-3 col-sm-6 col-xs-12">
                    <img src="{{ url('/img/how-it-works-1.png') }}">
                    <span class="badge">1</span>
                    <span class="badge-description">פותחים חשבון ועושים רשימת קניות</span>
                </div>
                <div class="col col-md-3 col-sm-6 col-xs-12">
                    <img src="{{ url('/img/how-it-works-2.png') }}">
                    <span class="badge">2</span>
                    <span class="badge-description">מסמנים מכולת <b>אחת או יותר</b> מתוך המפה</span>
                </div>
                <div class="col col-md-3 col-sm-6 col-xs-12">
                    <img src="{{ url('/img/how-it-works-3.png') }}">
                    <span class="badge">3</span>
                    <span class="badge-description">מקבלים הערכות זמן למשלוח</span>
                </div>
                <div class="col col-md-3 col-sm-6 col-xs-12">
                    <img src="{{ url('/img/how-it-works-4.png') }}">
                    <span class="badge">4</span>
                    <span class="badge-description">מזמינים ומשלמים במרוכז <b>רק בסוף החודש</b>!</span>
                </div>
            </div>
            <div class="row how-description">
                <div class="col col-md-6 col-md-offset-3">
                    <h3 class="center">המשלוח בדרך אליכם!</h3>
                </div>
            </div>
            <div class="row how-description">
                <div class="col col-md-6 col-md-offset-3">
                    <h3 class="no-margin center" style="padding: 0 36px; font-size: 36px;">מכולת, לקנות ממי שאנחנו מכירים - וממי שמכיר אותנו</h3>
                    <p>יותר ויותר לקוחות עוזבים היום את הרשתות הגדולות, וחוזרים לעשות את הקניות בחנות השכונתית, זו שהם מכירים - וזו שמכירה אותם. אלא שהיום אפשר את זה ישירות מהסמארטפון שלך! </p>
                    <p>"מכולת" היא אפליקציה חדשה המאפשרת לעשות קניות בחנויות השכונתיות המוכרות והאהובות בשיא הפשטות והקלות. </p>
                    <p>ככה עושים היום קניות</p>                    
                    <p class="no-padding center"><b>למה אתם מחכים?</b></p>                 
                 </div>
            </div>
        </div>
    </div>
    <div id="stores" class="section">
        <div class="section-header">{{ trans('lang.stores') }}</div>
        <div class="section-body">
            <div id="home-map"></div>
        </div>
    </div>
    <div id="benefits" class="section">
        <div class="section-header">{{ trans('lang.benefits_for_groceries') }}</div>
        <div class="section-body">
            <div class="row">
                <div class="col col-md-2 col-sm-6 col-xs-12 col-md-offset-2">
                    <div class="benefit-icon benefit-chair"></div>
                    <div class="benefit-description">אחריות מלאה לכל עסקה</div>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12 col-md-offset-1">
                    <div class="benefit-icon benefit-clock"></div>
                    <div class="benefit-description">מבחר של מכולות מקומיות</div>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12 col-md-offset-1">
                    <div class="benefit-icon benefit-bag"></div>
                    <div class="benefit-description">אפשרות לאיסוף בדרך</div>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12 col-md-offset-2">
                    <div class="benefit-icon benefit-phone"></div>
                    <div class="benefit-description">הכל בלחיצת כפתור מהבית</div>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12 col-md-offset-1">
                    <div class="benefit-icon benefit-stats"></div>
                    <div class="benefit-description">ללא עלות</div>
                </div>
                <div class="col col-md-2 col-sm-6 col-xs-12 col-md-offset-1">
                    <div class="benefit-icon benefit-money"></div>
                    <div class="benefit-description">ללא התחייבות‎</div>
                </div>
            </div>
        </div>
    </div>
    <div id="questions" class="section">
        <div class="section-header">{{ trans('lang.questions_and_answers') }}</div>
        <div class="section-body">
            <div class="row">
                <div class="col col-md-6 col-md-offset-3">
                    <h3>הרשמה</h3>
                    <div class="accordion">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" href="#question-one">
                                    <span class="accordion-arrow"></span>&nbsp;&nbsp;                                                                                                                                                              איך מתחילים?
                                    
                                </a>
                            </div>
                            <div id="question-one" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    מורידים את אפליקציית "מכלת" מחנות האפליקציות המתאימה (<a href="https://play.google.com/store/apps/details?id=com.makolet.app" target="_blank">Google Play</a> או <a href="https://itunes.apple.com/bg/app/makolet/id1100575810" target="_blank">AppStore</a>), עוברים הרשמה ראשונית וקצרה הכוללת: כתובת למשלוח (ניתן לשנות בהתאם למיקום הרצוי) ובדיקה במפה אילו עסקים יש בסביבה.<br>את פרטי כרטיס האשראי ניתן להכניס הן בעת ההרשמה והן בעת ביצוע עסקת הקנייה הראשונה. עכשיו אפשר להתחיל להזמין!
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" href="#question-two">
                                    <span class="accordion-arrow"></span>&nbsp;&nbsp;
                                     שכחתי את הסיסמה שלי, מה עושים?
                                </a>
                            </div>
                            <div id="question-two" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    במסך הכניסה למערכת באתר, ישנה אפשרות להכניס כתובת דוא"ל ולקבל את הסיסמה ישירות למייל.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" href="#question-three">
                                    <span class="accordion-arrow"></span>&nbsp;&nbsp;
                                    אני בעל עסק וצריך עזרה, מה לעשות?
                                </a>
                            </div>
                            <div id="question-three" class="accordion-body collapse">
                                <div class="accordion-inner"> בכל נושא ניתן לפנות אלינו בטלפון 050-7422797 בין השעות 09:00-17:00.<br>כל נושא יטופל במהירות האפשרית.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col col-md-6 col-md-offset-3">
                    <h3>הזמנה</h3>
                    <div class="accordion">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" href="#question-four">
                                    <span class="accordion-arrow"></span>&nbsp;&nbsp;
                                     אחרי שנרשמתי, איך אני מתחיל להזמין?
                                </a>
                            </div>
                            <div id="question-four" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    פשוט מאוד-
מקלידים מוצרים באופן חופשי, מסמנים כמות על כל מוצר ובסוף שולחים את ההזמנה לעסקים שאתם בוחרים, בתוספת הערות אישיות וטקסט חופשי. 
כעת מחכים לתגובה של בתי העסק עם הערכת זמן למשלוח.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" href="#question-five">
                                    <span class="accordion-arrow"></span>&nbsp;&nbsp;
                                    למה אני לא רואה מכולות באזור שלי?
                                </a>
                            </div>
                            <div id="question-five" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    גם זה יכול לקרות. אבל-
אנו מעניקים 100 ₪ במתנה לסל הקניות של כל לקוח שיביא עימו בעל מכולת להרשמה במערכת שלנו. לפרטים נוספים על המבצע <a href="http://localhost:8000/contact-us" target="_blank">לחצו כאן</a>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" href="#question-six">
                                    <span class="accordion-arrow"></span>&nbsp;&nbsp;
                                   איך אפשר לעדכן את ההזמנה אחרי שנשלחה?
                                </a>
                            </div>
                            <div id="question-six" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    לאחר אישור ביצוע ההזמנה מבעל המכולת, לא ניתן לשנות את ההזמנה, להוסיף ו/או להוריד מצרכים.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" href="#question-six-two">
                                    <span class="accordion-arrow"></span>&nbsp;&nbsp;                                                                                                                                                                                                                                                                 ?איך מבטלים הזמנה שנשלחה
                                </a>
                            </div>
                            <div id="question-six-two" class="accordion-body collapse">
                                <div class="accordion-inner">קיימת אפשרות למחוק הזמנה.<br>במידה וישנה בעיה עם המצרכים, אם ישנו משלוח אשר אינו תואם את ההזמנה או במידה והמוצרים הגיעו פגומים-ניתן לסרב לקבל את המשלוח מבעל המכולת ולפנות אלינו עם מספר הזמנה לבירור (באמצעות האתר כאן או דרך סימן השאלה באפליקצייה) ואנו נעשה כל שביכולתנו על-מנת לפתור את הבעיה.
                                </div>
                            </div>
                       </div>

                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col col-md-6 col-md-offset-3">
                    <h3>משלוח ואסיפת מוצרים</h3>
                    <div class="accordion">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" href="#question-seven">
                                    <span class="accordion-arrow"></span>&nbsp;&nbsp;
                                    מה לעשות אם המשלוח שלי מאחר?
                                </a>
                            </div>
                            <div id="question-seven" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    על כל עסק ישנו דירוג גולשים וניתן (ורצוי!) לכתוב תגובה על בית העסק לאחר קבלת המשלוח. בעלי העסקים מודעים לכך ולכן יש להם מוטיבציה לספק את השירות הטוב ביותר.<br>במקרים חריגים, יופסק הקשר עם עסקים שאינם מספקים שירות לשביעות רצונם של הגולשים.<br>במידה והמשלוח מאחר או לא מגיע כלל, יש לפנות אלינו דרך האתר (<a href="https://makolet.biz/contact-us" target="_blank">לחץ כאן</a>) עם מספר הזמנה ואנו נטפל בעניין במהירות האפשרית. לא יחויב כרטיס אשראי על משלוח שלא התבצע או הגיע פגום.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" href="#question-eigth">
                                    <span class="accordion-arrow"></span>&nbsp;&nbsp;
                                     לא קיבלתי את כל המוצרים שהזמנתי. מה לעשות?
                                </a>
                            </div>
                            <div id="question-eigth" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    לפני הכל- יש לפנות ישירות לבעל מכולת בטלפון שצוין. במקרה בו הנושא לא טופל לשביעות רצונכם, יש לפנות אלינו (ללחוץ על סימן השאלה באפליקציה או דרך "צור קשר" באתר), לכתוב מספר הזמנה ולציין את הבעיה- והנושא יועבר לטיפול מיידי שלנו. 
במידה ולא תיפתר הבעיה או לא יגיע המשלוח המלא, לא יחויב כרטיס האשראי.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" href="#question-nine">
                                    <span class="accordion-arrow"></span>&nbsp;&nbsp;
                                     איך מזמינים משלוח לכתובת אחרת?
                                </a>
                            </div>
                            <div id="question-nine" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    במפה ישנה אפשרות לשנות כתובת ולראות האם יש עסקים מתאימים בסביבה הרצויה. במידה ויש, ניתן ללחוץ על כפתור רשימת המוצרים ולהתחיל את התהליך כרגיל.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col col-md-6 col-md-offset-3">
                    <h3>תשלום</h3>
                    <div class="accordion">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" href="#question-ten">
                                    <span class="accordion-arrow"></span>&nbsp;&nbsp;
                                   איך ומתי משלמים?
                                </a>
                            </div>
                            <div id="question-ten" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    התשלום מתבצע עבור כל הרכישות רק בסוף החודש בתשלום מרוכז, באמצעות כרטיס אשראי שהוקלד למערכת. את פרטי כרטיס האשראי ניתן לשנות בהתאם לצורך.

                                </div>
                            </div>
                        </div>
                                              
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js"></script>
<script src="{{ url('/js/markerclusterer.js') }}"></script>
<script>

    function initialize() {

        var mapCanvas = document.getElementById('home-map');

        var mapOptions = {
            center: new google.maps.LatLng(32.077360, 34.811652),
            zoom: 14,
            minZoom: 3,
            scrollwheel: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        var map = new google.maps.Map(mapCanvas, mapOptions);

        var markers = [];
        @foreach ($shops as $shop)
        var shop{{ $shop->id }} = new google.maps.Marker({
            map: map,
            position: new google.maps.LatLng({{ $shop->latitude }}, {{ $shop->longitude }}),
            icon: @if ($shop->type == 2) '../img/donkey-icon.png' @else '../img/cart-icon-red.png' @endif,
            title: "{{ $shop->name }}"
        });
        markers.push(shop{{ $shop->id }});
        @endforeach

        var bounds = new google.maps.LatLngBounds();
        for (var i = 0; i < markers.length; i++) {
            bounds.extend(markers[i].getPosition());
        }

        map.fitBounds(bounds);

        var options = {
            imagePath: "{{ url('/img/m') }}"
        };
        var markerCluster = new MarkerClusterer(map, markers, options);

    }

    google.maps.event.addDomListener(window, 'load', initialize);

</script>

@endsection
