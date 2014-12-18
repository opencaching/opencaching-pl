<?php

$error_queryname_exists = '<tr><td colspan="2" class="errormsg">Podana nazwa istnieje już</td></tr>';
$error_empty_name = '<tr><td colspan="2" class="errormsg">Musisz podać nazwe pod jaką ma być zapisane opcje szukania</td></tr>';
$viewquery_line = '<tr>
            <td  width="30%" style="text-align: left; vertical-align: middle; background-color: {bgcolor}"><a href="search.php?queryid={queryid}">{queryname}</a></td>
            <td width="60%" style="text-align: left; vertical-align: middle; background-color: {bgcolor}">
            <a href="search.php?queryid={queryid}&output=gpx&count=max&zip=1" title="GPS Exchange Format .gpx">GPX</a>
            <a href="search.php?queryid={queryid}&output=gpxgc&count=max&zip=1" title="GPS Exchange Format (Groundspeak) .gpx">GPX GC</a>
            <a href="search.php?queryid={queryid}&output=loc&count=max&zip=1" title="Waypoint .loc">LOC</a>
            <a href="search.php?queryid={queryid}&output=kml&count=max&zip=1" title="Google Earth .kml">KML</a>
            <a href="search.php?queryid={queryid}&output=ov2&count=max&zip=1" title="TomTom POI .ov2">OV2</a>
            <a href="search.php?queryid={queryid}&output=ovl&count=max&zip=1" title="TOP50-Overlay .ovl">OVL</a>
            <a href="search.php?queryid={queryid}&output=txt&count=max&zip=1" title="Tekst .txt">TXT</a>
            <a href="search.php?queryid={queryid}&output=wpt&count=max&zip=1" title="Oziexplorer .wpt">WPT</a>
            <a href="search.php?queryid={queryid}&output=uam&count=max&zip=1" title="AutoMapa .uam">UAM</a>
            <a href="search.php?queryid={queryid}&output=xml&count=max&zip=1" title="xml">XML</a>
            <a href="search.php?queryid={queryid}&output=zip&count=max&zip=1" title="Garmin ZIP file (GPX + zdjęcia)  .zip">GARMIN</a>
            </td>
            <td width="10%" bgcolor="{bgcolor}" style="text-align: center"; vertical-align: middle; background-color: {bgcolor}"><a href="query.php?queryid={queryid}&action=delete" onclick="return confirm(\'' . tr("myviewqueries_1") . '\');" ><img style="vertical-align: middle;" src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title=' . tr('delete') . ' /></a></td>
            </tr>';
$noqueries = '<tr><td colspan="2">' . tr('no_queries') . '</td></tr>';

$saveastext = tr('select_queries');
$nosaveastext = tr('no_queries');

$bgcolor1 = '#eeeeee';
$bgcolor2 = '#ffffff';
?>
