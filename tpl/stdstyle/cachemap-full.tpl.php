<div id="map_canvas" style="width: 100%; height: 100%; position: absolute; top: 0px; bottom: 0px;">
</div>
<input class="chbox" id="zoom" name="zoom" value="{zoom}" type="hidden" />
	<script type="text/javascript" language="javascript"><!--


function MySearchControl() {
}

MySearchControl.prototype = new GControl();



MySearchControl.prototype.initialize = function(map) {
  var container = document.createElement("div");

  var searchArea = document.createElement("div");

  searchArea.setAttribute("id", "search_control");

  container.appendChild(searchArea);

  map.getContainer().appendChild(container);

  return container;
}

MySearchControl.prototype.getDefaultPosition = function() {
  return new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(30, 50));
}

function FullscreenOffControl() {
}

FullscreenOffControl.prototype = new GControl();



FullscreenOffControl.prototype.initialize = function(map) {
  var container = document.createElement("div");

  var toggleFullscreen = document.createElement("div");

  this.setButtonStyle_(toggleFullscreen);
  container.appendChild(toggleFullscreen);
  toggleFullscreen.appendChild(document.createTextNode("{{disable_fullscreen}}"));


  map.getContainer().appendChild(container);

    GEvent.addDomListener(toggleFullscreen, "click", function() {
		var bounds = "";
		if({fromlat} != {tolat}) {
			bounds = '&fromlat={fromlat}&fromlon={fromlon}&tolat={tolat}&tolon={tolon}';
		}
		window.location = "cachemap3.php?lat="+map.getCenter().lat()+"&lon="+map.getCenter().lng()+"&inputZoom="+map.getZoom()+"&{searchdata}"+bounds;
    });

  return container;
}

FullscreenOffControl.prototype.getDefaultPosition = function() {
  return new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(70, 7));
}

FullscreenOffControl.prototype.setButtonStyle_ = function(button) {
  button.style.textDecoration = "none";
  button.style.color = "#000000";
  button.style.backgroundColor = "white";
  button.style.font = "small Arial";
  button.style.border = "1px solid black";
  button.style.padding = "2px";
  button.style.marginBottom = "3px";
  button.style.textAlign = "center";
  button.style.cursor = "pointer";
}


function CacheFilterControl() {
}


CacheFilterControl.prototype = new GControl();


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

