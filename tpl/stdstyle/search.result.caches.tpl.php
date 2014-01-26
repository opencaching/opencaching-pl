<?php
    /***************************************************************************
                                                ./tpl/stdstyle/search.result.tpl.php
                                                                -------------------
            begin                : July 25 2004
            copyright            : (C) 2004 The OpenCaching Group
            forum contact at     : http://www.opencaching.com/phpBB2

        ***************************************************************************/

    /***************************************************************************
        *
        *   This program is free software; you can redistribute it and/or modify
        *   it under the terms of the GNU General Public License as published by
        *   the Free Software Foundation; either version 2 of the License, or
        *   (at your option) any later version.
        *
        ***************************************************************************/

    /****************************************************************************

    Unicode Reminder ??

        (X)HTML search output template

    ****************************************************************************/
    global $usr, $hide_coords;
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="Wyszukiwanie" title="Suchergebnis" align="middle" />&nbsp;{{search_results}} {results_count}</div>
<div class="content-title-noshade">
    <p align="left">
        <img src="tpl/stdstyle/images/blue/search3.png" class="icon32" alt="Search results" title="Search results" align="middle"/>&nbsp;<a href="search.php?queryid={queryid}&amp;showresult=0">{{search}}</a>&nbsp;&nbsp;
        <img src="tpl/stdstyle/images/blue/save.png" class="icon32" alt="Save results" title="Save results" align="middle"/>&nbsp;{safelink}<br/>
        {pages}<br/>
    </p>
</div>
<table class="content" style="font-size: 13px; line-height: 1.6em;" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="2" style="padding-left: 0px; padding-right: 0px;">
            <table border="0" cellspacing="0" cellpadding="0" class="null">
                <tr>
                <td width="18" height="13" bgcolor="#E6E6E6">#</td>
                <td width="15" height="13" bgcolor="#E6E6E6"><b>{distanceunit}</b></td>
                <td width="80" height="13" bgcolor="#E6E6E6"><b>WGS84</b></td>
                <td width="16" height="13" bgcolor="#E6E6E6"><b>{{recomm_short}}</b></td>
                <td width="32" height="13" bgcolor="#E6E6E6"><b>{{type}}</b></td>
                <td width="46" height="13" bgcolor="#E6E6E6"><b>Z/T</b></td>
                <td width="448" height="13" bgcolor="#E6E6E6"><b>{{name_label}}</b></td>
                <td width="126" height="13" bgcolor="#E6E6E6"><b>{{logs_info}}</b></td>
                <td width="20" height="13" bgcolor="#E6E6E6"></td>
                </tr>
                <!--a-->{results}<!--z-->
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="header-small">{pages}</td>
    </tr>
