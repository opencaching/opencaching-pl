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

				<div class="content2-container-2col-left" style="width:60px; clear: left; float: left;">

					<div><img src="{icon_cache}" class="icon32" id="viewcache-cacheicon" alt="{cachetype}" title="{cachetype}"/></div>
					<div>{difficulty_icon_diff}</div><div>{difficulty_icon_terr}</div>


				
				</div>
					<div class="content2-container-2col-left"id="cache_name_block" style="width: 80%">
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
						<img src="tpl/stdstyle/images/blue/kompas.png" class="icon32" alt="" title="" />
						<b>{coords}</b> <br/>
						<img src="tpl/stdstyle/images/blue/kompas.png" class="icon32" alt="" title="" />
						<b>{coords2}</b><br/>
						<img src="tpl/stdstyle/images/blue/kompas.png" class="icon32" alt="" title="" />
						<b>{coords3}</b> <span class="content-title-noshade-size0">(WGS84)</span><br />
					</p>
					<p style="line-height: 1.6em;">
<!--						{{location}}:<b><span style="color: rgb(88,144,168)"> {kraj} {dziubek} {woj}</span></b><br />-->
						<img src="tpl/stdstyle/images/free_icons/world.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{location}}:<b><span style="color: rgb(88,144,168)"> {kraj} {dziubek1} {woj} {dziubek2} {miasto}</span></b><br /> 
<img src="tpl/stdstyle/images/free_icons/box.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{cache_type}}: <b>{cachetype}</b><br />
						<img src="tpl/stdstyle/images/free_icons/package.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{size}}: <b>{cachesize}</b><br />
						<img src="tpl/stdstyle/images/free_icons/page.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{status_label}}: {status}<br />
						{hidetime_start}<img src="tpl/stdstyle/images/free_icons/time.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{time}}: {search_time}&nbsp;&nbsp;<img src="tpl/stdstyle/images/free_icons/arrow_switch.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{length}}: {way_length}<br />{hidetime_end}		
						<img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{date_hidden_label}}: {hidden_date}<br />
						<img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{date_created_label}}: {date_created}<br />
						<img src="tpl/stdstyle/images/free_icons/date.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{last_modified_label}}: {last_modified}<br />
						<img src="tpl/stdstyle/images/free_icons/arrow_in.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{waypoint}}: <b>{oc_waypoint}</b><br />
						{hidelistingsites_start}<img src="tpl/stdstyle/images/free_icons/page_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;{{listed_also_on}}: {listed_on}<br />{hidelistingsites_end}
					</p>
					<?php
global $usr, $lang, $hide_coords;			

?>

				</div>
				<div class="content2-container-2col-right" id="viewcache-maptypes">

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
							<img src="http://maps.google.com/staticmap?center={latitude},{longitude}&amp;zoom=13&amp;size=170x170&amp;maptype=terrain&amp;key={googlemap_key}&amp;sensor=false&amp;markers={latitude},{longitude},blue{typeLetter}&amp;format=png" alt="{{map}}" />
						</div></div>
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
				<div id="description">
					<div id="viewcache-description">
						{desc}
					</div>
				</div>
			</div>
<!-- End Text Container -->
<!-- Text container -->
{hidehint_start}
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
					<img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="" />
					<b>{{additional_hints}}</b>&nbsp;&nbsp;
					{decrypt_link_start}
					<img src="tpl/stdstyle/images/blue/decrypt.png" class="icon32" alt="" />
					{decrypt_link}
					{decrypt_link_end}
					<br/>

				</p>
			</div>
					<div class="content2-container">
						<div id="viewcache-hints">
							{hints}
						</div>

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
					<img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt=""/>
					{{log_entries}}
					&nbsp;&nbsp;
					{found_icon} {founds}x
					{notfound_icon} {notfounds}x
					{note_icon} {notes}x
				</p>
			</div>
			<div class="content2-container" id="viewcache-logs">
					{logs}
			</div>
<!-- End Text Container -->
