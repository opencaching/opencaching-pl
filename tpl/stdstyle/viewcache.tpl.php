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

					<div><img src="{icon_cache}" class="icon32" id="viewcache-cacheicon" alt="{cachetype}" title="{cachetype}"/></div>
					<div>{difficulty_icon_diff}</div><div>{difficulty_icon_terr}</div>
					<div>{cache_stats}</div>

				
				</div>
					<div class="content2-container-2col-left" id="cache_name_block">
					<span class="content-title-noshade-size5">{cachename}</span><br />
					<p class="content-title-noshade-size1">&nbsp;{short_desc}</p>
					<p>{{hidden_by}} <a href="viewprofile.php?userid={userid_urlencode}">{owner_name}</a></p>
					{event_attendance_list}
					</div>


			</div>
		</div>


<!-- End Text Container -->
<!-- Text container -->
			<div class="content2-container">
				<div class="content2-container-2col-left" id="viewcache-baseinfo">
					<p class="content-title-noshade-size3">
						<img src="tpl/stdstyle/images/blue/compas.png" class="icon32" alt="" title="" />
						<b>{coords}</b> <span class="content-title-noshade-size0">(WGS84)</span><br />
					</p>
					<p>
						<font size="1">{coords_other}</font><br />
<!--						{{location}}:<b><span style="color: rgb(88,144,168)"> {kraj} {dziubek} {woj}</span></b><br />  -->
						{{size}}: {cachesize}<br />
						{hidetime_start}{{time}}: {search_time}&nbsp;&nbsp;{{length}}: {way_length}<br />{hidetime_end}
						{{status_label}}: {status}<br />
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

						echo "<a class=\"send-to-gps\" href=\"#\" onclick=\"javascript:window.open('garmin.php?lat="; ?>{latitude}<?php echo "&amp;long="; ?>{longitude}<?php echo "&amp;wp="; ?>{oc_waypoint}<?php echo "&amp;name="; ?>{cachename}<?php echo "&amp;popup=y','Send_To_GPS','width=450,height=160,resizable=no,scrollbars=0')\"><input type=\"button\" name=\"SendToGPS\" value=\""; ?>{{send_to_gps}}<?php echo "\" id=\"SendToGPS\"/></a><p>&nbsp;</p>";
} ?>

				</div>
				<div class="content2-container-2col-right" id="viewcache-maptypes">
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
						<div class="content2-container-2col-left" id="viewcache-numstats">
						<p><br/><br/>
							{found_icon} {founds} {found_text}<br />
							{notfound_icon} {notfounds} {notfound_text}<br />
							{note_icon} {notes} {{comments}}<br />
							{watch_icon} {watcher} {{watchers}}<br />
							{visit_icon} {visits} {{visitors}}<br />
							{vote_icon} {votes_count} x {{scored}}<br />
							{score_icon} {{score_label}}: <b><font color="{scorecolor}">{score}</font></b><br />
							{rating_stat}
							</p>
						</div>
						<div id="viewcache-map" class="content2-container-2col-right"><div class="img-shadow">
							<img src="http://maps.google.com/staticmap?center={latitude},{longitude}&amp;zoom=8&amp;size=170x170&amp;maptype=terrain&amp;key={googlemap_key}&amp;sensor=false&amp;markers={latitude},{longitude},blue{typeLetter}&amp;format=png" longdesc="ifr::cachemap-mini.php?inputZoom=14&amp;lat={latitude}&amp;lon={longitude}&amp;cacheid={cacheid}::480::385" onclick="enlarge(this);" alt="{{map}}" />
						</div></div>
					<?php
							}
					if ($usr == false && $hide_coords)
							{
					echo "";
							}
							else
							{
					echo "<b>{{available_maps}}:</b>
											<a target=\"_blank\" href='cachemap3.php?lat=";?>{latitude}<?php echo "&amp;lon=";?>{longitude}<?php echo "&amp;cacheid=";?>{cacheid}<?php echo "&amp;inputZoom=14'>Opencaching.pl</a>,
											<a target=\"_blank\" href='http://mapa.ump.waw.pl/ump-www/?zoom=14&amp;lat=";?>{latitude}<?php echo "&amp;lon=";?>{longitude}<?php echo "&amp;layers=B00000T&amp;mlat=";?>{latitude}<?php echo "&amp;mlon=";?>{longitude}<?php echo "'>UMP</a>, <a target=\"_blank\" href='http://www.zumi.pl/namapie.html?&amp;lat=";?>{latitude}<?php echo "&amp;long=";?>{longitude}<?php echo "&amp;type=1&amp;scale=4'>Zumi</a>,<br/>											
											<a href=\"http://maps.google.com/maps?q=";?>{latitude}<?php echo "+";?>{longitude}<?php echo "\" target=\"_blank\">Google&nbsp;Maps</a>, 
											<a href=\"http://mapa.szukacz.pl/?n=";?>{latitude}<?php echo "&amp;e=";?>{longitude}<?php echo "&amp;t=Skrzynka%20Geocache\" target=\"_blank\">AutoMapa</a>";
								
					} 
					?>				
				</div>
			</div>
