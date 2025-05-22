<?php
use src\Utils\Database\XDb;
use src\Utils\Uri\Uri;
use src\Libs\PolylineEncoder\PolylineEncoder;

$route_id = $_REQUEST['routeid'];

$rscp = XDb::xSql("SELECT `lat` ,`lon`
                    FROM `route_points`
                    WHERE `route_id`= ? ", $route_id);
$p = array();
$points = array();
for ($i = 0; false != ($record = XDb::xFetchArray($rscp)); $i++) {

    $y = $record['lon'];
    $x = $record['lat'];

    $p[0] = $x;
    $p[1] = $y;
    $points[$i] = $p;
}

$encoder = new PolylineEncoder();
$polyline = $encoder->encode($points);
?>

<script src="<?=Uri::getLinkWithModificationTime('/views/myRoutes/myroutes_map.js')?>"></script>
<script>
//<![CDATA[

    var currentinfowindow = null;

    var icon3 = {url: "/images/google_maps/gmgreen.png"};

    function addMarker(lat, lon, iconUrl, cacheid, cachename, wp, username, ratings) {
        var icon = L.icon({
            iconUrl: iconUrl,
            iconSize: [16, 16],
            iconAnchor: [8, 8],
            popupAnchor: [0, -16]
        });

        var marker = L.marker([lat, lon], { icon: icon });

        var topratings = "";
        if (ratings > 0) {
            topratings = `
                <br/>
                <img width="10" height="10" src="images/rating-star.png" alt="{{recommendation}}" />
                <b>{{search_recommendations}}: </b>
                <span style="font-weight: bold; color: green;">${ratings}</span>
            `;
        }

        var popupContent = '<table border="0"><tr><td>' +
            '<img src="' + iconUrl + '" border="0" alt=""/>&nbsp;<a href="viewcache.php?cacheid=' + cacheid + '" target="_blank">' + cachename + '</a>' +
            ' - <b>' + wp + '</b></td></tr><tr><td width="70%" valign="top">' + '<b>{{created_by}}:</b> ' + username + topratings + '</td></tr></td></tr></table>';

        marker.bindPopup(popupContent);

        marker.addTo(leafletMap);

        marker.on('click', function () {
            console.log(`Marker clicked: ${cachename} (${cacheid})`);
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
<div class="content2-pagetitle"><img src="/images/blue/route.png" class="icon32" alt="" />&nbsp;{{caches_along_route}} ({number_caches}): <span style="color: black;font-size:13px;">{routes_name} ({{radius}} {distance} km)</span></div>

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
            <tr class="form-group-sm">
                <td class="content-title-noshade" style="font-size:12px;" colspan="2">
                    <input type="radio" name="cache_log" value="0" tabindex="0" id="l_all_logs_caches" class="radio" onclick="javascript:sync_options(this)" {all_logs_caches} /> <label for="l_all_logs_caches">{{show_all_log_entries}}</label>&nbsp;
                    <input type="radio" name="cache_log" value="1" tabindex="1" id="l_minl_caches" class="radio" onclick="javascript:sync_options(this)" {min_logs_caches} /> <label for="l_minl_caches">{{min_logs_cache}}</label>&nbsp;
                    <input type="text" name="nrlogs" value="{nrlogs}" maxlength="3" class="form-control input70" onchange="javascript:sync_options(this)" {min_logs_caches_disabled}/>
                </td>
            </tr>
        </table>
    </div>
    <br/>
    <button type="submit" name="back" value="back" class="btn btn-default">{{back}}</button>&nbsp;&nbsp;
    {list_empty_start}
    <button type="submit" name="submit_gpx" value="submit_gpx" class="btn btn-primary">{{save_gpx}}</button>
    <button type="submit" name="submit_gpx_with_photos" value="submit_gpx_with_photos" class="btn btn-primary">{{save_gpx_with_photos}}</button>
    {list_empty_end}
    <br/><br/><br/>
</form>
