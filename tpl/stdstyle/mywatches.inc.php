<?php


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

     set template specific variables

 ****************************************************************************/

    //$watche = '<tr><td><a href="viewcache.php?cacheid={urlencode_cacheid}">{cachename}</a></td><td>&nbsp;</td><td nowrap style="text-align:center;">{lastfound}</td><td nowrap style="text-align:center;">[<a href="removewatch.php?cacheid={cacheid}&target=mywatches.php">'.tr('delete').'</a>]</td></tr>';
    $watch = '<tr>'
                .'<td style="background-color: {bgcolor}"><img src="{cacheicon}" alt="" /></td>'
                .'<td style="background-color: {bgcolor}"><a href="viewcache.php?cacheid={urlencode_cacheid}">{cachename}</a></td>'
                .'<td style="background-color: {bgcolor}">&nbsp;</td>'
                .'<td nowrap style="text-align:center; background-color: {bgcolor}">{lastfound}</td>'
                .'<td nowrap style="text-align:center; background-color: {bgcolor}"><img src="tpl/stdstyle/images/{icon_name}" border="0" alt="" onmouseover="Tip(\'{log_text}\', OFFSETY, 25, OFFSETX, -135, PADDING,5, WIDTH,280,SHADOW,true)" onmouseout="UnTip()"/></td>'
                .'<td style="width:23px;background-color: {bgcolor}; text-align: center"><a class="links"  href="removewatch.php?cacheid={cacheid}&target=mywatches.php" onclick="return confirm(\''.tr("mywatches_1").'\');"><img style="vertical-align: middle;" src="tpl/stdstyle/images/log/16x16-trash.png" alt="" title='.tr('delete').' /></a></td>'
            .'</tr>';

//<a class="links" onmouseover="Tip({log_text})" onmouseout="UnTip() >
//.'<td style="text-align:center; background-color: {bgcolor}">[<a href="removewatch.php?cacheid={cacheid}&target=mywatches.php">'.tr('delete').'</a>]</td>'

    $no_watches = '<tr><td colspan="6">'.tr('no_watched_caches').'</td></tr>';
    $no_found_date = '---';
    $standard_title = tr('watched_caches');
    $print_delete_all_watches = '<tr><td colspan="4">&nbsp;</td><td nowrap style="text-align:center;">[<a href="printcache.php?source=mywatches">'.tr('print_all').'</a>]</td><td nowrap style="text-align:center;">[<a href="removewatch.php?cacheid=all&target=mywatches.php">'.tr('remove_all').'</a>]</td></tr>';
    $export_all_watches = '
                <tr>
                    <td colspan="6">'.tr('download').':
                        <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=gpxgc" title="GPS Exchange Format .gpx">GPX</a>
                        <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=loc" title="Waypoint .loc">LOC</a>
                        <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=kml" title="Google Earth .kml">KML</a>
                        <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=ov2" title="TomTom POI .ov2">OV2</a>
                        <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=ovl" title="TOP50-Overlay .ovl">OVL</a>
                        <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=txt" title="Tekst .txt">TXT</a>
                        <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=wpt" title="Oziexplorer .wpt">WPT</a>
                        <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=uam" title="AutoMapa .uam">UAM</a>
                        <a href="search.php?searchto=searchbywatched&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=zip" title="Garmin ZIP file (GPX + zdjęcia)  .zip">GARMIN</a>
                        <br />
                        <span class="help">'.tr('accept_terms_of_use').'</span>
                    </td>
                </tr>';

    $bgcolor1 = '#ffffff';
    $bgcolor2 = '#eeeeee';

?>