// Creates a one DIV for each of the buttons and places them in a container
// DIV which is returned as our control element. We add the control to
// to the map container and return the element for the map class to
// position properly.
CacheFilterControl.prototype.initialize = function(map) {
  var container = document.createElement("div");

  var filters = document.createElement("div");

  var toggleFilters = document.createElement("div");
//  toggleFilters.style.styleFloat = "right";
  filters.style.position = "absolute";
  filters.style.right = "0";

  this.setButtonStyle_(toggleFilters);
  container.appendChild(toggleFilters);
  toggleFilters.appendChild(document.createTextNode("{{toggle_filters}}"));
filters.style.backgroundColor = 'white';
  filters.style.border = "1px solid black";
  filters.style.width = "150px";


 filters.innerHTML = '\
<div id="fullscreen_map_filters">\
<div id="cache_type_filters_tab" style="background-image: url(images/horizontal_tab.png); cursor: default;{filters_hidden};">{{hide_caches_type}}:</div>\
<div id="cache_type_filters" style="{filters_hidden}">\
<input class="chbox" id="h_u" name="h_u" value="1" type="checkbox" {h_u_checked} onclick="reload()"/><label for="h_u">{{unknown_type}}</label><br/>\
<input class="chbox" id="h_t" name="h_t" value="1" type="checkbox" {h_t_checked} onclick="reload()"/><label for="h_t">{{traditional}}</label><br/>\
<input class="chbox" id="h_m" name="h_m" value="1" type="checkbox" {h_m_checked} onclick="reload()"/><label for="h_m">{{multicache}}</label><br/>\
<input class="chbox" id="h_v" name="h_v" value="1" type="checkbox" {h_v_checked} onclick="reload()"/><label for="h_v">{{virtual}}</label><br/>\
<input class="chbox" id="h_w" name="h_w" value="1" type="checkbox" {h_w_checked} onclick="reload()"/><label for="h_w">Webcam</label><br/>\
<input class="chbox" id="h_e" name="h_e" value="1" type="checkbox" {h_e_checked} onclick="reload()"/><label for="h_e">{{event}}</label><br/>\
<input class="chbox" id="h_q" name="h_q" value="1" type="checkbox" {h_q_checked} onclick="reload()"/><label for="h_q">Quiz</label><br/>\
<input class="chbox" id="h_o" name="h_o" value="1" type="checkbox" {h_o_checked} onclick="reload()"/><label for="h_o">{{moving}}</label><br/>\
</div>\
<div id="cache_status_filters_tab" style="background-image: url(images/horizontal_tab.png); cursor: default;{filters_hidden}">{{hide_caches}}:</div>\
<div id="cache_status_filters" style="{filters_hidden}">\
<input class="chbox" id="h_ignored" name="h_ignored" value="1" type="checkbox" {h_ignored_checked} onclick="reload()"/><label for="h_ignored">{{ignored}}</label><br/>\
<input class="chbox" id="h_own" name="h_own" value="1" type="checkbox" {h_own_checked} onclick="reload()"/><label for="h_own">{{own}}</label><br/>\
<input class="chbox" id="h_found" name="h_found" value="1" type="checkbox" {h_found_checked} onclick="reload()"/><label for="h_found">{{founds}}</label><br/>\
<input class="chbox" id="h_noattempt" name="h_noattempt" value="1" type="checkbox" {h_noattempt_checked} onclick="reload()"/><label for="h_noattempt">{{not_yet_found}}</label><br/>\
<input class="chbox" id="h_nogeokret" name="h_nogeokret" value="1" type="checkbox" {h_nogeokret_checked} onclick="reload()"/><label for="h_nogeokret">{{without_geokret}}</label><br/>\
<input class="chbox" id="h_avail" name="h_avail" value="1" type="checkbox" {h_avail_checked} onclick="reload()"/><label for="h_avail">{{ready_to_find}}</label><br/>\
<input class="chbox" id="h_temp_unavail" name="h_temp_unavail" value="1" type="checkbox" {h_temp_unavail_checked} onclick="reload()"/><label for="h_temp_unavail">{{temp_unavailables}}</label><br/>\
<input class="chbox" id="h_arch" name="h_arch" value="1" type="checkbox" {h_arch_checked} onclick="reload()"/><label for="h_arch">{{archived_plural}}</label><br/>\
</div>\
<div id="other_filters_tab" style="background-image: url(images/horizontal_tab.png); cursor: default;">{{other_options}}:</div>\
<div id="other_filters">\
<input class="chbox" id="signes" name="signes" value="1" type="checkbox" {signes_checked} onclick="reload()" disabled="disabled"/><label for="signes">{{show_signes}}</label><br/>\
<input class="chbox" id="waypoints" name="waypoints" value="1" type="checkbox" {waypoints_checked} onclick="reload()" disabled="disabled"/><label for="waypoints">{{show_waypoints}}</label><br/>\
<span  style="{filters_hidden}">\
<input class="chbox" id="be_ftf" name="be_ftf" value="1" type="checkbox" {be_ftf_checked} onclick="reload();check_field()"/><label for="be_ftf">{{be_ftf_label}}</label><br/>\
<input class="chbox" id="h_pl" name="h_pl" value="1" type="checkbox" {h_pl_checked} onclick="reload()"/><label for="h_pl">{{h_pl_label}}</label><br/>\
<input class="chbox" id="h_de" name="h_de" value="1" type="checkbox" {h_de_checked} onclick="reload()"/><label for="h_de">{{h_de_label}}</label><br/>\
{{from}}: \
<select id="min_score" name="min_score" onchange="reload()">\
    <option value="-3" {min_sel1}>{{rating_poor}}</option>\
    <option value="0.5" {min_sel2}>{{rating_mediocre}}</option>\
    <option value="1.2" {min_sel3}>{{rating_avarage}}</option>\
    <option value="2" {min_sel4}>{{rating_good}}</option>\
    <option value="2.5" {min_sel5}>{{rating_excellent}}</option>\
</select><br/>\
{{to}}:\
<select id="max_score" name="max_score" onchange="reload()">\
    <option value="0.499" {max_sel1}>{{rating_poor}}</option>\
    <option value="1.199" {max_sel2}>{{rating_mediocre}}</option>\
    <option value="1.999" {max_sel3}>{{rating_avarage}}</option>\
    <option value="2.499" {max_sel4}>{{rating_good}}</option>\
    <option value="3.000" {max_sel5}>{{rating_excellent}}</option>\
</select><br/>\
<input class="chbox" id="h_noscore" name="h_noscore" value="1" type="checkbox" {h_noscore_checked} onclick="reload()"/><label for="h_noscore">{{show_noscore}}</label>\
</span>\
</div>\
</div>\
';

  container.appendChild(filters);

  map.getContainer().appendChild(container);

    GEvent.addDomListener(document.getElementById('cache_type_filters_tab'), "click", function() {
        toggleFilterTab(document.getElementById('cache_type_filters_tab'));
    });
    GEvent.addDomListener(document.getElementById('cache_status_filters_tab'), "click", function() {
        toggleFilterTab(document.getElementById('cache_status_filters_tab'));
    });
    GEvent.addDomListener(document.getElementById('other_filters_tab'), "click", function() {
        toggleFilterTab(document.getElementById('other_filters_tab'));
    });
    GEvent.addDomListener(toggleFilters, "click", function() {
        if(filters.style.display == 'none')
            filters.style.display = '';
        else
            filters.style.display = 'none';
    });
  toggleFilterTab(document.getElementById('cache_type_filters_tab'));
    filters.style.display = 'none';
  return container;
}

