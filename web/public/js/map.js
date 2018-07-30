// Initialize the map variables
var geocoder;
var map;
var customer;

// Select the map container
var mapCanvas = document.getElementById('map-canvas');
var mapContainer = document.getElementById('map-container');
var latitudeField = document.getElementById('latitude-field');
var longitudeField = document.getElementById('longitude-field');

function initialize() {

    // Initialize the geocoder
    geocoder = new google.maps.Geocoder();

    // Map options
    var mapOptions = {
        center: new google.maps.LatLng(32.085302, 34.781726),
        zoom: 16,
        maxZoom: 18,
        minZoom: 12,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    // Create map object
    map = new google.maps.Map(mapCanvas, mapOptions);

    // Show the map
    mapContainer.className = mapContainer.className.replace("hidden", "");

    // Show the marker
    if (navigator.geolocation) {
        
        // Use the current position
        navigator.geolocation.getCurrentPosition(showPosition);
    } else if (userLatitude && userLongitude) {
        
        // Use the customer location
        showPosition();
    } else {
        console.log("Geolocation is not supported by this browser.");
    }

}

function addressToCoords(address) {
    geocoder.geocode({'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            
            // Change the marker position
            customer.setPosition(results[0].geometry.location);
            map.setCenter(customer.getPosition());

            // Change the location fields
            updateFields();

        } else {
            console.log("Geocode was not successful for the following reason: " + status);
        }
    });
}

function updateFields() {
    latitudeField.setAttribute('value', parseFloat(customer.getPosition().lat()).toFixed(6));
    longitudeField.setAttribute('value', parseFloat(customer.getPosition().lng()).toFixed(6));
}

function showPosition(position) {

    // Prepare the main latitude and longitude
    var mainLatitude = userLatitude ? userLatitude : position.coords.latitude;
    var mainLongitude = userLongitude ? userLongitude : position.coords.longitude;

    // Customer marker
    customer = new google.maps.Marker({
        map: map,
        draggable: true,
        position: new google.maps.LatLng(mainLatitude, mainLongitude),
        icon: userType == 'shop' ? window.location.origin + '/img/cart-icon-red.png' : window.location.origin + '/img/location-icon-blue.png', // null = default icon
        title: "You are here",
        animation: google.maps.Animation.DROP
    });

    // Center the map once we have the customer's coordinates
    map.setCenter(customer.getPosition());
    updateFields();

    // Listen for change in the marker's location
    customer.addListener('mouseup', function() {
        updateFields();
    });

    // Listen for click on the store's marker
    customer.addListener('click', function() {
        var infowindow = new google.maps.InfoWindow({
            content: "You are not here?<br>Move the marker to select your place."
        });
        map.setCenter(customer.getPosition());
        infowindow.open(map, customer);
    });

    (function($){
        $.fn.extend({
            donetyping: function(callback,timeout) {
                timeout = timeout || 1e3; // 1 second default timeout
                var timeoutReference,
                    doneTyping = function(el) {
                        if (!timeoutReference) return;
                        timeoutReference = null;
                        callback.call(el);
                    };
                return this.each(function(i, el) {
                    var $el = $(el);
                    $el.is(':input') && $el.on('keyup keypress paste', function(e) {
                        if (e.type=='keyup' && e.keyCode!=8) return;
                        if (timeoutReference) clearTimeout(timeoutReference);
                        timeoutReference = setTimeout(function(){
                            doneTyping(el);
                        }, timeout);
                    }).on('blur',function(){
                        doneTyping(el);
                    });
                });
            }
        });
    })(jQuery);

    $('input[type=text]').donetyping(function() {
        var city = $('#city-field').val();
        var street = $('#street-field').val();
        var building = $('#building-field').val();
        var address = city + ", " + street + " " + building;
        addressToCoords(address);
    });

}

// Initialize the map
google.maps.event.addDomListener(window, 'load', initialize);