<!-- End Text Container -->
	
<!-- Text container -->
					{cache_attributes_start}
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/attributes.png" class="icon32" alt="" />
					{{cache_attributes_label}}
				</p>
			</div>
			<div class="content2-container">
				<p>
					{cache_attributes}{password_req}
				</p>
			</div>
<div class="notice" id="viewcache-attributesend">{{attributes_desc_hint}}</div>
					{cache_attributes_end}
<!-- End Text Container -->
<!-- Text container -->
			{start_rr_comment}
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					
					<img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="" />
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
					<img src="tpl/stdstyle/images/blue/describe.png" class="icon32" alt="" />
					{{descriptions}}&nbsp;&nbsp;
					{desc_langs}&nbsp;{add_rr_comment}&nbsp;{remove_rr_comment}
				</p></div>
				<div class="content2-container">
				<div id='branding'>{branding}</div>

					<div style="font-size: 125%; line-height: 0.5cm;">
						{desc}
					</div>
			</div>
<!-- End Text Container -->
<!-- Text container -->
{hidehint_start}
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="" />
					<b>{{additional_hints}}</b>&nbsp;&nbsp;
					<span id="decrypt-info">
					{decrypt_link_start}
					<img src="tpl/stdstyle/images/blue/decrypt.png" class="icon32" alt="" />
					{decrypt_link}
					{decrypt_link_end}
					</span>
					<br/>

				</p>
			</div>
					<div class="content2-container">
					<p id="decrypt-hints">   
							{hints}
					</p>  

					<div style="width:200px;align:right;float:right">
						{decrypt_table_start}
						<font face="Courier" size="2" style="font-family : 'Courier New', FreeMono, Monospace;">A|B|C|D|E|F|G|H|I|J|K|L|M</font>
						<font face="Courier" size="2" style="font-family : 'Courier New', FreeMono, Monospace;">N|O|P|Q|R|S|T|U|V|W|X|Y|Z</font>
						{decrypt_table_end}
					</div>
				</div>

{hidehint_end}
<!-- End Text Container -->
<!-- Text container -->
{geokrety_begin}
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/travelbug.png" class="icon32" alt="" />
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
{hidemp3_start}
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/podcache-mp3.png" class="icon32" alt="" />
					{{mp3_files_info}}
				</p></div>
				<div class="content2-container">
				<div id="viewcache-mp3s">
					{mp3_files}
				</div>
			</div>
{hidemp3_end}
<!-- End Text Container -->

