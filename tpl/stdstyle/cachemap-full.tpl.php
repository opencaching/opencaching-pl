<div id="map_canvas" style="width: 100%; height: 100%; position: absolute; top: 0px; x-bottom: 0px;">
</div>

<div id="fullscreen_map_filters" style="position: absolute; top: 60px; right: 5px; border: 1px solid black; width: 150px; background-color: white; display: none;">
<div id="cache_type_filters_tab" style="background-image: url(images/horizontal_tab.png); cursor: default;{filters_hidden};">{{hide_caches_type}}:</div>
<div id="cache_type_filters" style="{filters_hidden}">
<input class="chbox" id="h_u" name="h_u" value="1" type="checkbox" {h_u_checked} onclick="reload()"/><label for="h_u">{{unknown_type}}</label><br/>
<input class="chbox" id="h_t" name="h_t" value="1" type="checkbox" {h_t_checked} onclick="reload()"/><label for="h_t">{{traditional}}</label><br/>
<input class="chbox" id="h_m" name="h_m" value="1" type="checkbox" {h_m_checked} onclick="reload()"/><label for="h_m">{{multicache}}</label><br/>
<input class="chbox" id="h_v" name="h_v" value="1" type="checkbox" {h_v_checked} onclick="reload()"/><label for="h_v">{{virtual}}</label><br/>
<input class="chbox" id="h_w" name="h_w" value="1" type="checkbox" {h_w_checked} onclick="reload()"/><label for="h_w">Webcam</label><br/>
<input class="chbox" id="h_e" name="h_e" value="1" type="checkbox" {h_e_checked} onclick="reload()"/><label for="h_e">{{event}}</label><br/>
<input class="chbox" id="h_q" name="h_q" value="1" type="checkbox" {h_q_checked} onclick="reload()"/><label for="h_q">Quiz</label><br/>
<input class="chbox" id="h_o" name="h_o" value="1" type="checkbox" {h_o_checked} onclick="reload()"/><label for="h_o">{{moving}}</label><br/>
<input class="chbox" id="h_owncache" name="h_owncache" value="1" type="checkbox" {h_owncache_checked} onclick="reload()"/><label for="h_owncache">{{owncache}}</label><br/>
</div>
<div id="cache_status_filters_tab" style="background-image: url(images/horizontal_tab.png); cursor: default;{filters_hidden}">{{hide_caches}}:</div>
<div id="cache_status_filters" style="{filters_hidden}">
<input class="chbox" id="h_ignored" name="h_ignored" value="1" type="checkbox" {h_ignored_checked} onclick="reload()"/><label for="h_ignored">{{ignored}}</label><br/>
<input class="chbox" id="h_own" name="h_own" value="1" type="checkbox" {h_own_checked} onclick="reload()"/><label for="h_own">{{own}}</label><br/>
<input class="chbox" id="h_found" name="h_found" value="1" type="checkbox" {h_found_checked} onclick="reload()"/><label for="h_found">{{founds}}</label><br/>
<input class="chbox" id="h_noattempt" name="h_noattempt" value="1" type="checkbox" {h_noattempt_checked} onclick="reload()"/><label for="h_noattempt">{{not_yet_found}}</label><br/>
<input class="chbox" id="h_nogeokret" name="h_nogeokret" value="1" type="checkbox" {h_nogeokret_checked} onclick="reload()"/><label for="h_nogeokret">{{without_geokret}}</label><br/>
<input class="chbox" id="h_avail" name="h_avail" value="1" type="checkbox" {h_avail_checked} onclick="reload()"/><label for="h_avail">{{ready_to_find}}</label><br/>
<input class="chbox" id="h_temp_unavail" name="h_temp_unavail" value="1" type="checkbox" {h_temp_unavail_checked} onclick="reload()"/><label for="h_temp_unavail">{{temp_unavailables}}</label><br/>
<input class="chbox" id="h_arch" name="h_arch" value="1" type="checkbox" {h_arch_checked} onclick="reload()"/><label for="h_arch">{{archived_plural}}</label><br/>
</div>
<div id="other_filters_tab" style="background-image: url(images/horizontal_tab.png); cursor: default;">{{other_options}}:</div>
<div id="other_filters">
<span  style="{filters_hidden}">
<input class="chbox" id="be_ftf" name="be_ftf" value="1" type="checkbox" {be_ftf_checked} onclick="reload();check_field()"/><label for="be_ftf">{{be_ftf_label}}</label><br/>
{{from}}: 
<select id="min_score" name="min_score" onchange="reload()">
    <option value="-3" {min_sel1}>{{rating_poor}}</option>
    <option value="0.5" {min_sel2}>{{rating_mediocre}}</option>
    <option value="1.2" {min_sel3}>{{rating_avarage}}</option>
    <option value="2" {min_sel4}>{{rating_good}}</option>
    <option value="2.5" {min_sel5}>{{rating_excellent}}</option>