</table>
<?php
global $usr, $hide_coords;
$login =0;
$googlemaps = "";
if ($usr || !$hide_coords){ 
    $queryid = tpl_get_var('queryid');
    $startat = tpl_get_var('startat');
    $google_kml_link = $absolute_server_URI . "search.php?queryid=$queryid&output=kml&startat=$startat";
    if ($hide_coords){
        $google_kml_link .= requestSigner::get_signature_text();
    }
    $google_kml_link = urlencode($google_kml_link);
echo "

<table class=\"content\" style=\"font-size: 12px; line-height: 1.6em;\">
    <tr>
        <td width=\"350\"><img src=\"tpl/stdstyle/images/blue/save.png\" class=\"icon32\" alt=\"Save results\" title=\"Save results\" align=\"middle\"/><b>".tr('download')."</b></td>
                <td>&nbsp;</td>
        </tr>
        </table>
        <div class=\"searchdiv\">
   <table class=\"content\" style=\"font-size: 12px; line-height: 1.6em;\">
       <tr>
        <td  width=\"350\" align=\"left\" style=\"padding-left:5px;\">
            ".tr('listing_from_this_page').":
                </td>
                <td>
                    <span class=\"content-title-noshade txt-blue08\">{{format_GPX}}</span>:<br/>
            <a class=\"links\" href=\"ocplgpx";?>{queryid}<?php echo ".gpx?startat=";?>{startat}<?php echo "\" title=\"GPS Exchange Format .gpx\">GPX</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".zip?startat=";?>{startat}<?php echo "\" title=\"Garmin ZIP file ({{format_pict}})  .zip\">GARMIN ({{format_pict}})</a>
                    </td>
        </tr>
        <tr>
                <td>&nbsp;</td>
                <td><span class=\"content-title-noshade txt-blue08\">{{format_other}}</span>:<br/>
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".loc?startat=";?>{startat}<?php echo "\" title=\"Waypoint .loc\">LOC</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".kml?startat=";?>{startat}<?php echo "\" title=\"Google Earth .kml\">KML</a> |
            <a class=\"links\" href='http://maps.google.pl/maps?f=q&amp;hl=pl&amp;geocode=&amp;q=$google_kml_link' target='_blank' title='".tr('show_in_google_maps')."'>GoogleMaps</a> | ";
            echo "<a class=\"links\" href=\"search.ov2?queryid=";?>{queryid}<?php echo "&amp;output=ov2&amp;startat=";?>{startat}<?php echo "\" title=\"TomTom POI .ov2\">OV2</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".ovl?startat=";?>{startat}<?php echo "\" title=\"TOP50-Overlay .ovl\">OVL</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".txt?startat=";?>{startat}<?php echo "\" title=\"Text .txt\">TXT</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".wpt?startat=";?>{startat}<?php echo "\" title=\"Oziexplorer .wpt\">WPT</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".uam?startat=";?>{startat}<?php echo "\" title=\"AutoMapa .uam\">UAM</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".xml?startat=";?>{startat}<?php echo "\" title=\"xml\">XML</a>
                    </td>
         </tr>

 </table></div><div class=\"searchdiv\">
 <table class=\"content\" style=\"font-size: 12px; line-height: 1.6em;\">
    <tr>
        <td width=\"350\" align=\"left\" style=\"padding-left:5px;\">
                ".tr('listing_from_to').":
                 </td>
                 <td>
                   <span class=\"content-title-noshade txt-blue08\">{{format_GPX}}</span>:<br/>
            <a class=\"links\" href=\"ocplgpx";?>{queryid}<?php echo ".gpx?startat=";?>{startat}<?php echo "&amp;count=max&amp;zip=1\" title=\"GPS Exchange Format .gpx\">GPX</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".zip?startat=";?>{startat}<?php echo "&amp;count=max&amp;zip=1\" title=\"Garmin ZIP file ({{format_pict}})  .zip\">GARMIN ({{format_pict}})</a>
                    </td>
          </tr>
          <tr>
                 <td>&nbsp;</td>
                        <td><span class=\"content-title-noshade txt-blue08\">{{format_other}}</span>:<br/>
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".loc?startat=";?>{startat}<?php echo "&amp;count=max&amp;zip=1\" title=\"Waypoint .loc\">LOC</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".kml?startat=";?>{startat}<?php echo "&amp;count=max&amp;zip=1\" title=\"Google Earth .kml\">KML</a> |
            <a class=\"links\" href='http://maps.google.pl/maps?f=q&amp;hl=pl&amp;geocode=&amp;q=http:%2F%2Fwww.opencaching.pl%2Fsearch.php%3Fqueryid%3D";?>{queryid}<?php echo "%26output%3Dkml%26startat%3D";?>{startat}<?php echo "%26count%3Dmax%26zip%3D1&amp;ie=UTF8&amp;z=7' target='_blank' title='".tr('show_in_google_maps')."'>GoogleMaps</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".ov2?startat=";?>{startat}<?php echo "&amp;count=max&amp;zip=1\" title=\"TomTom POI .ov2\">OV2</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".ovl?startat=";?>{startat}<?php echo "&amp;count=max&amp;zip=1\" title=\"TOP50-Overlay .ovl\">OVL</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".txt?startat=";?>{startat}<?php echo "&amp;count=max&amp;zip=1\" title=\"Text .txt\">TXT</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".wpt?startat=";?>{startat}<?php echo "&amp;count=max&amp;zip=1\" title=\"Oziexplorer .wpt\"> WPT</a> |
            <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".uam?startat=";?>{startat}<?php echo "&amp;count=max&amp;zip=1\" title=\"AutoMapa .uam\">UAM</a> |
                        <a class=\"links\" href=\"ocpl";?>{queryid}<?php echo ".xml?startat=";?>{startat}<?php echo "&amp;count=max&amp;zip=1\" title=\"xml\">XML</a>
                    </td>
       </tr>
</table></div>
<p>" . '{{accept_terms_of_use}}' ." </p><br/>"; } ?>
