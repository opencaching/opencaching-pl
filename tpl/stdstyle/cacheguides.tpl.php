<?php
/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *  UTF-8 ąść
 * ************************************************************************* */
?>
<script type="text/javascript">
    var map0 = null;
    var currentinfowindow = null;
    var icon1 = {url: "tpl/stdstyle/images/google_maps/guru.png"};

    function addMarker(lat, lon, userid, username, nrec) {
        var marker = new google.maps.Marker({position: new google.maps.LatLng(lat, lon), icon: icon1, map: map0});
        var infowindow = new google.maps.InfoWindow({
            content: '<span style="color:blue;"><table><tr><td><img src="tpl/stdstyle/images/free_icons/vcard.png" alt="img"><b>&nbsp;<a class="links" href="viewprofile.php?userid=' + userid + '">' + username + '</a></td></tr><tr><td><b><img src="images/rating-star.png" alt="rekomendacje" title="rekomendacje"><b>&nbsp;' + nrec + ' {{guru_15}}</td></tr><tr><td><img src="tpl/stdstyle/images/free_icons/email.png" alt="img"><b>&nbsp;<a class="links" href="mailto.php?userid=' + userid + '">{{guru_16}}</a></b></td></tr></table></span>'
        });
        google.maps.event.addListener(marker, "click", function () {
            if (currentinfowindow !== null) {
                currentinfowindow.close();
            }
            infowindow.open(map0, marker);
            currentinfowindow = infowindow;
        });
    }

    function initialize() {

        map0 = new google.maps.Map(
                document.getElementById("map0"),
        {
        center: new google.maps.LatLng({mapcenterLat}, {mapcenterLon}),
                zoom: {mapzoom}
        ,
                mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        );
        {points}

    }

    window.onload = function () {
        initialize();
    };
</script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/guru.png" class="icon32" alt="" />&nbsp;{{cacheguides}}</div>
<div class="searchdiv">
    <span style="font-size: 13px;">
        {{guru_01}}
        <br/><br/>
        {{guru_02}}
        <ul>
            <li> {{guru_03}}</li>
            <li> {{guru_04}}</li>
            <li> {{guru_05}}</li>
        </ul></br>
        {{guru_06}} <br/><br/>
        {{guru_07}} <b><font color="blue">{nguides}</font></b> {{guru_08}} <br/>
        <span>
            </div>
            <div class="searchdiv">
                <center>
                    <div id="map0" style="width:100%; height:500px"></div>
                </center>
                <br/>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/rating-star.png" alt="rekomendacje" title="rekomendacje"><b>&nbsp{{guru_09}}</b><br/>
            </div>
            <div class="searchdiv">
                <span class="content-title-noshade" style="width: 600px;margin: 10px;line-height: 1.6em;font-size: 12px;">{{guru_10}}
                    <ul><font color="black">
                        <li>{{guru_11}}</li>
                        </font></ul>
                    &nbsp;&nbsp;&nbsp;{{guru_12}} <a class="links" href="{serverURL}myprofile.php?action=change">{{guru_13}}</a>.
                    <br/><br/>
                    &nbsp;&nbsp;&nbsp;{{guru_14}}
                </span></div>
            <br/>


