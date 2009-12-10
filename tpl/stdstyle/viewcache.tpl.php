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
?>

<!-- Text container -->

		<div class="content2-container line-box">

			<div class="">

				<div class="nav4">
<?

					if ($usr == false) 
					{
						echo '<span class="notlogged-cacheview">'.tr('cache_logged_required').'</span>';
					}
					// cachelisting
					$clidx = mnu_MainMenuIndexFromPageId($menu, "cachelisting");
					if( $menu[$clidx]['title'] != '' )
					{
						echo '<ul id="cachemenu">';
						$menu[$clidx]['visible'] = false;
						echo '<li class="title" ';
						echo '>'.$menu[$clidx]["title"].'</li>';
						mnu_EchoSubMenu($menu[$clidx]['submenu'], $tplname, 1, false);
						echo '</ul>';
					}
					//end cachelisting
?>
				</div>
				<div class="content2-container-2col-left" style="width:60px; clear: left;">

					<div><img src="{icon_cache}" border="0" width="32" height="32" align="left" alt="{cachetype}" title="{cachetype}"/></div>
					<div>{difficulty_icon_diff}</div><div>{difficulty_icon_terr}</div>


				
				</div>
					<div class="content2-container-2col-left" id="cache_name_block">
					<span class="content-title-noshade-size5">{cachename}</span><br />
					<p class="content-title-noshade-size1">&nbsp;{short_desc}</p>
					<p>{{created_by}} <a href="viewprofile.php?userid={userid_urlencode}">{owner_name}</a></p>
					{event_attendance_list}
					</div>


			</div>
		</div>


<!-- End Text Container -->
<!-- Text container -->
			<div class="content2-container">
				<div class="content2-container-2col-left" style="width:400px;">
					<p class="content-title-noshade-size3">
						<img src="tpl/stdstyle/images/blue/compas.png"  width="22" height="22" border="0" alt="" title="" align="left"/>
						<b>{coords}</b> <span class="content-title-noshade-size0">(WGS84)</span><br />
					</p>
					<p>
						<font size="1">{coords_other}</font><br />
						{{size}}: {cachesize}<br />
						{hidetime_start}{{time}}: {search_time}&nbsp;&nbsp;{{length}}: {way_length}<br />{hidetime_end}
						{{status_label}}: {status}<br />
						{{score_label}}: <b><font color="{scorecolor}">{score}</font></b><br />
						{{date_hidden_label}}: {hidden_date}<br />
						{{date_created_label}}: {date_created}<br />
						{{last_modified_label}}: {last_modified}<br />
						{{waypoint}}: {oc_waypoint}<br />
						{hidelistingsites_start}{{listed_also_on}}: {listed_on}<br />{hidelistingsites_end}
					</p>
					<?php
global $usr, $lang, $hide_coords;			

// uśpiony mechanizm ukrywania niektórych danych dla niezalogowanych
if ($usr == false && $hide_coords)
{
	echo "";
}
else
{

						echo "<font size=\"2\"><a href=\"#\" onclick=\"javascript:window.open('garmin.php?lat="; ?>{latitude}<?php echo "&amp;long="; ?>{longitude}<?php echo "&amp;wp="; ?>{oc_waypoint}<?php echo "&amp;name="; ?>{cachename}<?php echo "&amp;popup=y','Send_To_GPS','width=450,height=160,resizable=no,scrollbars=0')\"><input type=\"button\" name=\"SendToGPS\" value=\""; ?>{{send_to_gps}}<?php echo "\" id=\"SendToGPS\"></a></font><p></p>";
} ?>

				</div>
				<div class="content2-container-2col-right" style="width:310px;" align="right">
					<?php
					if ($usr == false && $hide_coords)
							{
					?>
					{map_msg}
					<?php 
							}
							else
							{
					?>
						<div class="content2-container-2col-left" style="width: 140px; height: 170px;" align="left">
						<p><br/><br/>
							<nobr>{found_icon} {founds} {found_text}</nobr><br />
							<nobr>{notfound_icon} {notfounds} {notfound_text}</nobr><br />
							<nobr>{note_icon} {notes} {{comments}}</nobr><br />
							<nobr>{vote_icon} {votes_count} x {{scored}}</nobr><br />
							<nobr>{watch_icon} {watcher} {{watchers}}</nobr><br />
							<nobr>{visit_icon} {visits} {{visitors}}</nobr><br />
							{rating_stat}
							</p>
						</div>
						<div id="map" class="content2-container-2col-right" style="width: 170px; height: 170px;">
							<img src="http://maps.google.com/staticmap?center={latitude},{longitude}&amp;zoom=8&amp;size=170x170&amp;maptype=terrain&amp;key={googlemap_key}&amp;sensor=false&amp;markers={latitude},{longitude},blue{typeLetter}&amp;format=png" alt="mapa"/>
						</div>
					<?php
							}
					if ($usr == false && $hide_coords)
							{
					echo "";
							}
							else
							{
					echo "<b>Dostępne mapy:</b>
											<a target=\"_blank\" href='cachemap3.php?lat=";?>{latitude}<?php echo "&amp;lon=";?>{longitude}<?php echo "&amp;cacheid=";?>{cacheid}<?php echo "&amp;inputZoom=14'>Opencaching.pl</a>, 
											<a href=\"http://mapa.szukacz.pl/?n=";?>{latitude}<?php echo "&amp;e=";?>{longitude}<?php echo "&amp;t=Skrzynka Geocache\" target=\"_blank\">AutoMapa</a>, 
											<a href=\"http://www.mapquest.com/maps/map.adp?latlongtype=decimal&amp;latitude=";?>{latitude}<?php echo "&amp;longitude=";?>{longitude}<?php echo "\" target=\"_blank\">Mapquest</a>, 
											<a href=\"http://maps.google.com/maps?q=";?>{latitude}<?php echo "+";?>{longitude}<?php echo "\" target=\"_blank\">Google&nbsp;Maps</a>, 
											<a target=\"_blank\" href='http://mapa.ump.waw.pl/ump-www/?zoom=14&amp;lat=";?>{latitude}<?php echo "&amp;lon=";?>{longitude}<?php echo "&amp;layers=B00000T&amp;mlat=";?>{latitude}<?php echo "&amp;mlon=";?>{longitude}<?php echo "'>UMP</a>, <a target=\"_blank\" href='http://www.zumi.pl/namapie.html?&amp;lat=";?>{latitude}<?php echo "&amp;long=";?>{longitude}<?php echo "&amp;type=1&amp;scale=4'>Zumi</a>";
								
					} 
					?>				
				</div>
			</div>
