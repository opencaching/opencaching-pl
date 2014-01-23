<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Where Am I?</title>

    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>

    <script type="text/javascript">

var map = null;
var geocoder = null;
var marker = null;
var infowindow = null;

function initialize() {

    var latlng = new google.maps.LatLng(52, 19);
    var map_opts = {
        zoom: 5,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), map_opts);
    geocoder = new google.maps.Geocoder();
    marker = new google.maps.Marker({
        map: map,
        draggable: true
    });
    google.maps.event.addListener(marker, "dragend", showNewPosition);
    google.maps.event.addListener(marker, "click", showNewPosition);

    if (navigator.geolocation) {
        document.getElementById("pos_waiting").innerHTML = "Pobieranie położenia..."
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById("pos_waiting").innerHTML = "";
            initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
            moveToLocation(initialLocation);
        }, function() {
            document.getElementById("pos_waiting").innerHTML = "<br/>Nie można określić położenia";
        }, { timeout:10000 });
    }

}

function toWGS84(lat,lng) {

    var lat = lat, lng = lng;
    var latD = 'N', lngD = 'E';

    if(lat < 0) {
        lat = -lat;
        latD = 'S';
    }
    if(lng < 0) {
        lng = -lng;
        lngD = 'W';
    }

    var latstr, lngstr;

    var degs1 = lat | 0;
    var degs2 = lng | 0;
    var minutes1 = ((lat - degs1)*60);
    var minutes2 = ((lng - degs2)*60);
    latstr = degs1 + "° " + minutes1.toFixed(3) + "'";
    lngstr = degs2 + "° " + minutes2.toFixed(3) + "'";

    return "<br><b>" + latD + " " + latstr + " " + lngD + " " + lngstr + "</b>";
}

function moveToLocation(loc) {
    map.setCenter(loc);
    map.setZoom(15);
    marker.setPosition(loc);

    google.maps.event.trigger(marker, "dragend");
}

function showNewPosition() {
    var lat = marker.getPosition().lat().toFixed(5);
    var lng = marker.getPosition().lng().toFixed(5);

    window.opener.insertLocation(lat, lng);

    if (infowindow == null) {
        infowindow = new google.maps.InfoWindow();
    }
    infowindow.setContent(toWGS84(lat, lng));
    infowindow.open(map, marker);
}

function showAddress(address) {

    geocoder.geocode(
        { 'address': address },
        function(results, status) {
            if (status != google.maps.GeocoderStatus.OK) {
                alert(address + " not found");
            } else {
                moveToLocation(results[0].geometry.location);
            }
        }
    );

}
    </script>

  </head>

  <body onload="initialize()">
    <form action="#" onsubmit="showAddress(this.address.value); return false">
      <br>
        Podaj adres lub nazwę miejscowości, kraj aby otrzymać współrzędne.
        <br/>
        Nowe współrzędne będą pokazywać się po każdym przesunięciu myszką markera na mapie.
     <br />
        <input type="text" style="width:350px" name="address" value="Toruń Polska" />

        <input type="submit" value="Go!" /><span id="pos_waiting"></span>
       <br>
      <div id="map_canvas" style="width: 600px; height: 400px"></div>
    </form>

  </body>
</html>