</select><br/>
{{to}}:
<select id="max_score" name="max_score" onchange="reload()">
    <option value="0.499" {max_sel1}>{{rating_poor}}</option>
    <option value="1.199" {max_sel2}>{{rating_mediocre}}</option>
    <option value="1.999" {max_sel3}>{{rating_avarage}}</option>
    <option value="2.499" {max_sel4}>{{rating_good}}</option>
    <option value="3.000" {max_sel5}>{{rating_excellent}}</option>
</select><br/>
<input class="chbox" id="h_noscore" name="h_noscore" value="1" type="checkbox" {h_noscore_checked} onclick="reload()"/><label for="h_noscore">{{show_noscore}}</label>
</span>
</div>
</div>

<input class="chbox" id="zoom" name="zoom" value="{zoom}" type="hidden" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script src="{lib_cachemap3_js}" type="text/javascript"></script>
<script type="text/javascript" language="javascript">
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
		recommendations: "{{recommendations}}",
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
	var searchControl = createSearchControl();
	var fullScreenOffControl = createFullScreenOffControl();
	var cacheFilter = createCacheFilterControl();
	load([
		{ position: google.maps.ControlPosition.BOTTOM_LEFT, control: searchControl },
		{ position: google.maps.ControlPosition.TOP_LEFT, control: fullScreenOffControl },
		{ position: google.maps.ControlPosition.RIGHT_TOP, control: cacheFilter }
	], searchControl);
};

function createSearchControl() {
	var container = document.createElement("div");
	container.style.padding = "5px";

	var searchArea = document.createElement("div");
	searchArea.setAttribute("id", "search_control");

	container.appendChild(searchArea);

	return container;
}

function createFullScreenOffControl() {
	var container = document.createElement("div");
	container.style.padding = "5px";

	var toggleFullscreen = document.createElement("div");

	toggleFullscreen.style.backgroundColor = "white";
	toggleFullscreen.style.font = "small Arial";
	toggleFullscreen.style.border = "1px solid #717B87";
	toggleFullscreen.style.padding = "2px 6px";
	toggleFullscreen.style.textAlign = "center";
	toggleFullscreen.style.cursor = "pointer";

	container.appendChild(toggleFullscreen);
	toggleFullscreen.appendChild(document.createTextNode("{{disable_fullscreen}}"));

	google.maps.event.addDomListener(toggleFullscreen, "click", function() {
		var bounds = "";
		if({fromlat} != {tolat}) {
			bounds = '&fromlat={fromlat}&fromlon={fromlon}&tolat={tolat}&tolon={tolon}';
		}
		window.location = "cachemap3.php?lat="+map.getCenter().lat()+"&lon="+map.getCenter().lng()+"&inputZoom="+map.getZoom()+"&{searchdata}"+bounds+"{extrauserid}";
	});

	return container;
}

function toggleFilterTab(obj)
{
	var filters1 = document.getElementById('cache_type_filters');
	var filters2 = document.getElementById('cache_status_filters');
	var filters3 = document.getElementById('other_filters');
	var filters1_tab = document.getElementById('cache_type_filters_tab');
	var filters2_tab = document.getElementById('cache_status_filters_tab');
	var filters3_tab = document.getElementById('other_filters_tab');
	if(filters1_tab != obj)
		filters1.style.display='none';
	else
		filters1.style.display='';
	if(filters2_tab != obj)
		filters2.style.display='none';
	else
		filters2.style.display='';
	if(filters3_tab != obj)
		filters3.style.display='none';
	else
		filters3.style.display='';
}

function createCacheFilterControl() {
	var container = document.createElement("div");
	container.style.padding = "5px";

	var toggleFilters = document.createElement("div");

	toggleFilters.style.backgroundColor = "white";
	toggleFilters.style.font = "small Arial";
	toggleFilters.style.border = "1px solid #717B87";
	toggleFilters.style.padding = "2px 6px";
	toggleFilters.style.textAlign = "center";
	toggleFilters.style.cursor = "pointer";

	toggleFilters.appendChild(document.createTextNode("{{toggle_filters}}"));
	
	container.appendChild(toggleFilters);

	var filters = document.getElementById("fullscreen_map_filters");

	google.maps.event.addDomListener(document.getElementById('cache_type_filters_tab'), "click", function() {
        toggleFilterTab(document.getElementById('cache_type_filters_tab'));
	});
	google.maps.event.addDomListener(document.getElementById('cache_status_filters_tab'), "click", function() {
	    toggleFilterTab(document.getElementById('cache_status_filters_tab'));
	});
	google.maps.event.addDomListener(document.getElementById('other_filters_tab'), "click", function() {
	    toggleFilterTab(document.getElementById('other_filters_tab'));
	});
	google.maps.event.addDomListener(toggleFilters, "click", function() {
		if(filters.style.display == 'none')
			filters.style.display = '';
		else
			filters.style.display = 'none';
	});

	toggleFilterTab(document.getElementById('cache_type_filters_tab'));

	return container;
}
</script>
