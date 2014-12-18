{$pagename=$show_map}

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN"
    "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta name="description" content="Geocaching Opencaching Polska"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="Content-Language" content="pl" />
        <title>m.Opencaching.pl - {$pagename}</title>
        <meta name="HandheldFriendly" content="true" />
        <meta name="Viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
        <link rel="stylesheet" type="text/css" href="../lib/style.css" />


        <script type="text/javascript">

            function Deg2Rad(a) {
                return a * (Math.PI / 180);
            }

            function Rad2Deg(a) {
                return a * (180 / Math.PI);
            }

            var wid = 200;
            var zoom;
            var nav;
            var center;
            var lat;
            var lon;
            reset();
            var n, e;
            var base = (1024 / 3625);
            var move;
            var control_content = "<table class='tablefooter' width='75%'><tr><td class='button' style='width:50%' colspan='2'><a href='javascript:zoom--;showmap();' >-</a></td><td class='button' style='width:50%' colspan='2'><a href='javascript:zoom++;showmap();' >+</a></td></tr><tr><td class='button' style='width:25%'><a href='javascript:lm();'>&larr;</a></td><td class='button' style='width:25%'><a href='javascript:um();'>&uarr;</a></td><td class='button' style='width:25%'><a href='javascript:dm();'>&darr;</a></td><td class='button' style='width:25%'><a href='javascript:rm();'>&rarr;</a></td></tr></table>";

            {literal}
                function rm() {
                    lon = lon + move;
                    showmap();
                }
                function lm() {
                    lon = lon - move;
                    showmap();
                }
                function um() {
                    lat = lat + (move / 2);
                    showmap();
                }
                function dm() {
                    lat = lat - (move / 2);
                    showmap();
                }
            {/literal}

                function reset() {
                    lat ={$lat};
                    lon ={$lon};
                    zoom = 14;
                    nav = false;
                    center = false;
                }

                function navi() {
                    if (navigator.geolocation) {
                        center = false;
                        nav = true;
                        navigator.geolocation.getCurrentPosition(handle_geolocation_query);
                    }
                }

                function handle_geolocation_query(position) {
                    n = position.coords.latitude;
                    e = position.coords.longitude;
                    showmap();
                    document.getElementById("navig").innerHTML = "<br/><div class='button' ><a href='javascript:centerme();' >{$center_on_me}</a></div><div class='button' ><a href='javascript:reset();showmap();' >{$normal_view}</a></div>";
                }

                function centerme() {
                    center = true;
                    var zoom = 14;
                    lon = e;
                    lat = n;
                    showmap();
                    document.getElementById("control").innerHTML = control_content;
                    document.getElementById("navig").innerHTML = "<br/><div class='button' ><a href='javascript:navi();' >{$show_track}</a></div><div class='button' ><a href='javascript:reset();showmap();' >{$normal_view}</a></div>";
                }

                function round2(number, x) {
                    var x = (!x ? 2 : x);
                    return Math.round(number * Math.pow(10, x)) / Math.pow(10, x);
                }

                function showmap() {
                    if (zoom > 18)
                        zoom = 18;
                    if (zoom < 10)
                        zoom = 10;

                    move = (base / Math.pow(2, (zoom - 1))) * wid;

                    if (nav == false) {
                        if (navigator.geolocation)
                            document.getElementById("navig").innerHTML = "<br/><div class='button' ><a href='javascript:navi();' >{$show_from_me}</a></div>";
                        document.getElementById("map").src = "http://maps.googleapis.com/maps/api/staticmap?center=" + lat + "," + lon + "&zoom=" + zoom + "&size=" + wid + "x" + wid + "&maptype=roadmap&sensor=false&format=png&markers=color:red|{$lat},{$lon}";
                        document.getElementById("control").innerHTML = control_content;
                    } else {
                        var dist = round2(Math.acos((Math.sin({$lat}) * Math.sin(n)) + (Math.cos({$lat}) * Math.cos(n) * Math.cos(Math.abs({$lon} - e)))) * 111.19, 1);

                        var result = 0.0;

                        var ilat1 = (0.50 + n * 360000.0);
                        var ilat2 = (0.50 + {$lat} * 360000.0);
                        var ilon1 = (0.50 + e * 360000.0);
                        var ilon2 = (0.50 + {$lon} * 360000.0);

                        var lat1 = Deg2Rad(n);
                        var lon1 = Deg2Rad(e);
                        var lat2 = Deg2Rad({$lat});
                        var lon2 = Deg2Rad({$lon});

                        if ((ilat1 == ilat2) && (ilon1 == ilon2)) {
                        } else if (ilon1 == ilon2) {
                            if (ilat1 > ilat2)
                                result = 180.0;
                        } else {
                            var c = Math.acos(Math.sin(lat2) * Math.sin(lat1) + Math.cos(lat2) * Math.cos(lat1) * Math.cos((lon2 - lon1)));
                            var A = Math.asin(Math.cos(lat2) * Math.sin((lon2 - lon1)) / Math.sin(c));
                            result = Rad2Deg(A);

                            if ((ilat2 > ilat1) && (ilon2 > ilon1)) {
                            } else if ((ilat2 < ilat1) && (ilon2 < ilon1)) {
                                result = 180.0 - result;
                            } else if ((ilat2 < ilat1) && (ilon2 > ilon1)) {
                                result = 180.0 - result;
                            } else if ((ilat2 > ilat1) && (ilon2 < ilon1)) {
                                result = result + 360.0;
                            }
                        }
                        result = parseInt(result);

                        document.getElementById("info").innerHTML = result + "°, " + dist + " km<br/>";

                        if (center == true)
                            document.getElementById("map").src = "http://maps.googleapis.com/maps/api/staticmap?size=" + wid + "x" + wid + "&maptype=roadmap&sensor=false&center=" + lat + "," + lon + "&zoom=" + zoom + "&format=png&markers={$lat},{$lon}&markers=color:green|label:S|" + n + "," + e + "&path=color:0x0000ff|weight:5|" + n + "," + e + "|{$lat},{$lon}";
                        else {
                            document.getElementById("map").src = "http://maps.googleapis.com/maps/api/staticmap?size=" + wid + "x" + wid + "&maptype=roadmap&sensor=false&format=png&markers={$lat},{$lon}&markers=color:green|label:S|" + n + "," + e + "&path=color:0x0000ff|weight:5|" + n + "," + e + "|{$lat},{$lon}";
                            document.getElementById("control").innerHTML = "";
                        }
                    }
                }

                window.onload = function () {
                    wid = document.getElementById("content").offsetWidth - 20;
                    showmap();
                }

        </script>

    </head>

    <body style="max-width:600px; margin:auto; padding: 8px;">

        <div id="content">

            <b>{$name} ({$smarty.get.wp})</b><hr/>

            <img id="map" src=""/>

            <div id="info"></div>

            <div id="control"></div>

            <div id="navig"></div>

            <hr/>
            <div class='menu'>
                <div class='button'>
                    <a href='./viewcache.php?wp={$wp}'>{$back}</a>
                </div>
            </div>

        </div>

    </body>

</html>