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
		<td class="content2-pagetitle">
			<table class="null">
				<tr>
					<td valign="top" width="70"><img src="{icon_cache}" border="0" width="32" height="32" align="left" alt="{cachetype}" title="{cachetype}">
						{difficulty_icon_diff}
						{difficulty_icon_terr}
					</td>
					<td valign="top">
						Skrzynka <b>{cachename}</b> założona przez <b>{owner_name}</b><br>
						Krótki opis: {short_desc}
						<font size="1"><br></font>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top" width="500">
						</span><font size="3"><b>{coords}</b></font> <font size="1">(WGS84)</font><br />
						{size}: {cachesize}<br />
						{hidetime_start}{time}: {search_time}&nbsp;&nbsp;{length}: {way_length}<br />{hidetime_end}
						{status_label}: {status}<br />
						{date_hidden_label}: {hidden_date}<br />
						{date_created_label}: {date_created}<br />
						{last_modified_label}: {last_modified}<br />
						{waypoint}: {oc_waypoint}<br />
						{hidelistingsites_start}{listed_also_on}: {listed_on}<br />{hidelistingsites_end}
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td style="vertical-align: top"><br><br><img src="tpl/stdstyle/images/blue/attributes.png" width="32" height="32" align="middle" border="0">Atrybuty skrzynki:&nbsp;</td>								
								<td>{cache_attributes}{password_req}</td>								
							</tr>
						</table>
					</td>
					<td valign="top" nowrap>
						<script type="text/javascript">
						//<![CDATA[
						function load() {
							if (GBrowserIsCompatible()) {
								var map = new GMap2(document.getElementById("map"));
								map.setCenter(new GLatLng({latitude},{longitude}), 12);
								var point = new GLatLng({latitude},{longitude});
								map.addOverlay(new GMarker(point));
								map.addControl(new GSmallZoomControl());
							}
						}
						//]]>
						</script>
						<div id="map" style="width: 600px; height: 400px;"></div>
				</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="header-small">
			<img src="tpl/stdstyle/images/blue/describe.png" width="32" height="32" align="middle" border="0">
			<b>Opis</b>&nbsp;&nbsp;<span style="font-weight: 400;">{desc_langs}</span>&nbsp;
		</td>
	</tr>
	<tr>
		<td>
			<div id="description">{desc}</div>
		</td>
	</tr>
	{start_rr_comment}
	<tr>
		<td class="header-small">
			<img src="tpl/stdstyle/images/blue/crypt.png" width="32" height="32" style="vertical-align:middle" border="0">
			<b>{rr_comment_label}</b>
		</td>
	</tr>
	<tr>
		<td>
			{rr_comment}
		</td>
	</tr>
	{end_rr_comment}
	{hidehint_start}
	<tr>
		<td class="header-small">
			<img src="tpl/stdstyle/images/blue/crypt.png" width="32" height="32" align="middle" border="0">
			<b>Odnosnik kodowania</b>&nbsp;&nbsp;
			{decrypt_link_start}
			<img src="tpl/stdstyle/images/blue/decrypt.png" width="32" height="32" align="middle" border="0">
			{decrypt_link}
			{decrypt_link_end}
			<br>
		</td>
	</tr>
	<tr>
		<td bgcolor="#ffffff" style="padding-left : 5px; padding-right : 5px;">
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
			<img src="tpl/stdstyle/images/blue/picture.png" width="32" height="32" align="middle" border="0">
			<b>Obrazki/Zdjęia</b> &nbsp;&nbsp;
			&nbsp;
		</td>
	</tr>
	<tr>
		<td>
		<br>
		{pictures}
		</td>
	</tr>
	{hidepictures_end}
	<tr><td class="spacer"><br></td></tr>

	<tr>
		<td height="26" valign="middle" class="header-small">
			<img src="tpl/stdstyle/images/blue/logs.png" width="32" height="32" align="middle" border="0">
			<b>Wpisy do LOGu</b><span style="font-weight: 400;">&nbsp;&nbsp;
			<img src="tpl/stdstyle/images/log/16x16-found.png" width="16" height="16" align="middle" border="0" align="left" alt="znaleziony" title="znaleziona"> {founds}x
			<img src="tpl/stdstyle/images/log/16x16-dnf.png" width="16" height="16" align="middle" border="0" align="left" alt="Nie znaleziony" title="nie znaleziona"> {notfounds}x
			<img src="tpl/stdstyle/images/log/16x16-note.png" width="16" height="16" align="middle" border="0" align="left" alt="Komentarze" title="skomentowana"> {notes}x </span>
			&nbsp;&nbsp;
			<span style="font-weight:400">{viewlogs_last}&nbsp;&nbsp;{viewlogs}</span>
		</td>
	</tr>
	<tr><td class="spacer"><br></td></tr>
	{logs}
</table>
