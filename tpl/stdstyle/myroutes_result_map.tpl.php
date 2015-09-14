<?php

?>
<?php
require_once('./lib/common.inc.php');
require_once('./lib/class.polylineEncoder.php');
$route_id = $_REQUEST['routeid'];

$rscp = sql("SELECT `lat` ,`lon`
                    FROM `route_points`
                    WHERE `route_id`='$route_id'");
$p = array();
$points = array();
for ($i = 0; $i < mysql_num_rows($rscp); $i++) {
    $record = sql_fetch_array($rscp);
    $y = $record['lon'];
    $x = $record['lat'];

    $p[0] = $x;
    $p[1] = $y;
    $points[$i] = $p;
}

$encoder = new PolylineEncoder();
$polyline = $encoder->encode($points);
?>
<script type="text/javascript" src="/lib/jsts/attache.array.min.js"></script>
<script type="text/javascript" src="/lib/jsts/javascript.util.js"></script>
<script type="text/javascript" src="/lib/jsts/jsts.0.13.2.js"></script>
<script type="text/javascript" src="/lib/js/myroutes_map.<?= date("YmdHis", filemtime($rootpath . 'lib/js/myroutes_map.js')) ?>.js"></script>
<script type="text/javascript">
//<![CDATA[

    var currentinfowindow = null;

    var icon3 = {url: "tpl/stdstyle/images/google_maps/gmgreen.gif"};

    function addMarker(lat, lon, icon, cacheid, cachename, wp, username, ratings) {
        var marker = new google.maps.Marker({position: new google.maps.LatLng(lat, lon), icon: icon3, map: map});
        var topratings = "";

        if (ratings > 0) {
            topratings = '<br/><img width="10" height="10" src="images/rating-star.png" alt="{{recommendation}}" />&nbsp;<b>{{search_recommendations}}: </b>';
            topratings += '<span style="font-weight: bold; color: green;">' + ratings + '</span>';
        }

        var infowindow = new google.maps.InfoWindow({
            content:
                    '<table border="0"><tr><td>' +
                    '<img src="tpl/stdstyle/images/' + icon + '" border="0" alt=""/>&nbsp;<a href="viewcache.php?cacheid=' + cacheid + '" target="_blank">' + cachename + '</a>' +
                    ' - <b>' + wp + '</b></td></tr><tr><td width="70%" valign="top">' + '<b>{{created_by}}:</b> ' + username + topratings + '</td></tr></td></tr></table>'
        });

        google.maps.event.addListener(marker, "click", function () {
            if (currentinfowindow !== null) {
                currentinfowindow.close();
            }
            infowindow.open(map, marker);
            currentinfowindow = infowindow;
        });
    }

    function check_logs() {
        if (document.myroute_form.cache_log[1].checked == true) {
            if (isNaN(document.myroute_form.nrlogs.value)) {
                alert("Minimalna ilość logów musi być cyfrą!");
                return false;
            } else if (document.myroute_form.nrlogs.value <= 0 || document.myroute_form.nrlogs.value > 999) {
                alert("Dozwolona wartość minimalnej ilości logów musi być z zakresu: 0 - 999");
                return false;
            }
        }
        return true;
    }
    function sync_options(element)
    {
        var nlogs = 0;
        if (document.forms['myroute_form'].cache_log[0].checked == true) {
            document.forms['myroute_form'].nrlogs.disabled = 'disabled';
            nlogs = 0;
        }
        else if (document.forms['myroute_form'].cache_log[1].checked == true) {
            document.forms['myroute_form'].nrlogs.disabled = false;
            nlogs = document.forms['myroute_form'].nrlogs.value;
        }
        document.forms['myroute_form'].logs.value = nlogs;
    }

    window.onload = function () {
        load(document.myroute_form.distance.value, "<?= $polyline->points ?>");
// display caches
        {points}
    };

//]]>
</script>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{caches_along_route}} ({number_caches}): <span style="color: black;font-size:13px;">{routes_name} ({{radius}} {distance} km)</span></div>

<div class="searchdiv">
    <center>
        <div id="map" style="width:100%; height:500px"></div>
        <br/><span style="font-weight:bold;">{{marked_grey_search_area}}</span>
    </center>
</div>
<br/>

<form action="myroutes_search.php" method="post" enctype="multipart/form-data" name="myroute_form" dir="ltr">
    <div class="searchdiv">
        <input type="hidden" name="routeid" value="{routeid}"/>
        <input type="hidden" name="distance" value="{distance}"/>
        <input type="hidden" name="logs" value=""/>
        <table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">
            <tr>
                <td class="content-title-noshade" style="font-size:14px;">{{logs_cache_gpx}}:</td></tr>
            <tr>
                <td class="content-title-noshade" style="font-size:12px;" colspan="2">
                    <input type="radio" name="cache_log" value="0" tabindex="0" id="l_all_logs_caches" class="radio" onclick="javascript:sync_options(this)" {all_logs_caches} /> <label for="l_all_logs_caches">{{show_all_log_entries}}</label>&nbsp;
                    <input type="radio" name="cache_log" value="1" tabindex="1" id="l_minl_caches" class="radio" onclick="javascript:sync_options(this)" {min_logs_caches} /> <label for="l_minl_caches">{{min_logs_cache}}</label>&nbsp;
                    <input type="text" name="nrlogs" value="{nrlogs}" maxlength="3" class="input50" onchange="javascript:sync_options(this)" {min_logs_caches_disabled}/>
                </td>
            </tr>
        </table>
    </div>
    <br/>
    <button type="submit" name="back" value="back" style="font-size:12px;width:160px"><b>{{back}}</b></button>&nbsp;&nbsp;
    {list_empty_start}
    <button type="submit" name="submit_gpx" value="submit_gpx" style="font-size:12px;width:160px"><b>{{save_gpx}}</b></button>
    <button type="submit" name="submit_gpx_with_photos" value="submit_gpx_with_photos" style="font-size:12px;width:160px"><b>{{save_gpx_with_photos}}</b></button>
    {list_empty_end}
    <br/><br/><br/>
</form>
