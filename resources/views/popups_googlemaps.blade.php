<style>
    #map {
        height: 500px;
        border-color: red;
    }
</style>
<script>
    var map, infowindow, service, markers = new Array();
    var directionsService, directionsDisplay;

    $(window).load(function () {
        <?php
            startfile("popups_googlemaps");
            if(isset($latitude) && isset($longitude)){
                echo 'initMap(' . $latitude . ', ' . $longitude . ');';
                if(isset($name)){
                    echo 'addmarker("' . $name . '", ' . $latitude . ', ' . $longitude . ');';
                }
            }
        ?>
    });

    function calculateAndDisplayRoute(origin, destination) {
        directionsService.route({
            origin: origin,
            destination: destination,
            travelMode: 'DRIVING'
        }, function (response, status) {
            if (status === 'OK') {
                directionsDisplay.setDirections(response);
            } else {
                window.alert('Directions request failed due to ' + status);
            }
        });
    }

    function initMap(latitude, longitude) {
        directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer;
        if(isUndefined(latitude) || isUndefined(longitude)){log("FAILURE TO INIT MAPS"); return;}
        log("initMap: " + latitude + ", " + longitude);
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: latitude, lng: longitude},
            zoom: 15
        });
        directionsDisplay.setMap(map);
        infowindow = new google.maps.InfoWindow();
        service = new google.maps.places.PlacesService(map);
    }

    function addmarker(Name, Latitude, Longitude, Center){
        var myLatlng = new google.maps.LatLng(parseFloat(Latitude),parseFloat(Longitude));
        var marker = new google.maps.Marker({
            map: map,
            position: myLatlng
        });
        if(!isUndefined(Center)){
            if(Center){
                var latLng = marker.getPosition(); // returns LatLng object
                map.setCenter(latLng); // setCenter takes a LatLng object
            }
        }
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.setContent(Name);
            infowindow.open(map, this);
        });
    }

    function directions(CustName, CustLatitude, CustLongitude, RestName, RestLatitude, RestLongitude){
        addmarker(CustName, CustLatitude, CustLongitude);
        addmarker(RestName, RestLatitude, RestLongitude);
        calculateAndDisplayRoute(RestLatitude + ", " + RestLongitude, CustLatitude + ", " + CustLongitude);
    }

    function addmarker2(Name, Latitude, Longitude, Delimiter, Prepend, Append){
        if(isUndefined(Name)){
            for(var id = 0; id < markers.length; id++){
                addmarker(markers[id]["Prepend"] + markers[id]["Name"] + markers[id]["Append"], markers[id]["Latitude"], markers[id]["Longitude"]);
            }
        } else {
            if(isUndefined(Prepend)){Prepend="";}
            if(isUndefined(Append)){Append="";}
            if(isUndefined(Delimiter)){Delimiter="<BR>";}
            for(var id = 0; id < markers.length; id++){
                if(markers[id]["Latitude"] == Latitude && markers[id]["Longitude"] == Longitude){
                    markers[id]["Name"] += Delimiter + Name;
                    return id;
                }
            }
            markers.push({Name: Name, Latitude: Latitude, Longitude: Longitude, Prepend: Prepend, Append: Append});
        }
    }
</script>
<div id="map"></div>
@if(!isset($includeapi))
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWSUc8EbZYVKF37jWVCb3lpBQwWqXUZw8&signed_in=true&libraries=places&callback=initMap" async defer></script>
@endif
<?php endfile("popups_googlemaps"); ?>