CacheFilterControl.prototype.getDefaultPosition = function() {
  return new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(7, 60));
}

CacheFilterControl.prototype.setButtonStyle_ = function(button) {
  button.style.textDecoration = "none";
  button.style.color = "#000000";
  button.style.backgroundColor = "white";
  button.style.font = "small Arial";
  button.style.border = "1px solid black";
  button.style.padding = "2px";
  button.style.marginBottom = "3px";
  button.style.textAlign = "center";
  button.style.width = "7em";
  button.style.cursor = "pointer";
}

function ShowCoordsControl() {
}
ShowCoordsControl.prototype = new GControl();
ShowCoordsControl.prototype.initialize = function(map) {
  var container = document.createElement("div");

  var showCoords = document.createElement("div");

  var icon = document.createElement("img");
  icon.src = "tpl/stdstyle/images/blue/compas.png";
  icon.alt = "";
  icon.style.height = "20px";
  icon.style.float = "left";

  this.type = 1;

  this.showCoords = showCoords;

//  this.setCoords(map.getCenter());

  this.setStyle_(showCoords);
  container.appendChild(showCoords);
  showCoords.appendChild(icon);
  showCoords.appendChild(document.createTextNode(""));
  showCoords.owner = this;

  map.getContainer().appendChild(container);

  GEvent.addDomListener(showCoords, "click", function() {
        this.owner.type = ((this.owner.type + 1) % 3);
        this.owner.setCoords(this.owner.lastLatLng);
    });

  this.setCoords(map.getCenter());

  return container;
}
ShowCoordsControl.prototype.getDefaultPosition = function() {
  return new GControlPosition(G_ANCHOR_BOTTOM_RIGHT, new GSize(125, 20));
}