<!-- Text container -->
{hidepictures_start}
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/picture.png" class="icon32" alt="" />
					{{images}}
				</p></div>
				<div class="content2-container">
				<div id="viewcache-pictures">
					{pictures}
				</div>
			</div>
{hidepictures_end}
<!-- End Text Container -->
<!-- Text container -->
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<!-- End Text Container -->
					<img src="tpl/stdstyle/images/blue/tools.png" class="icon32" alt="" />&nbsp;{{utilities}}
				</p></div>
				<div class="content2-container">
			<div id="viewcache-utility">
			<div>{watch_icon} {{search_geocaches_nearby}}<?php echo ":
			<a href=\"search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;latNS=";?>{latNS}<?php echo "&amp;lat_h="; ?>{lat_h}<?php echo "&amp;lat_min="; ?>{lat_min}<?php echo "&amp;lonEW="; ?>{lonEW}<?php echo "&amp;lon_h="; ?>{lon_h}<?php echo "&amp;lon_min="; ?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">";?>{{all_geocaches}}<?php echo "</a>&nbsp;
			<a href=\"search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=1&amp;f_userfound=1&amp;f_inactive=1&amp;latNS="; ?>{latNS}<?php echo "&amp;lat_h="; ?>{lat_h}<?php echo "&amp;lat_min="; ?>{lat_min}<?php echo "&amp;lonEW="; ?>{lonEW}<?php echo "&amp;lon_h=";?>{lon_h}<?php echo "&amp;lon_min=";?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">";?>{{searchable}}<?php echo "</a>&nbsp;&nbsp;&nbsp;<br/>"; ?>
{watch_icon} {{find_geocaches_on}}<?php echo ":&nbsp;<b>
			<a href=\"http://www.geocaching.com/seek/nearest.aspx?origin_lat=";?>{latitude}<?php echo "&amp;origin_long=";?>{longitude}<?php echo "&amp;dist=100&amp;submit8=Submit\">Geocaching.com</a>&nbsp;&nbsp;&nbsp;
        		<a href=\"http://geocaching.gpsgames.org/cgi-bin/ge.pl?basic=yes&amp;download=Google+Maps&amp;zoom=8&amp;lat_1=";?>{latitude}<?php echo "&amp;lon_1=";?>{longitude}<?php echo "\">GPSgames.org</a>&nbsp;&nbsp;&nbsp;
        		<a href=\"http://www.opencaching.cz/search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;country=&amp;cachetype=&amp;cache_attribs=&amp;cache_attribs_not=7&amp;latNS=";?>{latNS}<?php echo "&amp;lat_h=";?>{lat_h}<?php echo "&amp;lat_min=";?>{lat_min}<?php echo "&amp;lonEW=";?>{lonEW}<?php echo "&amp;lon_h=";?>{lon_h}<?php echo "&amp;lon_min=";?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">OpenCaching.cz</a>&nbsp;&nbsp;&nbsp;
        		<a href=\"http://www.opencaching.de/search.php?searchto=searchbydistance&amp;showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bydistance&amp;f_userowner=0&amp;f_userfound=0&amp;f_inactive=1&amp;country=&amp;cachetype=&amp;cache_attribs=&amp;cache_attribs_not=7&amp;latNS=";?>{latNS}<?php echo "&amp;lat_h=";?>{lat_h}<?php echo "&amp;lat_min=";?>{lat_min}<?php echo "&amp;lonEW=";?>{lonEW}<?php echo "&amp;lon_h=";?>{lon_h}<?php echo "&amp;lon_min=";?>{lon_min}<?php echo "&amp;distance=100&amp;unit=km\">OpenCaching.de</a></b>
						
			"; ?></div><div> {{download_as_file}}<?php echo ":
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=gpx\" title=\"GPS Exchange Format .gpx\">GPX</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=gpxgc\" title=\"GPS Exchange Format (Groundspeak) .gpx\">GPX GC</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=loc\" title=\"Waypoint .loc\">LOC</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=kml\" title=\"Google Earth .kml\">KML</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=ov2\" title=\"TomTom POI .ov2\">OV2</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=ovl\" title=\"TOP50-Overlay .ovl\">OVL</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=txt\" title=\"Tekst .txt\">TXT</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=wpt\" title=\"Oziexplorer .wpt\">WPT</a>
			<a href=\"search.php?searchto=searchbycacheid&amp;showresult=1&amp;f_inactive=0&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0&amp;f_watched=0&amp;startat=0&amp;cacheid=";?>{cacheid_urlencode}<?php echo "&amp;output=uam\" title=\"AutoMapa .uam\">UAM</a>
			<br /><br />
			<div class=\"notice buffer\" id=\"viewcache-termsofuse\">"; ?> {{accept_terms_of_use}}<?php echo "</div></div>
";
	 ?>
				</div>
			</div>
<!-- Text container -->
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt=""/>
					{{log_entries}}
					&nbsp;&nbsp;
					{found_icon} {founds}x
					{notfound_icon} {notfounds}x
					{note_icon} {notes}x
					&nbsp;&nbsp;
					{viewlogs}
					&nbsp;
					<img src="images/actions/new-entry-18.png" alt=""/>
					<a href="log.php?cacheid={cacheid_urlencode}">{{new_log_entry}}</a>
				</p>
			</div>
			<div class="content2-container" id="viewcache-logs">
					{logs}
			</div>
<!-- End Text Container -->