<!-- End Text Container -->
	
<!-- Text container -->
					{cache_attributes_start}
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/attributes.png" width="32" height="32" style="vertical-align:middle" border="0" alt="enc"/>
					{{cache_attributes_label}}
				</p>
			</div>
			<div class="content2-container">
				<p>
					{cache_attributes}{password_req}
				</p>
			</div>
<div class="notice" style="width:500px;min-height:24px;height:auto;">{{attributes_edit_hint}} {{attributes_desc_hint}}</div>
					{cache_attributes_end}
<!-- End Text Container -->
<!-- Text container -->
			{start_rr_comment}
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					
					<img src="tpl/stdstyle/images/blue/crypt.png" width="32" height="32" style="vertical-align:middle" border="0" alt="desc"/>
					{{rr_comment_label}}
				</p>
				</div>
				<div class="content2-container">
				<p><br/>
				{rr_comment}
				</p>
			</div>
			{end_rr_comment}
<!-- End Text Container -->
<!-- Text container -->
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/describe.png" width="32" height="32" style="vertical-align:middle" border="0" alt="desc"/>
					{{description}}&nbsp;&nbsp;
					{desc_langs}&nbsp;{add_rr_comment}&nbsp;{remove_rr_comment}
				</p></div>
				<div class="content2-container">
				<div id='branding'>{branding}</div>
				<div id="description">
					<p>
						{desc}
					</p>
				</div>
			</div>
