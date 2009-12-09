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

	$standard_title = 'Schowek';
	$no_list = '<tr><td colspan="4">Nie znaleziono żadnych skrzynek w schowku</td></tr>';

	$list_e = '<tr><td><a href="viewcache.php?cacheid={urlencode_cacheid}">{cachename}</a></td><td>&nbsp;</td><td nowrap style="text-align:center;">{lastfound}</td><td nowrap style="text-align:center;">[<a href="removelist.php?cacheid={cacheid}&target=mylist.php">Usuń</a>]</td></tr>';
	$list_o = '<tr bgcolor=\'#eeeeee\'><td><a href="viewcache.php?cacheid={urlencode_cacheid}">{cachename}</a></td><td>&nbsp;</td><td nowrap style="text-align:center;">{lastfound}</td><td nowrap style="text-align:center;">[<a href="removelist.php?cacheid={cacheid}&target=mylist.php">Usuń</a>]</td></tr>';
	$no_found_date = '---';
	$print_delete_list = '<tr><td colspan="2">&nbsp;</td><td nowrap style="text-align:center;">[<a href="printcache.php?source=mylist">Wydrukuj wszystkie</a>]</td><td nowrap style="text-align:center;">[<a href="removelist.php?cacheid=all&target=mylist.php">Usuń wszystkie</a>]</td></tr>';
	$export_list = '
				<tr>
					<td colspan="4">Pobież dane skrzynek ze schowka:
						<a href="search.php?searchto=searchbylist&showresult=1&f_inactive=0&f_ignored=0&f_userfound=0&f_userowner=0&f_watched=0&count=max&output=gpx" title="GPS Exchange Format .gpx">GPX</a>
						<a href="search.php?searchto=searchbylist&showresult=1&f_inactive=0&f_ignored=0&f_userfound=0&f_userowner=0&f_watched=0&count=max&output=gpxgc" title="GPS Exchange Format (Groundspeak) .gpx">GPX GC</a>
						<a href="search.php?searchto=searchbylist&showresult=1&f_inactive=0&f_ignored=0&f_userfound=0&f_userowner=0&f_watched=0&count=max&output=loc" title="Waypoint .loc">LOC</a>
						<a href="search.php?searchto=searchbylist&showresult=1&f_inactive=0&f_ignored=0&f_userfound=0&f_userowner=0&f_watched=0&count=max&output=kml" title="Google Earth .kml">KML</a>
						<a href="search.php?searchto=searchbylist&showresult=1&f_inactive=0&f_ignored=0&f_userfound=0&f_userowner=0&f_watched=0&count=max&output=ov2" title="TomTom POI .ov2">OV2</a>
						<a href="search.php?searchto=searchbylist&showresult=1&f_inactive=0&f_ignored=0&f_userfound=0&f_userowner=0&f_watched=0&count=max&output=ovl" title="TOP50-Overlay .ovl">OVL</a>
						<a href="search.php?searchto=searchbylist&showresult=1&f_inactive=0&f_ignored=0&f_userfound=0&f_userowner=0&f_watched=0&count=max&output=txt" title="Tekst .txt">TXT</a>
						<a href="search.php?searchto=searchbylist&showresult=1&f_inactive=0&f_ignored=0&f_userfound=0&f_userowner=0&f_watched=0&count=max&output=wpt" title="Oziexplorer .wpt">WPT</a>
						<a href="search.php?searchto=searchbylist&showresult=1&f_inactive=0&f_ignored=0&f_userfound=0&f_userowner=0&f_watched=0&count=max&output=uam" title="AutoMapa .uam">UAM</a>
						<br />
						<span class="help">Pobierając dane z OpenCaching PL akceptujesz <a href=http://wiki.opencaching.pl/index.php/Regulamin_OC_PL>warunki ich użycia</a>.</span>
					</td>
				</tr>';
?>
