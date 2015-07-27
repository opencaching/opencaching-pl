<?php
/***************************************************************************
															-------------------
		begin                : July 23 2006
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

Set center of map and desired zoom level (7) in this js calling: map.setCenter(new GLatLng(49.845068,15.441284), 7);

***************************************************************************/
global $usr;
//echo c_u_f[number];
?>
<script type="text/javascript">

//<![CDATA[icon.image = "http://www.google.com/mapfiles/marker" + cache_icon[number] + ".png";

function load() {
	if (GBrowserIsCompatible()) {
		var map = new GMap2(document.getElementById("map"));
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
//		map.addControl(new GOverviewMapControl());
		map.setCenter(new GLatLng({coords}), 9);
		
		{cachemap_lat}
		{cachemap_lon}
		{cachemap_label}
		{cachemap_cacheid}
		{cachemap_author}
		{cachemap_icon}
		{cachemap_old}
		{cachemap_userid}
//		{cachemap_c_u_f}
		
		var baseIcon = new GIcon();
		//baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
		baseIcon.iconSize = new GSize(20, 34);
		//baseIcon.shadowSize = new GSize(37, 34);
		baseIcon.iconAnchor = new GPoint(10, 34);
		baseIcon.infoWindowAnchor = new GPoint(9, 2);
		//baseIcon.infoShadowAnchor = new GPoint(18, 25);
		
		// Creates a marker at the given point with the given label
		function createMarker(point, number) {
			var icon = new GIcon(baseIcon);
			var icon = new GIcon(baseIcon);
			if(c_user_id[number]) {icon.image = "/tpl/stdstyle/images/google_maps/own" + cache_icon[number] + ".png";
			} else
//			if(c_u_f[number]) {icon.image = "/tpl/stdstyle/images/google_maps/f" + cache_icon[number] + ".png";
//			} else
			if(cache_old[number]<=10) {
				icon.image = "/tpl/stdstyle/images/google_maps/yellow" + cache_icon[number] + ".png";
			} else {
				icon.image = "http://www.google.com/mapfiles/marker" + cache_icon[number] + ".png";
			}
			var marker = new GMarker(point, icon);
			GEvent.addListener(marker, "click", function() {
				marker.openInfoWindowHtml("<a href=\"viewcache.php?cacheid=" + cache_id[number] + "\" target=\"_blank\">" + label[number] + "</a><br/>by " + author[number] + "");
			});
			return marker;
		}

		for (var i = 1; i <= {cachemap_count}; i++) {
			var point = new GLatLng(lat[i], lon[i]);
			map.addOverlay(createMarker(point, i));
		}
	}
}

//]]>
</script>

<div id="map" style="width: 800px; height: 800px"></div>

<div>
<form action="cachemap.php" method="post">
<b>{hide_caches}: </b><br/>
{unknown_type} (U) <input type="checkbox" name="leave_unknown" {cachemap_f_unknown} /> | 
{traditional} (T) <input type="checkbox" name="leave_traditional" {cachemap_f_traditional} /> | 
{multicache} (M) <input type="checkbox" name="leave_multi" {cachemap_f_multi} /> | 
{virtual} (V) <input type="checkbox" name="leave_virtual" {cachemap_f_virtual} /> | 
{webcam} (W) <input type="checkbox" name="leave_webcam" {cachemap_f_webcam} /> | 
{event} (E) <input type="checkbox" name="leave_event" {cachemap_f_event} /> | 
{quiz} (Q) <input type="checkbox" name="leave_quiz" {cachemap_f_quiz} /> | 
{moving} (O) <input type="checkbox" name="leave_moving" {cachemap_f_moving} /> | 
{ready_to_find} <input type="checkbox" name="leave_active" {cachemap_f_active} />
<br/><b>{show_caches}</b><br/>
<?php
if($usr==true) {
?>
<font color="blue">{founds} <input type="checkbox" name="leave_found" {cachemap_f_found} /> | 
{own} <input type="checkbox" name="leave_own" {cachemap_f_own} />
| {ignored} </font><input type="checkbox" name="leave_ignored" {cachemap_f_ignored} /><br/>
<?php
}
?>
<?php
if($usr==true) {
?>
{only_founds} <input type="checkbox" name="show_found" {cachemap_f_ofound} /> |
<?php
}
?>
{archived_plural} <input type="checkbox" name="show_archived" {cachemap_f_archived} /> | 
{only_new} <input type="checkbox" name="show_onlynew" {cachemap_f_newonly} /> |
{temp_unavailable} <input type="checkbox" name="leave_unavailable" {cachemap_f_unavailable} /><br/><br/>
<input type="submit" value="{filter}">
</form>
</div>