function toWGS84(type, latlng)
{
    var lat = latlng.lat(), lng = latlng.lng();
    var latD = 'N', lngD = 'E';

    if(lat < 0) {
        lat = -lat;
        latD = 'S';
    }
    if(lng < 0) {
        lng = -lng;
        lngD = 'W';
    }

    var latstr, lngstr;

    if(type == 0) {
        latstr = lat.toFixed(4) + "°";
        lngstr = lng.toFixed(4) + "°";
    }
    else if(type == 1) {
        var degs1 = lat | 0;
        var degs2 = lng | 0;
        var minutes1 = ((lat - degs1)*60);
        var minutes2 = ((lng - degs2)*60);
        latstr = degs1 + "° " + minutes1.toFixed(3) + "'";
        lngstr = degs2 + "° " + minutes2.toFixed(3) + "'";
    }
    else if(type == 2) {
        var degs1 = lat | 0;
        var degs2 = lng | 0;
        var minutes1 = ((lat - degs1)*60);
        var minutes2 = ((lng - degs2)*60);
        var seconds1 = (minutes1 - (minutes1 | 0))*60;
        var seconds2 = (minutes2 - (minutes2 | 0))*60;
        latstr = degs1 + "° " + (minutes1 | 0) + "' " + (seconds1 | 0) + "\"";
        lngstr = degs2 + "° " + (minutes2 | 0) + "' " + (seconds2 | 0) + "\"";;
    }
    return latD + " " + latstr + " " + lngD + " " + lngstr;
}

ShowCoordsControl.prototype.setCoords = function(latlng) {
    this.lastLatLng = latlng;
    this.showCoords.childNodes[1].data = toWGS84(this.type, latlng);
}

