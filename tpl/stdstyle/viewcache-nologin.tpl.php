<?php
/***************************************************************************
											./tpl/stdstyle/viewcache.tpl.php
															-------------------
		begin                : June 24 2004
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

	 view a cache

	 template replacements:

			cachename
			owner_name
			coords
			country
			cachetype
			cachesize
			hidden_date
			status
			date_created
			difficulty
			terrain
			desc_langs
			short_desc
			desc
			founds
			notfounds
			notes
			logs
			cacheid_urlencode
			hints
			cache_watcher
			userid_urlencode
			cache_log_pw
			longitude
			latitude
			search_time
			way_length

 ****************************************************************************/
?>
<table class="content">
	<tr>
		<td class="header">
			<table class="null">
				<tr>
					<td valign="top" width="70"><img src="{icon_cache}" border="0" width="32" height="32" align="left" alt="{cachetype}" title="{cachetype}">
						{difficulty_icon_diff}
						{difficulty_icon_terr}
					</td>
					<td valign="top">Skrzynkę <font size="4"><b>{cachename}</b></font><span style="font-weight:400">&nbsp;założył <a href="viewprofile.php?userid={userid_urlencode}">{owner_name}</a></span><br />
					{short_desc}
					{event_attendance_list}
					</td>
					<td valign="top" width="120" nowrap="1">
						{log}
						{watch}
					</td>
					<td valign="top" width="120" nowrap="1">
						{print}
						{edit}
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td><br><img src='tpl/stdstyle/images/description/22x22-location.png'  width='22' height='22' border='0' alt='' title='' align="left">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>					<td valign="top" width="360">

						</span><font size="3"><b>{coords}</b></font> <font size="1">(WGS84)</font><br />
						<font size="1"><a href="#" onClick="javascript:window.open('http://www.opencaching.de/coordinates.php?lat={latitude}&lon={longitude}&popup=y&wp={oc_waypoint}','Współrzędne w innych systemach','width=240,height=334,resizable=no,scrollbars=0')">Współrzędne w innych systemach</a></font><br />
						Wielkość: {cachesize}<br />
						{hidetime_start}Czas: {search_time}&nbsp;&nbsp;Długość drogi: {way_length}<br />{hidetime_end}
						Status: {status}<br />
						Data ukrycia: {hidden_date}<br />
						Data założenia: {date_created}<br />
						Ostatnia modyfikacja: {last_modified}<br />
						Waypoint: {oc_waypoint}<br />
						{hidelistingsites_start}Zarejestrowana także na: {listed_on}<br />{hidelistingsites_end}
					</td>
					<td valign="top" width="170">
						<img src="tpl/stdstyle/images/log/16x16-found.png" width="16" height="16" border="0"> {founds} {found_text}<br />
						<nobr><img src="tpl/stdstyle/images/log/16x16-dnf.png" width="16" height="16" border="0"> {notfounds} {notfound_text}</nobr><br />
						<img src="tpl/stdstyle/images/log/16x16-note.png" width="16" height="16" border="0"> {notes} komentarze<br />
						<img src="tpl/stdstyle/images/action/16x16-watch.png" width="16" height="16" border="0"> {watcher} obserwatorów<br />
						<img src="tpl/stdstyle/images/description/16x16-visitors.png" width="16" height="16" border="0"> {visits} odwiedzających<br />
						{rating_stat}
						<br />
					</td>
					<td valign="top" width="80" nowrap>
						<span style="background-color : #E6E2E6;"><b>{country}</b></span><br>
						<a href="http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude={latitude}&longitude={longitude}" target="_blank">Mapquest</a><br>
						<a href="http://maps.google.com/maps?q={latitude}+{longitude}" target="_blank">Google&nbsp;Maps</a><br>

					</td>
					<td valign="top" nowrap><center>
						<script type="text/javascript">
						//<![CDATA[
						function load() {
							if (GBrowserIsCompatible()) {
								var map = new GMap2(document.getElementById("map"));
								map.setCenter(new GLatLng({latitude},{longitude}), 7);
								var point = new GLatLng({latitude},{longitude});
								map.addOverlay(new GMarker(point));
								map.addControl(new GSmallZoomControl());
							}
						}
						//]]>
						</script>

						<div id="map" style="width: 200px; height: 200px;"></div>
					</td>



				</tr>
			</table>
		</td>
	</tr>
	{cache_attributes_start}
	<tr>
		<td class="header-small">
			<img src="tpl/stdstyle/images/description/22x22-encrypted.png" width="22" height="22" style="vertical-align:middle" border="0">
			<b>Atrybuty skrzynki</b><br />
		</td>
	</tr>
	<tr>
		<td valign="top">
			{cache_attributes}
		</td>
	</tr>
	<tr><td class="spacer"><br /></td></tr>
	{cache_attributes_end}

	<tr>
		<td class="header-small">
			<img src="tpl/stdstyle/images/description/22x22-description.png" width="22" height="22" style="vertical-align:middle" border="0">
			<b>Opis</b>&nbsp;&nbsp;
			<span style="font-weight: 400;">{desc_langs}
			</span>
	&nbsp;
		</td>
	</tr>
	<tr>
		<td><p>
		{desc}</p>
		</td>
	</tr>
	{hidehint_start}
	<tr>
		<td class="header-small">
			<img src="tpl/stdstyle/images/description/22x22-encrypted.png" width="22" height="22" style="vertical-align:middle" border="0">
			<b>Dodatkowe informacje kodowane</b>&nbsp;&nbsp;
			{decrypt_link_start}
			<img src="tpl/stdstyle/images/action/16x16-encrypt.png" width="16" height="16" style="vertical-align:middle" border="0">
			{decrypt_link}
			{decrypt_link_end}
			<br>
		</td>
	</tr>
	<tr>
		<td style="padding-left : 5px; padding-right : 5px;">
			<table width="100%" cellspacing="0" border="0" cellpadding="0">
				<tr>
					<td valign="top">
						<p>
						{hints}
						</p>
					</td>
					<td width="100" valign="top">
						{decrypt_table_start}<br />
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td style="border-bottom-color : #808080; border-bottom-style : solid; border-bottom-width : 1px;" nowrap="1">
									<font face="Courier" style="font-family : 'Courier New', FreeMono, Monospace;">A|B|C|D|E|F|G|H|I|J|K|L|M</font>
								</td>
							</tr>
							<tr>
								<td>
									<font face="Courier" style="font-family : 'Courier New', FreeMono, Monospace;">N|O|P|Q|R|S|T|U|V|W|X|Y|Z</font><br><br>
								</td>
							</tr>
						</table>
						{decrypt_table_end}
					</td>
				</tr>
			</table>
		</td>
	</tr>
	{hidehint_end}
	{hidepictures_start}
	<tr>
		<td class="header-small">
			<img src="tpl/stdstyle/images/description/22x22-image.png" width="22" height="22" style="vertical-align:middle" border="0">
			<b>Obrazki/Zdjęcia</b> &nbsp;&nbsp;
			&nbsp;
		</td>
	</tr>
	<tr>
		<td>
		{pictures}
		</td>
	</tr>
	{hidepictures_end}
	<tr><td class="spacer"><br></td></tr>

	<tr>
		<td class="header-small" valign="middle">
			<img src="tpl/stdstyle/images/description/22x22-utility.png" width="22" height="22" style="vertical-align:middle" border="0" title="">
			Użyteczne
		</td>
	</tr>
	<tr><td class="spacer"><br></td></tr>
	<tr>
		<td valign="top">
			Szukaj skrzynek w pobliżu (150 km promień) na OC PL:
			<a href="search.php?searchto=searchbydistance&showresult=1&expert=0&output=HTML&sort=bydistance&f_userowner=0&f_userfound=0&f_inactive=1&latNS={latNS}&lat_h={lat_h}&lat_min={lat_min}&lonEW={lonEW}&lon_h={lon_h}&lon_min={lon_min}&distance=150&unit=km">wszystkie</a>&nbsp;
			<a href="search.php?searchto=searchbydistance&showresult=1&expert=0&output=HTML&sort=bydistance&f_userowner=1&f_userfound=1&f_inactive=1&latNS={latNS}&lat_h={lat_h}&lat_min={lat_min}&lonEW={lonEW}&lon_h={lon_h}&lon_min={lon_min}&distance=150&unit=km">znalezione</a>&nbsp;&nbsp;&nbsp;<br><font color=blue>Pokaż najbliższe skrzynki na:&nbsp;<b>
			<a href="http://www.geocaching.com/seek/nearest.aspx?origin_lat={latitude}&origin_long={longitude}&submit3=Submit">Geocaching.com</a>&nbsp;&nbsp;&nbsp;
        		<a href="http://geocaching.gpsgames.org/cgi-bin/ge.pl?basic=yes&download=Google+Maps&zoom=8&lat_1={latitude}&lon_1={longitude}">GPSgames.org</a></font></b>
			</td>
	</tr>
	<tr>
		<td>Pobierz dane:
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&startat=0&cacheid={cacheid_urlencode}&output=gpx" title="GPS Exchange Format .gpx">GPX</a>
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&startat=0&cacheid={cacheid_urlencode}&output=loc" title="Waypoint .loc">LOC</a>
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&startat=0&cacheid={cacheid_urlencode}&output=kml" title="Google Earth .kml">KML</a>
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&startat=0&cacheid={cacheid_urlencode}&output=ov2" title="TomTom POI .ov2">OV2</a>
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&startat=0&cacheid={cacheid_urlencode}&output=ovl" title="TOP50-Overlay .ovl">OVL</a>
			<a href="search.php?searchto=searchbycacheid&showresult=1&f_inactive=0&startat=0&cacheid={cacheid_urlencode}&output=txt" title="Tekst .txt">TXT</a>
			<br />
			<span class="help">Pobierając dane z OpenCaching PL akceptujesz <a href="articles.php?page=impressum#tos">warunki ich użycia</a>.</span>
		</td>
	</tr>
	<tr><td class="spacer"><br /></td></tr>

	<tr>
		<td class="header-small" height="26" valign="middle" style="padding-left: 5px; padding-right: 5px;">
			<div><img src="tpl/stdstyle/images/description/22x22-logs.png" width="22" height="22" style="vertical-align:middle" border="0">
			<b>Wpisy w LOGu</b><span style="font-weight: 400;">&nbsp;&nbsp;
			<img src="tpl/stdstyle/images/log/16x16-found.png" width="16" height="16" align="middle" border="0" align="left" alt="Znaleziona" title="Znaleziona"> {founds}x
			<img src="tpl/stdstyle/images/log/16x16-dnf.png" width="16" height="16" align="middle" border="0" align="left" alt="Nie znaleziona" title="Nie znaleziona"> {notfounds}x
			<img src="tpl/stdstyle/images/log/16x16-note.png" width="16" height="16" align="middle" border="0" align="left" align="Komentarz" title="Komentarz"> {notes}x </span>
			&nbsp;&nbsp;
			{viewlogs}
			</div>
			{log}
		</td>
	</tr>
	<tr><td class="spacer"><br></td></tr>
	{logs}

	{viewlogs_start}
	<tr>
		<td class="header-small">
			<span style="font-weight: 400;">{viewlogs}</span>
		</td>
	</tr>
	{viewlogs_end}
</table>
