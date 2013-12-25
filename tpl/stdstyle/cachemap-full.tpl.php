<style>
	.opt_table input { border: 0; }
	.opt_table { background: #eee; margin: 3px auto; }
	.opt_table th { background: #888; padding: 3px 8px 5px 8px; font-family: Tahoma; font-size: 13px; font-weight: bold; color: #fff; }
	.opt_table td { padding: 6px; font-family: Tahoma; font-size: 13px; vertical-align: top; }
	.opt_table select { padding: 1px; font-family: Tahoma; font-size: 13px; border: 1px solid #888; }
	.opt_table td.i { position: relative; width: 35px; display: block; }
	.opt_table td.i img { position: absolute; top: 0; }
	.opt_table .dim { color: #888; }
	.opt_table .dim img { opacity: .3; }
	#ext_search input.gsc-input { width: 10em; }
</style>

<div id="map_canvas" style="width: 100%; height: 100%; position: absolute; top: 0px;">
</div>

<div id="control_combo">
	<table id="control_combo_table" style='background: #eee; padding: 3px 0px 3px 8px;'>
		<tr>
			<td>
				<div id="ext_search"><div id="search_control" style="float: left;">
					<table cellspacing="0" cellpadding="0"><tr>
						<td><input id="place_search_text" class="gsc-input" type="text" size="10"></td>
						<td><input id="place_search_button" class="gsc-search-button" value="{{search}}" type="button"></td>
					</tr></table>
				</div></div>
			</td>
			<td>
				<table style='float: right;'><tr>
					<td><a id="fullscreen_off" style='cursor: pointer'><img src="images/fullscreen-off.png" title="{{disable_fullscreen}}"/></a></td>
					<td><a id="current_position" style='cursor: pointer; display: none'><img id="current_position_icon" src="images/map_geolocation_0.png" title="{{where_i_am}}"/></a></td>
					<td><a id="toggle_filters" style='cursor: pointer'><img src="okapi/static/tilemap/legend_other.png" title="{{toggle_filters}}"/></a></td>
				</tr></table>
			</td>
		</tr>
	</table>
</div>

<div id="map_filters" style="position: absolute; top: 50px; display: none;">

	<table id='cache_types' class='opt_table' cellspacing="0">
		<tr>
			<th colspan='2'>{{hide_caches_type}}:</th>
		</tr>
		<tr>
			<td>
				<table>
					<tr class='h_t'>
						<td><input class="chbox" id="h_t" name="h_t" value="1" type="checkbox" {h_t_checked} onclick="reload()"/>&nbsp;<label for="h_t">{{traditional}}</label></td>
						<td class='i'><img src='okapi/static/tilemap/legend_traditional.png'/></td>
					</tr>
					<tr class='h_m'>
						<td><input class="chbox" id="h_m" name="h_m" value="1" type="checkbox" {h_m_checked} onclick="reload()"/><label for="h_m">&nbsp;{{multicache}}</label></td>
						<td class='i'><img src='okapi/static/tilemap/legend_multi.png'/></td>
					</tr>
					<tr class='h_q'>
						<td><input class="chbox" id="h_q" name="h_q" value="1" type="checkbox" {h_q_checked} onclick="reload()"/><label for="h_q">&nbsp;{{quiz}}</label></td>
						<td class='i'><img src='okapi/static/tilemap/legend_quiz.png'/></td>
					</tr>
					<tr class='h_v'>
						<td><input class="chbox" id="h_v" name="h_v" value="1" type="checkbox" {h_v_checked} onclick="reload()"/><label for="h_v">&nbsp;{{virtual}}</label></td>
						<td class='i'><img src='okapi/static/tilemap/legend_virtual.png'/></td>
					</tr>
					<tr class='h_e'>
						<td><input class="chbox" id="h_e" name="h_e" value="1" type="checkbox" {h_e_checked} onclick="reload()"/><label for="h_e">&nbsp;{{event}}</label></td>
						<td class='i'><img src='okapi/static/tilemap/legend_event.png'/></td>
					</tr>
				</table>
			</td>
			<td>
				<table>
					<tr class='h_u'>
						<td><input class="chbox" id="h_u" name="h_u" value="1" type="checkbox" {h_u_checked} onclick="reload()"/><label for="h_u">&nbsp;{{unknown_type}}</label></td>
						<td class='i'><img src='okapi/static/tilemap/legend_unknown.png'/></td>
					</tr>
					<tr class='h_w'>
						<td><input class="chbox" id="h_w" name="h_w" value="1" type="checkbox" {h_w_checked} onclick="reload()"/><label for="h_w">&nbsp;Webcam</label></td>
						<td class='i'><img src='okapi/static/tilemap/legend_webcam.png'/></td>
					</tr>
					<tr class='h_o'>
						<td><input class="chbox" id="h_o" name="h_o" value="1" type="checkbox" {h_o_checked} onclick="reload()"/><label for="h_o">&nbsp;{{moving}}</label></td>
						<td class='i'><img src='okapi/static/tilemap/legend_moving.png'/></td>
					</tr>
					<tr class='h_owncache'>
						<td><input class="chbox" id="h_owncache" name="h_owncache" value="1" type="checkbox" {h_owncache_checked} onclick="reload()"/><label for="h_owncache">&nbsp;{{owncache}}</label></td>
						<td class='i'><img src='okapi/static/tilemap/legend_own.png'/></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<table id='other_options' class='opt_table' cellspacing="0">
		<tr>
			<th colspan='2'>{{hide_caches}}:</th>
		</tr>
		<tr>
			<td>
				<div class='h_ignored'>
					<input class="chbox" id="h_ignored" name="h_ignored" value="1" type="checkbox" {h_ignored_checked} onclick="reload()"/><label for="h_ignored">&nbsp;{{ignored}}</label>
				</div>
				<div class='h_own'>
					<input class="chbox" id="h_own" name="h_own" value="1" type="checkbox" {h_own_checked} onclick="reload()"/><label for="h_own">&nbsp;{{own}}</label>
				</div>
				<div class='h_found'>
					<input class="chbox" id="h_found" name="h_found" value="1" type="checkbox" {h_found_checked} onclick="reload()"/><label for="h_found">&nbsp;{{founds}}</label>
				</div>
				<div class='h_noattempt'>
					<input class="chbox" id="h_noattempt" name="h_noattempt" value="1" type="checkbox" {h_noattempt_checked} onclick="reload()"/><label for="h_noattempt">&nbsp;{{not_yet_found}}</label>
				</div>
				<div class='h_nogeokret'>
					<input class="chbox" id="h_nogeokret" name="h_nogeokret" value="1" type="checkbox" {h_nogeokret_checked} onclick="reload()"/><label for="h_nogeokret">&nbsp;{{without_geokret}}</label>
				</div>
			</td>
			<td>
				<div class='h_temp_unavail'>
					<input class="chbox" id="h_temp_unavail" name="h_temp_unavail" value="1" type="checkbox" {h_temp_unavail_checked} onclick="reload()"/><label for="h_temp_unavail">&nbsp;{{temp_unavailables}}</label>
				</div>
				<div class='h_arch'>
					<input class="chbox" id="h_arch" name="h_arch" value="1" type="checkbox" {h_arch_checked} onclick="reload()"/><label for="h_arch">&nbsp;{{archived_plural}}</label>
				</div>
				<hr>
				<div>
					<input class="chbox" id="be_ftf" name="be_ftf" value="1" type="checkbox" {be_ftf_checked} onclick="reload();check_field()"/><label for="be_ftf">&nbsp;{{map_01}}</label>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<div>
					<center>
						{{map_02}}
						<select id="min_score" name="min_score" onchange="reload()">
							<option value="-3" {min_sel1}>{{map_03}}</option>
							<!--<option value="0.5" {min_sel2}>pomiń najsłabsze skrzynki</option>-->
							<option value="1.2" {min_sel3}>{{rating_ge_average}}</option>
							<option value="2" {min_sel4}>{{rating_ge_good}}</option>
							<option value="2.5" {min_sel5}>{{rating_ge_excellent}}</option>
						</select>
					</center>
				</div>
				<div style='margin-top: 5px'><center>
					<input class="chbox" id="h_noscore" name="h_noscore" value="1" type="checkbox" {h_noscore_checked} onclick="reload()"/><label for="h_noscore">&nbsp;{{map_04}}</label>
				</center></div>
			</td>
		</tr>
	</table>

</div>

<input class="chbox" id="zoom" name="zoom" value="{zoom}" type="hidden" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script src="{lib_cachemap3_js}" type="text/javascript"></script>
<script type="text/javascript" language="javascript">
$(function() {
	var checkbox_changed = function() {
		var $related = $("." + $(this).attr('name'));
		if ($(this).is(':checked'))
			$related.addClass('dim');
		else
			$related.removeClass('dim');
	}
	$('.opt_table input')
		.each(checkbox_changed)
		.change(checkbox_changed);
});

initial_params = {
	start: {
		cachemap_mapper: "{cachemap_mapper}",
		userid: {userid},
		coords: [{coords}], 
		zoom: {zoom},
		map_type: {map_type},
		circle: 0, //{circle},
		doopen: {doopen},
		fromlat: {fromlat}, fromlon: {fromlon},
		tolat: {tolat}, tolon: {tolon},
		searchdata: "{searchdata}",
		boundsurl: "{boundsurl}",
		extrauserid: "{extrauserid}",
		moremaptypes: true,
		fullscreen: true,
		largemap: true,
		savesettings: true
	},
	translation: {
		score_label: "{{score_label}}",
		recommendations: "{{search_recommendations}}",
		recommendation: "{{recommendation}}",
		attendends: "{{attendends}}",
		will_attend: "{{will_attend}}",
		found: "{{found}}",
		not_found: "{{not_found}}",
		size: "{{size}}",
		created_by: "{{created_by}}",
		scored: "{{scored}}"
	}
};

window.onload = function() {

	attachFullScreenOffControl();
	attachCacheFilterControl();
	attachCurrentPositionControl();

	load([
		{ position: google.maps.ControlPosition.TOP_LEFT, control: document.getElementById("control_combo") }
	], document.getElementById("search_control"));

	// Hack for very small devices - check if the control combo fits in the browser window
	// If not - shrink the search box 
	var excessPixels = $("#control_combo_table").outerWidth() - window.innerWidth;
	if (excessPixels > 0) {
		var oldWidth = $("input.gsc-input").width();
		$("input.gsc-input").width(oldWidth - excessPixels);
	};

};

function attachFullScreenOffControl() {
	google.maps.event.addDomListener(document.getElementById("fullscreen_off"), "click", function() {
		var bounds = "";
		if({fromlat} != {tolat}) {
			bounds = '&fromlat={fromlat}&fromlon={fromlon}&tolat={tolat}&tolon={tolon}';
		}
		window.location = "cachemap3.php?lat="+map.getCenter().lat()+"&lon="+map.getCenter().lng()+"&inputZoom="+map.getZoom()+"&{searchdata}"+bounds+"{extrauserid}";
	});
}

function attachCacheFilterControl() {
	var filters = document.getElementById("map_filters");

	google.maps.event.addDomListener(document.getElementById("toggle_filters"), "click", function() {
		if(filters.style.display == 'none') {
			filters.style.left = document.getElementById("control_combo").offsetLeft + "px";
			filters.style.display = '';
		}
		else
			filters.style.display = 'none';
	});
}

function attachCurrentPositionControl() {
	if (!("geolocation" in navigator))
		return;

	var curr_pos_el = document.getElementById("current_position"); 
	curr_pos_el.style.display = "";
	google.maps.event.addDomListener(curr_pos_el, "click", function() {
		getCurrentPosition();
	});
}
</script>
