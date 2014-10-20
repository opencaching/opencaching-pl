<?php
/***************************************************************************
                                                  ./tpl/stdstyle/mywatches.inc.php
                                                            -------------------
        begin                : July 17 2004
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

   Unicode Reminder メモ

     set template specific variables

 ****************************************************************************/

    $standard_title = tr('clipboard');
    $no_list = '<tr><td colspan="4">'.tr('mylist_01').'</td></tr>';

    $list_e = '<tr><td><a href="viewcache.php?cacheid={urlencode_cacheid}">{mod_suffix}{cachename}</a></td><td>&nbsp;</td><td nowrap style="text-align:center;">{lastfound}</td><td nowrap style="text-align:center;">[<a href="removelist.php?cacheid={cacheid}&target=mylist.php">'.tr('mylist_02').'</a>]</td></tr>';
    $list_o = '<tr bgcolor=\'#eeeeee\'><td><a href="viewcache.php?cacheid={urlencode_cacheid}">{mod_suffix}{cachename}</a></td><td>&nbsp;</td><td nowrap style="text-align:center;">{lastfound}</td><td nowrap style="text-align:center;">[<a href="removelist.php?cacheid={cacheid}&target=mylist.php">'.tr('mylist_02').'</a>]</td></tr>';
    $no_found_date = '---';
    $print_delete_list = '<tr><td colspan="4">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td><td nowrap style="text-align:center;">[<a href="printcache.php?source=mylist">'.tr('mylist_03').'</a>]</td><td nowrap style="text-align:center;">[<a href="removelist.php?cacheid=all&target=mylist.php">'.tr('mylist_04').'</a>]</td></tr>';
    $export_list = '<div>'.tr('mylist_05').':
                        <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=gpx" title="GPS Exchange Format .gpx">GPX</a>
                        <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=zip" title="Garmin (GPX+Photos)">Garmin</a>
                        <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=gpxgc" title="GPS Exchange Format (Groundspeak) .gpx">GPX GC</a>
                        <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=loc" title="Waypoint .loc">LOC</a>
                        <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=kml" title="Google Earth .kml">KML</a>
                        <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=ov2" title="TomTom POI .ov2">OV2</a>
                        <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=ovl" title="TOP50-Overlay .ovl">OVL</a>
                        <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=txt" title="Tekst .txt">TXT</a>
                        <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=wpt" title="Oziexplorer .wpt">WPT</a>
                        <a href="search.php?searchto=searchbylist&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;count=max&amp;output=uam" title="AutoMapa .uam">UAM</a>
                        <br />
<span class="help">'.tr('accept_terms_of_use').'</a></span></div>';
?>
