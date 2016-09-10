<?php

use Utils\Database\XDb;
global $usr;
global $get_userid;
global $filter;
global $language;
global $lang;
$userid = '';

if ($usr) {
    if ($get_userid != '')
        $userid = $get_userid;
    else
        $userid = $usr['userid'];

    function makeFilter()
    {

        $filter = "";
        if (isset($_GET['u']))
            $filter .= "u";
        if (isset($_GET['t']))
            $filter .= "t";
        if (isset($_GET['m']))
            $filter .= "m";
        if (isset($_GET['v']))
            $filter .= "v";
        if (isset($_GET['w']))
            $filter .= "w";
        if (isset($_GET['e']))
            $filter .= "e";
        if (isset($_GET['q']))
            $filter .= "q";
        if (isset($_GET['o']))
            $filter .= "o";
        if (isset($_GET['c']))
            $filter .= "c";
        if (isset($_GET['d']))
            $filter .= "d";
        if (isset($_GET['I']))
            $filter .= "I";
        if (isset($_GET['W']))
            $filter .= "W";
        if (isset($_GET['Z']))
            $filter .= "Z";
        if (isset($_GET['A']))
            $filter .= "A";
        if (isset($_GET['N']))
            $filter .= "N";
        if (isset($_GET['C']))
            $filter .= "C";
        if (isset($_GET['T']))
            $filter .= "T";
        if (isset($_GET['Y']))
            $filter .= "Y";

        if ($filter == "")
            return -1;
        return $filter;
    }

    function getCacheData($cacheid)
    {
        $query = XDb::xSql(
            "SELECT caches.longitude, caches.latitude, caches.wp_oc as wp, caches.votes,
                (
                    SELECT count(*) FROM cache_logs
                    WHERE deleted=0 AND cache_id= ? AND type=1
                ) as founds,
                (
                    SELECT count(*) FROM cache_logs
                    WHERE deleted=0 AND cache_id= ? AND type=2
                ) as notfounds,
                caches.topratings, caches.score as score, caches.name as cachename, user.username as username
            FROM caches, user
            WHERE user.user_id = caches.user_id AND cache_id = ? ",
            $cacheid, $cacheid, $cacheid);

        return XDb::xFetchArray($query);
    }
    ?>
    <script type="text/javascript" src="/lib/labeledmarker.js">
    </script>

    <script type="text/javascript">

    //<![CDATA[
                var map;
                var latch = 0;
                var user = <?php echo $userid; ?>;
                var zoom;
                var caches = "<?php echo (($filter[19] + 1) * 50); ?>";
                var order = "<?php echo $filter[20]; ?>";
                var filter = "<?php echo $filter; ?>";
                function switchActiveFilter(id)
                {
                /*if( id == 'aktywne' )
                 {
                 if( document.getElementById('aktywne').checked == 1 )
                 document.getElementById('nieaktywne').checked = 0;
                 }
                 if( id == 'nieaktywne' )
                 {
                 if( document.getElementById('nieaktywne').checked == 1 )
                 document.getElementById('aktywne').checked = 0;
                 }*/
                }

        function statusToImageName(status)
        {
        switch (status)
        {
        case "2":
                return "-n";
                case "3":
                return "-a";
                case "6":
                return "-d";
                default:
                return "-s";
        }
        }

        function greyedWindow(status)
        {
        if (status == "1")
        {
        return "class=\"enabled\"";
        }
        return "class=\"disabled\"";
        }

        function typeToImageName(type, status)
        {
        switch (type)
        {
        case "T":
                default:
                return "traditional" + statusToImageName(status) + ".png";
                case "E":
                return "event" + statusToImageName(status) + ".png";
                case "M":
                return "multi" + statusToImageName(status) + ".png";
                case "O":
                return "moving" + statusToImageName(status) + ".png";
                case "Q":
                return "quiz" + statusToImageName(status) + ".png";
                case "U":
                return "unknown" + statusToImageName(status) + ".png";
                case "V":
                return "virtual" + statusToImageName(status) + ".png";
                case "W":
                return "webcam" + statusToImageName(status) + ".png";
        }
        }

        function setMapSettings() {
        switch (map.getCurrentMapType()) {
        case G_NORMAL_MAP:
                document.getElementById("maptype").value = "0";
                break;
                case G_SATELLITE_MAP:
                document.getElementById("maptype").value = "1";
                break;
                case G_HYBRID_MAP:
                document.getElementById("maptype").value = "2";
                break;
                case G_PHYSICAL_MAP:
                document.getElementById("maptype").value = "3";
                break;
                default:
                document.getElementById("maptype").value = "0";
        }
        }

        function getForeignAddress(wp)
        {
        switch (wp.substring(0, 2))
        {
        case "OP":
                return "";
                case "OB":
                return "";
                case "OC":
                return "http://www.opencaching.de/";
                case "OZ":
                return "http://www.opencaching.cz/";
        }
        }

        function load_data(page) {
        var zoom = map.getZoom();
                document.getElementById("zoom").value = map.getZoom();
                map.clearOverlays();
                var latNE = map.getBounds().getNorthEast().lat();
                var lonNE = map.getBounds().getNorthEast().lng();
                var latSW = map.getBounds().getSouthWest().lat();
                var lonSW = map.getBounds().getSouthWest().lng();
                //var centerX = map.getBounds().getCenterX();
                //var centerY = map.getBounds().getCenterY();

                document.getElementById("lat").value = map.getBounds().getCenter().lat();
                document.getElementById("lon").value = map.getBounds().getCenter().lng();
                document.getElementById("inputZoom").value = map.getZoom();
                var baseIcon = new GIcon();
                baseIcon.iconSize = new GSize(21, 32);
                baseIcon.iconAnchor = new GPoint(10, 32);
                baseIcon.infoWindowAnchor = new GPoint(9, 2);

                if (zoom >= 1) {

        // Creates a marker at the given point with the given label
        function createMarker(point, marker) {
        var cache_id = marker.getAttribute("id");
                var cache_name = marker.getAttribute("name");
                var cache_owner = marker.getAttribute("owner");
                var cache_type = marker.getAttribute("type");
                var cache_old = marker.getAttribute("old");
                var cache_uid = marker.getAttribute("owner_id");
                var cache_found = marker.getAttribute("found");
                var cache_founds = marker.getAttribute("founds");
                var cache_notfounds = marker.getAttribute("notfounds");
                var cache_votes = marker.getAttribute("votes");
                var cache_score = marker.getAttribute("score");
                var cache_topratings = marker.getAttribute("topratings");
                var cache_wp = marker.getAttribute("wp");
                var foreign_address = getForeignAddress(cache_wp);
                var cache_druk = marker.getAttribute("druk");
                var cache_status = marker.getAttribute("status");
                if (cache_type == 'O')
                baseIcon.iconSize = new GSize(17, 26);
                else
                baseIcon.iconSize = new GSize(21, 32);
                var icon = new GIcon(baseIcon);
                var show_score;
                var print_topratings;
                if (cache_score != "")
        {
        if (cache_score > 0)
                show_score = "<br /><b>{{score}}:</b> +" + cache_score;
                else
                show_score = "<br /><b>{{score}}:</b> " + cache_score;
                if (cache_score >= 2)
                cache_score = "3";
                else if (cache_score >= 0.4)
                cache_score = "2";
                else if (cache_score >= - 0.5)
                cache_score = "1";
                else
                cache_score = "0";
        }
        else
                show_score = "";
                if (cache_topratings == 0)
                print_topratings = "";
                else
        {
        print_topratings = "<br /><b>{{recommendations}}: </b>";
                var gwiazdka = "<img width=\"10\" height=\"10\" src=\"images/rating-star.png\" alt=\"{{recommendation}}\" />";
                var i = 0;
                for (i = 0; i < cache_topratings; i++)
                print_topratings += gwiazdka;
        }


        if (cache_uid == user) {
        icon.image = "tpl/stdstyle/images/google_maps/own" + cache_type + cache_score + ".png";
        } else if (cache_found == 1) {
        icon.image = "tpl/stdstyle/images/google_maps/found" + cache_type + cache_score + ".png";
        } else if (cache_old <= 10) {
        icon.image = "tpl/stdstyle/images/google_maps/new" + cache_type + cache_score + ".png";
        } else {
        icon.image = "tpl/stdstyle/images/google_maps/r" + cache_type + cache_score + ".png";
        }

        opts = {
        "icon": icon,
                "clickable": true,
                "labelText": <?php echo ($filter[15] != 0) ? ("cache_wp") : ("\"\""); ?>,
                "labelOffset": new GSize( - 25, - 3)
        };
                var marker = new LabeledMarker(point, opts);
                //var marker = new GMarker(point, icon);
                GEvent.addListener(marker, "click", function () {


                var yntext = "";
                        if (cache_druk == "y")
                        yntext = "{{add_to}}";
                        else
                        yntext = "{{remove_from}}";
                        var infoWindowContent = "";
                        infoWindowContent += "<table border=\"0\" width=\"350\" height=\"120\">";
                        infoWindowContent += "<tr><td colspan=\"2\" width=\"100%\"><table cellspacing=\"0\" width=\"100%\"><tr><td width=\"90%\" align=\"left\">";
                        infoWindowContent += "<center><img align=\"left\" width=\"20\" height=\"20\" src=\"tpl/stdstyle/images/cache/" + typeToImageName(cache_type, cache_status) + "\" /></center>";
                        infoWindowContent += "&nbsp;<a href=\"" + foreign_address + "viewcache.php?cacheid=" + cache_id + "\" target=\"_blank\">" + cache_name + "</a></font>";
                        infoWindowContent += "</td><td width=\"10%\">";
                        infoWindowContent += "<b>" + cache_wp + "</b></td></tr></table>";
                        infoWindowContent += "</td></tr>";
                        infoWindowContent += "<tr><td width=\"70%\" valign=\"top\">";
                        infoWindowContent += "<b>{{created_by}}:</b> " + cache_owner + show_score + print_topratings;
                        infoWindowContent += "</td>";
                        infoWindowContent += "<td valign=\"top\" width=\"30%\"><table cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"100%\">";
                        infoWindowContent += "<nobr><img src=\"tpl/stdstyle/images/log/16x16-found.png\" border=\"0\" width=\"10\" height=\"10\" /> " + cache_founds + " x {{found}}</nobr></td></tr>";
                        infoWindowContent += "<tr><td width=\"100%\"><nobr><img src=\"tpl/stdstyle/images/log/16x16-dnf.png\" border=\"0\" width=\"10\" height=\"10\" /> " + cache_notfounds + " x {{not_found}}</nobr><nobr></td></tr>";
                        if (getForeignAddress(cache_wp) == "")
                        infoWindowContent += "<tr><td width=\"100%\"><img src=\"tpl/stdstyle/images/action/16x16-adddesc.png\" border=\"0\" width=\"10\" height=\"10\" /> " + cache_votes + " x {{scored}}</nobr>";
                        infoWindowContent += "</td></tr></table></td></tr>";
                        infoWindowContent += "<tr><td align=\"left\" width=\"100%\" colspan=\"2\">";
                        if (getForeignAddress(cache_wp) == "")
                        infoWindowContent += "<font size=\"0\"><a href=\"cachemap2.php?lat=" + document.getElementById("lat").value + "&lon=" + document.getElementById("lon").value + "&cacheid=" + cache_id + "&print_list=" + cache_druk + "&inputZoom=" + document.getElementById("inputZoom").value + "\">" + yntext + " {{to_print_list}}</a></font>";
                        infoWindowContent += "</td></tr></table></td></tr>";
                        infoWindowContent += "</table>";
                        marker.openInfoWindowHtml(infoWindowContent);
                });
                return marker;
        }

        var request = GXmlHttp.create();
                request.open("GET", "lib/marker.php?u=" + user + "&latNE=" + latNE + "&lonNE=" + lonNE + "&latSW=" + latSW + "&lonSW=" + lonSW + "&page=" + page + "&caches=" + caches + "&order=" + order + "&filter=" + filter, true);
                request.onreadystatechange = function () {
                if (request.readyState == 4) {
                var xmlDoc = request.responseXML;
                        var markers = xmlDoc.documentElement.getElementsByTagName("marker");
                        for (var i = 0; i < markers.length; i++)
                {
                var point = new GLatLng(markers[i].getAttribute("lat"), markers[i].getAttribute("lng"));
                        var marker = createMarker(point, markers[i]);
                        map.addOverlay(marker);
    <?php
    $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : 0;
    $cacheData = getCacheData($cache_id);
    ?>
                var cache_id = <?php echo $cache_id; ?>;
                        var cache_name = "<?php echo $cacheData['cachename']; ?>";
                        var cache_owner = "<?php echo $cacheData['username']; ?>";
                        var cache_topratings = <?php echo ($cacheData['topratings'] == '' ? '0' : $cacheData['topratings']); ?>;
                        var cache_score = <?php echo ($cacheData['score'] == '' ? '""' : $cacheData['score']); ?>;
                        var cache_founds = <?php echo ($cacheData['founds'] == '' ? "0" : $cacheData['founds']); ?>;
                        var cache_notfounds = <?php echo ($cacheData['notfounds'] == '' ? "0" : $cacheData['notfounds']); ?>;
                        var cache_votes = <?php echo ($cacheData['votes'] == '' ? "0" : $cacheData['votes']); ?>;
                        var cache_wp = "<?php echo $cacheData['wp']; ?>";
                        var show_score;
                        var print_topratings;
                        if (cache_score != "" && cache_votes > 2)
                {
                if (cache_score > 0)
                        show_score = "<br /><b>{{score}}:</b> +" + cache_score;
                        else
                        show_score = "<br /><b>{{score}}:</b> " + cache_score;
                        if (cache_score >= 2)
                        cache_score = "3";
                        else if (cache_score >= 0.4)
                        cache_score = "2";
                        else if (cache_score >= - 0.5)
                        cache_score = "1";
                        else
                        cache_score = "0";
                }
                else
                        show_score = "";
                        if (cache_topratings == 0)
                        print_topratings = "";
                        else
                {
                print_topratings = "<br /><b>{{recommendations}}: </b>";
                        var gwiazdka = "<img width=\"10\" height=\"10\" src=\"images/rating-star.png\" alt=\"{{recommendation}\" />";
                        var ii;
                        for (ii = 0; ii < cache_topratings; ii++)
                        print_topratings += gwiazdka;
                }
    <?php ((!isset($_REQUEST['print_list']) || onTheList($_SESSION['print_list'], $cache_id) == -1 ) ? $yn = 'y' : $yn = 'n'); ?>

                if (markers[i].getAttribute("id") == cache_id)
                {
                var infoWindowContent = "";
                        infoWindowContent += "<table border=\"0\" width=\"350\" height=\"120\">";
                        infoWindowContent += "<tr><td colspan=\"2\" width=\"100%\"><table cellspacing=\"0\" width=\"100%\"><tr><td width=\"90%\">";
                        infoWindowContent += "<a href=\"viewcache.php?cacheid=" + cache_id + "\" target=\"_blank\">" + cache_name + "</a>";
                        infoWindowContent += "</td><td width=\"10%\">";
                        infoWindowContent += "<b>" + cache_wp + "</b></td></tr></table>";
                        infoWindowContent += "</td></tr>";
                        infoWindowContent += "<tr><td width=\"70%\" valign=\"top\">";
                        infoWindowContent += "<b>{{created_by}}:</b> " + cache_owner + show_score + print_topratings;
                        infoWindowContent += "</td>";
                        infoWindowContent += "<td valign=\"top\" width=\"30%\"><table cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"100%\">";
                        infoWindowContent += "<nobr><img src=\"tpl/stdstyle/images/log/16x16-found.png\" border=\"0\" width=\"10\" height=\"10\" /> " + cache_founds + " x {{found}}</nobr></td></tr>";
                        infoWindowContent += "<tr><td width=\"100%\"><nobr><img src=\"tpl/stdstyle/images/log/16x16-dnf.png\" border=\"0\" width=\"10\" height=\"10\" /> " + cache_notfounds + " x {{not_found}}</nobr><nobr></td></tr>";
                        if (getForeignAddress(cache_wp) == "")
                        infoWindowContent += "<tr><td width=\"100%\"><img src=\"tpl/stdstyle/images/action/16x16-adddesc.png\" border=\"0\" width=\"10\" height=\"10\" /> " + cache_votes + " x {{scored}}</nobr>";
                        infoWindowContent += "</td></tr></table></td></tr>";
                        infoWindowContent += "<tr><td align=\"left\" width=\"100%\" colspan=\"2\">";
                        if (getForeignAddress(cache_wp) == "")
                        infoWindowContent += "<font size=\"0\"><a href=\"cachemap2.php?lat=" + document.getElementById("lat").value + "&lon=" + document.getElementById("lon").value + "&cacheid=" + cache_id + "&print_list=<?php echo $yn; ?>&inputZoom=" + document.getElementById("inputZoom").value + "\"><?php echo ($yn == 'y' ? tr('add_to') : tr('remove_from')); ?> {{to_print_list}}</a></font>";
                        infoWindowContent += "</td></tr></table></td></tr>";
                        infoWindowContent += "</table>";
                        if (latch == 0)
                        marker.openInfoWindowHtml(infoWindowContent);
                        latch++;
                }
                }



                var system = xmlDoc.documentElement.getElementsByTagName("data");
                        document.getElementById('list').innerHTML = system[0].getAttribute("count") + " {{active_caches_in_area}} | " + markers.length + " {{shown}}<br />{{next}}: " + system[0].getAttribute("pager");
                }
                }

        request.send(null);
        }
        }

        function load() {
        if (GBrowserIsCompatible()) {

        var maptype = "<?php echo $filter[18]; ?>";
                var g_maptype;
                switch (maptype)
        {
        case "0": g_maptype = G_NORMAL_MAP; break;
                case "1": g_maptype = G_SATELLITE_MAP; break;
                case "2": g_maptype = G_HYBRID_MAP; break;
                case "3": g_maptype = G_PHYSICAL_MAP; break;
                default: g_maptype = G_PHYSICAL_MAP;
        }

        map = new GMap2(document.getElementById("map"));
                map.addControl(new GLargeMapControl());
                map.addControl(new GScaleControl());
                map.removeMapType(G_HYBRID_MAP);
                map.addMapType(G_PHYSICAL_MAP);
                map.addControl(new GMapTypeControl());
                map.addControl(new GOverviewMapControl());
                map.setCenter(new GLatLng({coords}), {zoom}, g_maptype);
                GEvent.addListener(map, "zoomend", function () {
                load_data(0);
                });
                GEvent.addListener(map, "dragend", function () {
                load_data(0);
                });
                //GEvent.addListener(map, "moveend", function() {
                //map.clearOverlays();
                //load_data(0);
                //});

                load_data(0);
        }

        }

    //]]>
    </script>
    <?php
    if (stripos($_SERVER['HTTP_USER_AGENT'], "MSIE") != NULL) {
        $x_print = 970;
        $y_print = 610;
    } else {
        $x_print = 1600;
        $y_print = 1000;
    }
    ?>
    <div class="content2-pagetitle">&nbsp;<img src="tpl/stdstyle/images/blue/world.png" class="icon32" alt="" title=""/>&nbsp;&nbsp;{{user_map}} {username}</div><br />
    <table border="0" cellspacing="0" cellpadding="0" width="99%" style="font-size: 115%;">
        <tr>
            <td>
                {{current_zoom}}: <input type="text" id="zoom" size="2" disabled>
            </td>
            <td align="right">
    <?php echo (!isset($_GET['print']) ? ('[<a href="cachemap2.php?print=y">' . tr('printer_friendly') . '</a>]') : ''); ?>
            </td>
        </tr>
        <td colspan="2">
            {{colors}}: <b><font color="#dddd00">{{yellow}}</font></b> - {{last_10_days}}, <b><font color="#00dd00">{{green}}</font></b> - {{own}}, <b><font color="#aaaaaa">{{gray}}</font></b> - {{found}}, <b><font color="#ff0000">{{red}}</font></b> - {{rest}} <br />
        </td>
    </tr>
    <tr><td colspan="2" width="100%"><div id="map" style="width: <?php echo (isset($_GET['print']) ? ($x_print . "px") : ("100%")); ?>; height: <?php echo (isset($_GET['print']) ? $y_print : ("600")); ?>px; float:left; border: 1px solid #000;">
            </div>

            <br /><br />
        </td>
    </tr>
    <tr>
        <td colspan="2">
    <?php if (isset($_GET['print'])) echo "<!--"; ?>
            <div id="list" style="border:1px solid black; background-color: #B6C0C0;float:left; margin-left: 5px; margin-top: 5px; padding: 2px;"></div>
            <div id="settings" style="border:1px solid black; background-color: #B6C0C0;float:left; margin-left: 5px; margin-top: 5px; padding: 2px;">
                {{max_caches_per_page}}: <select onchange="caches = this.options[this.selectedIndex].value;
                            document.getElementById('cachelimit').value = this.options[this.selectedIndex].value;
                            load_data(0);" style="border: none; background-color: #B6C0B0;">
    <?php
    for ($a = 0; $a < 10; $a++) {
        ?>
                        <option value="<?php echo (($a + 1) * 50); ?>" <?php if ($filter[19] == $a) echo "selected" ?>><?php echo (($a + 1) * 50); ?></option>
        <?php
    }
    ?>
                </select><br/>
                {{sort_by}}: <select onchange="order = this.options[this.selectedIndex].value;
                                    document.getElementById('cachesort').value = this.options[this.selectedIndex].value;
                                    load_data(0);" style="border: none; background-color: #B6C0B0;">
                    <option value="1" <?php if ($filter[20] == "1") echo "selected" ?>>cache ID</option>
                    <option value="2" <?php if ($filter[20] == "2") echo "selected" ?>>{{by_name}}</option>
                    <option value="3" <?php if ($filter[20] == "3") echo "selected" ?>>{{by_date}}</option>
                </select></div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div style="border:1px solid black; background-color: #B6C0C0;float:left; margin-left: 5px; margin-top: 5px; padding: 2px;"><b>{{hide_caches_type}}:</b>
                <form method="post" action="cachemap2.php" id="settingsForm" onsubmit="setMapSettings()">
                    <input id="nieznany" name="u" value="1" type="checkbox" <?php echo ($filter[0]) ? "" : "checked"; ?>><label for="nieznany">{{unknown_type}} (U)</label> |
                    <input id="tradycyjna" name="t" value="1" type="checkbox" <?php echo ($filter[1]) ? "" : "checked"; ?>><label for="tradycyjna">{{traditional}} (T)</label> |
                    <input id="multi" name="m" value="1" type="checkbox" <?php echo ($filter[2]) ? "" : "checked"; ?>><label for="multi">{{multicache}} (M)</label> |
                    <input id="wirtualna" name="v" value="1" type="checkbox" <?php echo ($filter[3]) ? "" : "checked"; ?>><label for="wirtualna">{{virtual}} (V)</label> |
                    <input id="webcam" name="w" value="1" type="checkbox" <?php echo ($filter[4]) ? "" : "checked"; ?>><label for="webcam">Webcam (W)</label> |
                    <input id="wydarzenia" name="e" value="1" type="checkbox" <?php echo ($filter[5]) ? "" : "checked"; ?>><label for="wydarzenia">{{event}} (E)</label> |
                    <input id="quiz" name="q" value="1" type="checkbox" <?php echo ($filter[6]) ? "" : "checked"; ?>><label for="quiz">Quiz (Q)</label> |
                    <input id="mobilna" name="o" value="1" type="checkbox" <?php echo ($filter[7]) ? "" : "checked"; ?>><label for="mobilna">{{moving}} (O)</label>
                    <br /><b>{{hide_caches}}:</b><br />
                    <input id="ignorowane" name="I" value="1" type="checkbox" <?php echo ($filter[10]) ? "" : "checked"; ?>><label for="ignorowane">{{ignored}}</label> |
                    <input id="wlasne" name="W" value="1" type="checkbox" <?php echo ($filter[11]) ? "" : "checked"; ?>><label for="wlasne">{{own}}</label> |
                    <input id="znalezione" name="Z" value="1" type="checkbox" <?php echo ($filter[12]) ? "" : "checked"; ?>><label for="znalezione">{{founds}}</label> |
                    <input id="jeszczenieznalezione" name="A" value="1" type="checkbox" <?php echo ($filter[13]) ? "" : "checked"; ?>><label for="jeszczenieznalezione">{{not_yet_found}}</label> |
                    <input id="geokrety" name="N" value="1" type="checkbox" <?php echo ($filter[14]) ? "" : "checked"; ?>><label for="geokrety">{{without_geokret}}</label>  |
                    <input id="aktywne" name="Y" value="1" type="checkbox" <?php echo ($filter[17]) ? "" : "checked"; ?>><label for="aktywne">{{ready_to_find}}</label>  |
                    <input id="nieaktywne" name="T" value="1" type="checkbox" <?php echo ($filter[16]) ? "" : "checked"; ?>><label for="nieaktywne">{{temp_unavailables}}</label>
                    <br />
                    <b>{{other_options}}:</b>
                    <br />
                    <input id="podpisy" name="C" value="1" type="checkbox" <?php echo (!$filter[15]) ? "" : "checked"; ?>><label for="podpisy">{{show_signes}}</label>

                    <br />
                    <input type="submit" name="submit" value={{filter}}>
                    <input type="hidden" name="userid" value="<?php echo $userid; ?>">
                    <input type="hidden" name="maptype" id="maptype" value="<?php echo $filter[18]; ?>">
                    <input type="hidden" name="cachelimit" id="cachelimit" value="<?php echo (($filter[19] + 1) * 50); ?>">
                    <input type="hidden" name="cachesort" id="cachesort" value="<?php echo $filter[20]; ?>">
    <?php
    if (!isset($_REQUEST['lat']) || !isset($_REQUEST['lon']) || $_REQUEST['lat'] == "" || $_REQUEST['lon'] == "") {
        $lat_val = "";
        $lon_val = "";
    } else {
        $lat_val = $_REQUEST['lat'];
        $lon_val = $_REQUEST['lon'];
    }
    ?>
    <?php if (isset($_GET['print'])) echo "-->"; ?>
                    <input type="hidden" name="lat" id="lat" value="<?php echo $lat_val; ?>">
                    <input type="hidden" name="lon" id="lon" value="<?php echo $lon_val; ?>">
                    <input type="hidden" name="inputZoom" id="inputZoom">
    <?php if (isset($_GET['print'])) echo "<!--"; ?>
                </form>
            </div>
    <?php if (isset($_GET['print'])) echo "-->"; ?>
        </td>
    </tr>
    </table>
    <?php
} else
    echo tr('cachemap_must_login');
?>