<!-- End Text Container -->
<!-- Text container -->
{hidehint_start}
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/crypt.png" width="32" height="32" style="vertical-align:middle" border="0" alt="enc"/>
					<b>{{additional_hints}}</b>&nbsp;&nbsp;
					{decrypt_link_start}
					<img src="tpl/stdstyle/images/blue/decrypt.png" width="32" height="32" style="vertical-align:middle" border="0" alt="enc"/>
					{decrypt_link}
					{decrypt_link_end}
					<br/></div><div class="content2-container">
					<div id='hint' style="float:left">
					<p>
						{hints}
					</p>
					</div>
					<div style="width:200px;align:right;float:right">
						{decrypt_table_start}
						<font face="Courier" size="2" style="font-family : 'Courier New', FreeMono, Monospace;">A|B|C|D|E|F|G|H|I|J|K|L|M</font>
						<font face="Courier" size="2" style="font-family : 'Courier New', FreeMono, Monospace;">N|O|P|Q|R|S|T|U|V|W|X|Y|Z</font>
						{decrypt_table_end}
					</div>
				</p>
			</div>
{hidehint_end}
<!-- End Text Container -->
<!-- Text container -->
{geokrety_begin}
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/travelbug.png" width="32" height="32" style="vertical-align:middle" border="0" alt="geokrety"/>
					Geokrety
				</p></div>
				<div class="content2-container">
				<p>
					{geokrety_content}
				</p>
			</div>
{geokrety_end}
<!-- End Text Container -->
<!-- Text container -->
{hidepictures_start}
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/picture.png" width="32" height="32" style="vertical-align:middle" border="0" alt="images"/>
					{{images}}
				</p></div>
				<div class="content2-container">
				<p>
					{pictures}
				</p>
			</div>
{hidepictures_end}
<!-- End Text Container -->
<!-- Text container -->
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<!-- End Text Container -->
					<img src="tpl/stdstyle/images/blue/utils.png" width="32" height="32" style="vertical-align:middle" border="0" title="" alt="utilities"/>&nbsp;{{utilities}}
				</p></div>
				<div class="content2-container">
			<p>
			- {{search_geocaches_nearby}}<?php echo ":
			<a href=\"search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;latNS=";?>{latNS}<?php echo "&amp;lat_h="; ?>{lat_h}<?php echo "&amp;lat_min="; ?>{lat_min}<?php echo "&amp;lonEW="; ?>{lonEW}<?php echo "&amp;lon_h="; ?>{lon_h}<?php echo "&amp;lon_min="; ?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">";?>{{all_geocaches}}<?php echo "</a>&nbsp;
			<a href=\"search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=1&amp;f_userfound=1&amp;f_inactive=1&amp;latNS="; ?>{latNS}<?php echo "&amp;lat_h="; ?>{lat_h}<?php echo "&amp;lat_min="; ?>{lat_min}<?php echo "&amp;lonEW="; ?>{lonEW}<?php echo "&amp;lon_h=";?>{lon_h}<?php echo "&amp;lon_min=";?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">";?>{{searchable}}<?php echo "</a>&nbsp;&nbsp;&nbsp;<br/>"; ?>- {{find_geocaches_on}}<?php echo ":&nbsp;<b>
			<a href=\"http://www.geocaching.com/seek/nearest.aspx?origin_lat=";?>{latitude}<?php echo "&amp;origin_long=";?>{longitude}<?php echo "&amp;dist=100&amp;submit8=Submit\">Geocaching.com</a>&nbsp;&nbsp;&nbsp;
        		<a href=\"http://geocaching.gpsgames.org/cgi-bin/ge.pl?basic=yes&amp;download=Google+Maps&amp;zoom=8&amp;lat_1=";?>{latitude}<?php echo "&amp;lon_1=";?>{longitude}<?php echo "\">GPSgames.org</a>&nbsp;&nbsp;&nbsp;
        		<a href=\"http://www.opencaching.cz/search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;country=&amp;cachetype=&amp;cache_attribs=&amp;cache_attribs_not=7&amp;latNS=";?>{latNS}<?php echo "&amp;lat_h=";?>{lat_h}<?php echo "&amp;lat_min=";?>{lat_min}<?php echo "&amp;lonEW=";?>{lonEW}<?php echo "&amp;lon_h=";?>{lon_h}<?php echo "&amp;lon_min=";?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">OpenCaching.cz</a>&nbsp;&nbsp;&nbsp;
        		<a href=\"http://www.opencaching.de/search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;country=&amp;cachetype=&amp;cache_attribs=&amp;cache_attribs_not=7&amp;latNS=";?>{latNS}<?php echo "&amp;lat_h=";?>{lat_h}<?php echo "&amp;lat_min=";?>{lat_min}<?php echo "&amp;lonEW=";?>{lonEW}<?php echo "&amp;lon_h=";?>{lon_h}<?php echo "&amp;lon_min=";?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">OpenCaching.de</a></b></p>
						<p>
			"; ?>- {{download_as_file}}<?php echo ":
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=gpx\" title=\"GPS Exchange Format .gpx\">GPX</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=gpxgc\" title=\"GPS Exchange Format (Groundspeak) .gpx\">GPX GC</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=loc\" title=\"Waypoint .loc\">LOC</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=kml\" title=\"Google Earth .kml\">KML</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=ov2\" title=\"TomTom POI .ov2\">OV2</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=ovl\" title=\"TOP50-Overlay .ovl\">OVL</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=txt\" title=\"Tekst .txt\">TXT</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=wpt\" title=\"Oziexplorer .wpt\">WPT</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=uam\" title=\"AutoMapa .uam\">UAM</a>
			<br />
			<div class=\"notice buffer\">"; ?> {{accept_terms_of_use}}<?php echo "</div>
";
	 ?>
				</p>
			</div>
<!-- Text container -->
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/logs.png" width="32" height="32" style="vertical-align:middle" border="0" alt="logs"/>
					{{log_entries}}
					&nbsp;&nbsp;
					{found_icon} {founds}x
					{notfound_icon} {notfounds}x
					{note_icon} {notes}x
					&nbsp;&nbsp;
					{viewlogs}
				</p>
			</div>
			<div class="content2-container">
				<p>
					{logs}
				</p>
			</div>
<!-- End Text Container -->
