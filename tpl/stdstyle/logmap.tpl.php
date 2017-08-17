<?php

?>
<script type="text/javascript">
var map0 = null;
var currentinfowindow = null;

function addMarker(lat, lon, icon, cache_icon, wp, cache_name, log_id, log_icon, user_id, user_name, log_date) {
    var marker = new google.maps.Marker({position: new google.maps.LatLng(lat, lon), icon: icon, map: map0});
    var infowindow = new google.maps.InfoWindow({
        content: "<table><tr><td><img src=\"tpl/stdstyle/images/" + cache_icon + "\" alt=\"\"> <a class=\"links\" href=\"viewcache.php?wp=" + wp + "\">" + wp + ": " + cache_name + "</a></td></tr><tr><td><a class=\"links\" href=\"viewlogs.php?logid=" + log_id + "\"><img src=\"tpl/stdstyle/images/" + log_icon + "\" alt=\"\"> " + log_date + "</a> {{logmap_01}} <a class=\"links\" href=\"viewprofile.php?userid=" + user_id + "\">" + user_name + "</a></td></tr></table>"
    });
    google.maps.event.addListener(marker, "click", function() {
        if (currentinfowindow !== null) {
            currentinfowindow.close();
        }
        infowindow.open (map0, marker);
        currentinfowindow = infowindow;
    });
}

function initialize() {

    var icon1 = { url: "tpl/stdstyle/images/google_maps/green.png" };
    var icon2 = { url: "tpl/stdstyle/images/google_maps/red.png" };
    var icon3 = { url: "tpl/stdstyle/images/google_maps/yellow.png" };
    var icon4 = { url: "tpl/stdstyle/images/google_maps/yellow.png" };
    var icon5 = { url: "tpl/stdstyle/images/google_maps/yellow.png" };

    map0 = new google.maps.Map(
        document.getElementById("map0"),
        {
            center: new google.maps.LatLng({mapcenterLat},{mapcenterLon}),
            zoom: {mapzoom},
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
    );

{points}
}

window.onload = function() {
    initialize();
};
</script>

<div class="content2-container">
  <div class="content2-pagetitle">
    <img src="tpl/stdstyle/images/blue/world.png" class="icon32" alt="">
    {{logmap_03}}
  </div>
  <div class="buffer"></div>
  <div id="map0" style="height: 600px"></div>
</div>