ShowCoordsControl.prototype.setStyle_ = function(elem) {
  elem.style.textDecoration = "none";
  elem.style.color = "#000000";
  elem.style.backgroundColor = "white";
  elem.style.font = "small Arial";
  elem.style.border = "1px solid black";
  elem.style.padding = "2px";
  elem.style.width = "190px";
  elem.style.textAlign = "center";
  elem.style.cursor = "pointer";
}





	var h_t = 0;
	var map=null;
	var tlo=null;
	var old_temp_unavail_value=null;
	var old_arch_value=null;

	function statusToImageName(status)
	{
		switch( status )
		{
			case "2":
				return "-n";
			case "3":
				return "-a";
			case "6":
				return "-d";
			default:
				return "-s";			
		}
	}

	function typeToImageName(type, status)
	{
		switch( type )
		{
			case "1":
				return "unknown"+statusToImageName(status)+".png";
			case "2":
			default:
				return "traditional"+statusToImageName(status)+".png";
			case "3":
				return "multi"+statusToImageName(status)+".png";
			case "4":
				return "virtual"+statusToImageName(status)+".png";
			case "5":
				return "webcam"+statusToImageName(status)+".png";
			case "6":
				return "event"+statusToImageName(status)+".png";
			case "7":
				return "quiz"+statusToImageName(status)+".png";
			case "8":
				return "moving"+statusToImageName(status)+".png";
		}
	}
	
	function stripslashes(str) 
	{
		str=str.replace(/\\'/g,'\'');
		str=str.replace(/\\"/g,'"');
		str=str.replace(/\\\\/g,'\\');
		str=str.replace(/\\0/g,'\0');
		return str;
	}
	
	function check_field()
	{
		if( document.getElementById('be_ftf').checked )
		{
			// store previews values of temp_unavail and arch checkboxes
			old_temp_unavail_value = document.getElementById('h_temp_unavail').checked;
			old_arch_value = document.getElementById('h_arch').checked;

			document.getElementById('h_temp_unavail').checked = true;
			document.getElementById('h_arch').checked = true;
			
			document.getElementById('h_temp_unavail').disabled = true;
			document.getElementById('h_arch').disabled = true;
		}
		else
		{
			// restore previews values of temp_unavail and arch checkboxes
			document.getElementById('h_temp_unavail').checked = old_temp_unavail_value;
			document.getElementById('h_arch').checked = old_arch_value;
			
			document.getElementById('h_temp_unavail').disabled = false;
			document.getElementById('h_arch').disabled = false;
		}
	}
	
	function get_current_mapid()
	{
		switch (map.getCurrentMapType()) {
			case G_NORMAL_MAP:
            	return 0;
			case G_SATELLITE_MAP:
				return 1;
			case G_HYBRID_MAP:
				return 2;
			case G_PHYSICAL_MAP:
				return 3;
			default:
				return 0;
            }
	}

	function addocoverlay()
	{
			var tilelayer = new GTileLayer(null, null, null, 
					{
						tileUrlTemplate: "lib/cgi-bin/mapper.fcgi?userid={userid}&z={Z}&x={X}&y={Y}&sc={sc}&h_u="+document.getElementById('h_u').checked+"&h_t="+document.getElementById('h_t').checked+"&h_m="+document.getElementById('h_m').checked+"&h_v="+document.getElementById('h_v').checked+"&h_w="+document.getElementById('h_w').checked+"&h_e="+document.getElementById('h_e').checked+"&h_q="+document.getElementById('h_q').checked+"&h_o="+document.getElementById('h_o').checked+"&h_ignored="+document.getElementById('h_ignored').checked+"&h_own="+document.getElementById('h_own').checked+"&h_found="+document.getElementById('h_found').checked+"&h_noattempt="+document.getElementById('h_noattempt').checked+"&h_nogeokret="+document.getElementById('h_nogeokret').checked+"&h_avail="+document.getElementById('h_avail').checked+"&h_temp_unavail="+document.getElementById('h_temp_unavail').checked+"&h_arch="+document.getElementById('h_arch').checked+"&signes="+document.getElementById('signes').checked+"&waypoints="+document.getElementById('waypoints').checked+"&be_ftf="+document.getElementById('be_ftf').checked+"&h_de="+document.getElementById('h_de').checked+"&h_pl="+document.getElementById('h_pl').checked+"&min_score="+document.getElementById('min_score').value+"&max_score="+document.getElementById('max_score').value+"&h_noscore="+document.getElementById('h_noscore').checked,
						isPng:true,
						opacity:1.0
                    });
			tilelayer.getTileUrl = function(tile, zoom) { return "lib/cgi-bin/mapper.fcgi?userid={userid}&z="+zoom+"&x="+tile.x+"&y="+tile.y+"&sc={sc}&h_u="+document.getElementById('h_u').checked+"&h_t="+document.getElementById('h_t').checked+"&h_m="+document.getElementById('h_m').checked+"&h_v="+document.getElementById('h_v').checked+"&h_w="+document.getElementById('h_w').checked+"&h_e="+document.getElementById('h_e').checked+"&h_q="+document.getElementById('h_q').checked+"&h_o="+document.getElementById('h_o').checked+"&h_ignored="+document.getElementById('h_ignored').checked+"&h_own="+document.getElementById('h_own').checked+"&h_found="+document.getElementById('h_found').checked+"&h_noattempt="+document.getElementById('h_noattempt').checked+"&h_nogeokret="+document.getElementById('h_nogeokret').checked+"&h_avail="+document.getElementById('h_avail').checked+"&h_temp_unavail="+document.getElementById('h_temp_unavail').checked+"&h_arch="+document.getElementById('h_arch').checked+"&signes="+document.getElementById('signes').checked+"&waypoints="+document.getElementById('waypoints').checked+"&be_ftf="+document.getElementById('be_ftf').checked+"&h_de="+document.getElementById('h_de').checked+"&h_pl="+document.getElementById('h_pl').checked+"&min_score="+document.getElementById('min_score').value+"&max_score="+document.getElementById('max_score').value+"&h_noscore="+document.getElementById('h_noscore').checked+"&mapid="+get_current_mapid()+"&{searchdata}"; };
			tlo = new GTileLayerOverlay(tilelayer);
	}

	function reload()
	{
		map.clearOverlays(tlo);
		addocoverlay();
		map.addOverlay(tlo);
	}
	
	function load() 
	{
	 if (GBrowserIsCompatible()) 
		{
			map = new GMap2(document.getElementById("map_canvas"), {draggableCursor: 'crosshair', draggingCursor: 'pointer', googleBarOptions : { style: "new", } });
			map.addControl(new CacheFilterControl());


			addocoverlay();

			// UMP
			var copyUMP = new GCopyrightCollection("<a href=\"http://ump.waw.pl/\">UMP-PcPL<\/a>");
			copyUMP.addCopyright(new GCopyright(1, new GLatLngBounds(new GLatLng(-90,-180), new GLatLng(90,180)), 0, " "));
			var tilesUMP = new GTileLayer(copyUMP, 1, 18, {tileUrlTemplate: "http://tiles.ump.waw.pl/ump_tiles/{Z}/{X}/{Y}.png"});
			var mapUMP = new GMapType([tilesUMP], G_NORMAL_MAP.getProjection(), "UMP");
			map.addMapType(mapUMP);

			// OpenStreetMap
			var copyOSM = new GCopyrightCollection("<a href=\"http://www.openstreetmap.org/\">OpenStreetMaps<\/a>");
			copyOSM.addCopyright(new GCopyright(1, new GLatLngBounds(new GLatLng(-90,-180), new GLatLng(90,180)), 0, " "));
			var tilesOSM = new GTileLayer(copyOSM, 1, 18, {tileUrlTemplate: "http://tile.openstreetmap.org/{Z}/{X}/{Y}.png"});
			var mapOSM = new GMapType([tilesOSM], G_NORMAL_MAP.getProjection(), "OSM");
			map.addMapType(mapOSM);

			




			
			map.setCenter(new GLatLng({coords}),{zoom},G_PHYSICAL_MAP);
			document.getElementById("zoom").value = map.getZoom();
	
			map.addControl(new GLargeMapControl());
			map.addControl(new GScaleControl());
//			map.removeMapType(G_HYBRID_MAP);
			map.addMapType(G_PHYSICAL_MAP);
			map.addControl(new GHierarchicalMapTypeControl(true));
			map.addControl(new GOverviewMapControl());			
			map.addControl(new FullscreenOffControl());			
			map.addControl(new MySearchControl());
            var showCoords = new ShowCoordsControl();
            map.addControl(showCoords);
            GEvent.addListener(map, "mousemove", function(latlng) {showCoords.setCoords(latlng);} );


	      // Create a search control
	      var searchControl = new google.search.SearchControl();

	      // Add in local search
	      var localSearch = new google.search.LocalSearch();
	      var options = new google.search.SearcherOptions();
	      options.setExpandMode(GSearchControl.EXPAND_MODE_OPEN);
	      searchControl.addSearcher(localSearch, options);

	      // Set the Local Search center point
	      localSearch.setCenterPoint("Poland");

	      // Tell the searcher to draw itself and tell it where to attach
	      searchControl.draw(document.getElementById("search_control"));

		searchControl.setSearchCompleteCallback(this, function(sc, searcher) { 		
			if(searcher.results.length < 1)
				return;
			var result = searcher.results[0];
			var p = new GLatLng(parseFloat(result.lat), parseFloat(result.lng));
			map.setCenter(p, 13, map.getCurrentMapType());
			document.getElementById("search_control").getElementsByTagName("input")[0].value = "";
		});




			map.setMapType({map_type});
			map.addOverlay(tlo);
			GEvent.addListener(map, "moveend", function() 
			{
			});
			
			GEvent.addListener(map, "zoomend", function() 
			{
				var zoom = map.getZoom();
				if( zoom > 13 ) {
					document.getElementById('signes').disabled = false;
					document.getElementById('waypoints').disabled = false;
				}
				else {
					document.getElementById('waypoints').disabled = true;
					document.getElementById('signes').disabled = true;
				}
				
				// reset double click timer
				document.getElementById("zoom").value = map.getZoom();
				
			});
			

			var onClickFunc = function(overlay,point) 
			{
				if( point==undefined )
					return;
				
				GDownloadUrl("lib/xmlmap.php?lat="+point.lat()+"&lon="+point.lng()+"&zoom="+map.getZoom()+"&userid={userid}&h_u="+document.getElementById('h_u').checked+"&h_t="+document.getElementById('h_t').checked+"&h_m="+document.getElementById('h_m').checked+"&h_v="+document.getElementById('h_v').checked+"&h_w="+document.getElementById('h_w').checked+"&h_e="+document.getElementById('h_e').checked+"&h_q="+document.getElementById('h_q').checked+"&h_o="+document.getElementById('h_o').checked+"&h_ignored="+document.getElementById('h_ignored').checked+"&h_own="+document.getElementById('h_own').checked+"&h_found="+document.getElementById('h_found').checked+"&h_noattempt="+document.getElementById('h_noattempt').checked+"&h_nogeokret="+document.getElementById('h_nogeokret').checked+"&h_avail="+document.getElementById('h_avail').checked+"&h_temp_unavail="+document.getElementById('h_temp_unavail').checked+"&h_arch="+document.getElementById('h_arch').checked+"&signes="+document.getElementById('signes').checked+"&be_ftf="+document.getElementById('be_ftf').checked+"&h_pl="+document.getElementById('h_pl').checked+"&h_de="+document.getElementById('h_de').checked+"&min_score="+document.getElementById('min_score').value+"&max_score="+document.getElementById('max_score').value+"&h_noscore="+document.getElementById('h_noscore').checked+"&{searchdata}", function(data, responseCode) 
					{
						var xml = GXml.parse(data);
							
						var caches = xml.documentElement.getElementsByTagName("cache");
						var cache_id = caches[0].getAttribute("cache_id");
						var name = stripslashes(caches[0].getAttribute("name"));
						var username = stripslashes(caches[0].getAttribute("username"));
						var wp = caches[0].getAttribute("wp");
						var votes = caches[0].getAttribute("votes");
						var score = caches[0].getAttribute("score");
						var topratings = caches[0].getAttribute("topratings");
						var lat = caches[0].getAttribute("lat");
						var lon = caches[0].getAttribute("lon");
						var type = caches[0].getAttribute("type");
						var status = caches[0].getAttribute("status");
						var user_id = caches[0].getAttribute("user_id");
						var founds = caches[0].getAttribute("founds");
						var notfounds = caches[0].getAttribute("notfounds");
						var node = caches[0].getAttribute("node");
							
						if( cache_id != "" )
						{							
							var show_score;
							var print_topratings;
							if( score != "" && votes > 2)
							{
								show_score = "<br><b>{{score_label}}:<\/b> " + score;
							}
							else show_score = "";
							
							if( topratings == 0 )
								print_topratings = "";
							else 
							{
								print_topratings = "<br><b>{{recommendations}}: <\/b>";
								var gwiazdka = "<img width=\"10\" height=\"10\" src=\"images/rating-star.png\" alt=\"{{recommendation}}\" />";
								var ii;
								for( ii=0;ii<topratings;ii++)
									print_topratings += gwiazdka;
							}

							var infoWindowContent = "";
							var domain="";
							switch( node )
							{
								case "1":
									domain = "http://www.opencaching.de/";
									break;
								case "2":
									domain = "";
									break;
								case "3":
									domain = "http://www.opencaching.cz/";
									break;
								default:
									domain = "";
							}
								
							if( type == 6 )
							{
								found_attended = "{{attendends}}";
								notfound_will_attend = "{{will_attend}}";
							}
							else
							{
								found_attended = "{{found}}";
								notfound_will_attend = "{{not_found}}";
							}

							infoWindowContent += "<table border=\"0\" width=\"350\" height=\"120\" class=\"table\">";
							infoWindowContent += "<tr><td colspan=\"2\" width=\"100%\"><table cellspacing=\"0\" width=\"100%\"><tr><td width=\"90%\">";
							infoWindowContent += "<center><img align=\"left\" width=\"20\" height=\"20\" src=\"tpl/stdstyle/images/cache/"+typeToImageName(type, status)+"\" /><\/center>";
							infoWindowContent += "&nbsp;<a href=\""+domain+"viewcache.php?cacheid=" + cache_id + "\" target=\"_blank\">" + name + "<\/a>";
							infoWindowContent += "<\/td><td width=\"10%\">";
							infoWindowContent += "<b>"+wp+"<\/b><\/td><\/tr><\/table>";
							infoWindowContent += "<\/td><\/tr>";
							infoWindowContent += "<tr><td width=\"70%\" valign=\"top\">";
							infoWindowContent += "<b>{{created_by}}:<\/b> " + username + show_score + print_topratings;
				
							infoWindowContent += "<\/td>";
							infoWindowContent += "<td valign=\"top\" width=\"30%\"><table cellspacing=\"0\" cellpadding=\"0\" class=\"table\"><tr><td width=\"100%\">";
							infoWindowContent += "<nobr><img src=\"tpl/stdstyle/images/log/16x16-found.png\" border=\"0\" width=\"10\" height=\"10\" /> "+founds+" x "+found_attended+"<\/nobr><\/td><\/tr>";
							infoWindowContent += "<tr><td width=\"100%\"><nobr><img src=\"tpl/stdstyle/images/log/16x16-dnf.png\" border=\"0\" width=\"10\" height=\"10\" /> "+notfounds+" x "+notfound_will_attend+"<\/nobr><\/td><\/tr>";
							if( node == 2 )
								infoWindowContent += "<tr><td width=\"100%\"><nobr><img src=\"tpl/stdstyle/images/action/16x16-adddesc.png\" border=\"0\" width=\"10\" height=\"10\" /> "+votes+" x {{scored}}<\/nobr>";

							infoWindowContent += "<\/td><\/tr><\/table><\/td><\/tr>";
							infoWindowContent += "<tr><td align=\"left\" width=\"100%\" colspan=\"2\">";
							/*if( node == 2 )
								infoWindowContent += "<font size=\"0\"><a href=\"cachemap3.php?lat="+"\"><?php echo ($yn=='y'?tr('add_to'):tr('remove_from'));?> {{to_print_list}}<\/a><\/font>";*/
							infoWindowContent += "<\/td><\/tr><\/table><\/td><\/tr>";
							infoWindowContent += "<\/table>";
							
							map.openInfoWindowHtml(new GLatLng(lat,lon), infoWindowContent,{onCloseFn: function() {
								
						}
					});
					}
				});
			};

			GEvent.addListener(map, "click", onClickFunc);

			
		}
		document.getElementsByTagName("body")[0].onclick = saveMapType;
		if({doopen})
			onClickFunc(tlo, new GLatLng({coords}));

		if("{filters_hidden}".length)
			toggleFilterTab(document.getElementById('other_filters_tab'));
		if( map.getZoom() > 13 ) {
			document.getElementById('signes').disabled = false;
			document.getElementById('waypoints').disabled = false;
		}
		else {
			document.getElementById('waypoints').disabled = true;
			document.getElementById('signes').disabled = true;
		}
        if({fromlat} != {tolat}) {
            var area = new GLatLngBounds();
            area.extend(new GLatLng({fromlat}, {fromlon}));
            area.extend(new GLatLng({tolat}, {tolon}));
            var newZoom = map.getBoundsZoomLevel(area);
            map.setCenter(area.getCenter(), newZoom);
        }
	}
// -->
</script>
