<div class="content2-pagetitle">&nbsp;<img src="tpl/stdstyle/images/blue/world.png" class="icon32" alt="" title=""/>&nbsp;&nbsp;{{user_map}} {username}</div>
<div class="content2-container">
<p class="content-title-noshade-size1">{{current_zoom}}: <input type="text" id="zoom" size="2" value="{zoom}" disabled="disabled"/></p> 
<p class="content-title-noshade">{{colors}}: <b><font color="#dddd00">{{yellow}}</font></b> - {{last_10_days}}, <b><font color="#00dd00">{{green}}</font></b> - {{own}}, <b><font color="#aaaaaa">{{gray}}</font></b> - {{found}}, <b><font color="#ff0000">{{red}}</font></b> - {{rest}}</p>
</div>
<div class="nav4">
<ul id="topmapmenu"><li class="group"><a style="background-image: url(images/actions/fullscreen-18.png);background-repeat:no-repeat;" href="cachemap-full.php?{searchdata}{boundsurl}{extrauserid}" onclick='window.location = "cachemap-full.php?lat="+map.getCenter().lat()+"&amp;lon="+map.getCenter().lng()+"&amp;inputZoom="+map.getZoom()+"&amp;{searchdata}{boundsurl}{extrauserid}"; return false;'>{{fullscreen}}</a></li></ul>
</div>
<div id="ext_search">
<div id="search_control" style="float: left;">&nbsp;</div>
</div>
<div id="map_canvas" style="width: {map_width}; height: {map_height}; float:left; border: 1px solid #000;">
</div>
<div class="content2-container">
<table width="100%">
<tr>
	<td width="33%" style="{filters_hidden}">
		<div class="nav3">
			<ul>
				<li class="title">{{hide_caches_type}}:</li>
				<li class="group"><input class="chbox" id="h_u" name="h_u" value="1" type="checkbox" {h_u_checked} onclick="reload()"/><label for="h_u">{{unknown_type}}</label></li>
				<li class="group"><input class="chbox" id="h_t" name="h_t" value="1" type="checkbox" {h_t_checked} onclick="reload()"/><label for="h_t">{{traditional}}</label></li>
				<li class="group"><input class="chbox" id="h_m" name="h_m" value="1" type="checkbox" {h_m_checked} onclick="reload()"/><label for="h_m">{{multicache}}</label></li>
				<li class="group"><input class="chbox" id="h_v" name="h_v" value="1" type="checkbox" {h_v_checked} onclick="reload()"/><label for="h_v">{{virtual}}</label></li>
				<li class="group"><input class="chbox" id="h_w" name="h_w" value="1" type="checkbox" {h_w_checked} onclick="reload()"/><label for="h_w">Webcam</label></li>
				<li class="group"><input class="chbox" id="h_e" name="h_e" value="1" type="checkbox" {h_e_checked} onclick="reload()"/><label for="h_e">{{event}}</label></li>
				<li class="group"><input class="chbox" id="h_q" name="h_q" value="1" type="checkbox" {h_q_checked} onclick="reload()"/><label for="h_q">Quiz</label></li>
				<li class="group"><input class="chbox" id="h_o" name="h_o" value="1" type="checkbox" {h_o_checked} onclick="reload()"/><label for="h_o">{{moving}}</label></li>
				<li class="group"><input class="chbox" id="h_owncache" name="h_owncache" value="1" type="checkbox" {h_owncache_checked} onclick="reload()"/><label for="h_owncache">{{owncache}}</label></li>
			</ul>
		</div>
	</td>
	<td>
		<div class="nav3" style="{filters_hidden}">
			<ul>
				<li class="title">{{hide_caches}}:</li>
				<li class="group"><input class="chbox" id="h_ignored" name="h_ignored" value="1" type="checkbox" {h_ignored_checked} onclick="reload()"/><label for="h_ignored">{{ignored}}</label></li>
				<li class="group"><input class="chbox" id="h_own" name="h_own" value="1" type="checkbox" {h_own_checked} onclick="reload()"/><label for="h_own">{{own}}</label></li>
				<li class="group"><input class="chbox" id="h_found" name="h_found" value="1" type="checkbox" {h_found_checked} onclick="reload()"/><label for="h_found">{{founds}}</label></li>
				<li class="group"><input class="chbox" id="h_noattempt" name="h_noattempt" value="1" type="checkbox" {h_noattempt_checked} onclick="reload()"/><label for="h_noattempt">{{not_yet_found}}</label></li>
				<li class="group"><input class="chbox" id="h_nogeokret" name="h_nogeokret" value="1" type="checkbox" {h_nogeokret_checked} onclick="reload()"/><label for="h_nogeokret">{{without_geokret}}</label></li>
				<li class="group"><input class="chbox" id="h_avail" name="h_avail" value="1" type="checkbox" {h_avail_checked} onclick="reload()"/><label for="h_avail">{{ready_to_find}}</label></li>
				<li class="group"><input class="chbox" id="h_temp_unavail" name="h_temp_unavail" value="1" type="checkbox" {h_temp_unavail_checked} onclick="reload()"/><label for="h_temp_unavail">{{temp_unavailables}}</label></li>
				<li class="group"><input class="chbox" id="h_arch" name="h_arch" value="1" type="checkbox" {h_arch_checked} onclick="reload()"/><label for="h_arch">{{archived_plural}}</label></li>	
			</ul>
		</div>
	</td>
	<td width="33%">
		<div class="nav3">
			<ul>
				<li class="title">{{other_options}}:</li>
				<li class="group"><input class="chbox" id="signes" name="signes" value="1" type="checkbox" {signes_checked} onclick="reload()" disabled="disabled"/><label for="signes">{{show_signes}}</label></li>
				<li class="group"><input class="chbox" id="waypoints" name="waypoints" value="1" type="checkbox" {waypoints_checked} onclick="reload()" disabled="disabled"/><label for="waypoints">{{show_waypoints}}</label></li>
				<li class="group" style="{filters_hidden}"><input class="chbox" id="be_ftf" name="be_ftf" value="1" type="checkbox" {be_ftf_checked} onclick="reload();check_field()"/><label for="be_ftf">{{be_ftf_label}}</label></li>
				<li class="group" style="{filters_hidden}"><input class="chbox" id="h_pl" name="h_pl" value="1" type="checkbox" {h_pl_checked} onclick="reload()"/><label for="h_pl">{{h_pl_label}}</label></li>
				<li class="group" style="{filters_hidden}"><input class="chbox" id="h_de" name="h_de" value="1" type="checkbox" {h_de_checked} onclick="reload()"/><label for="h_de">{{h_de_label}}</label></li>
				<li class="group" style="{filters_hidden}"><input class="chbox" id="h_se" name="h_se" value="1" type="checkbox" {h_se_checked} onclick="reload()"/><label for="h_se">{{h_se_label}}</label></li>
				<li class="group" style="{filters_hidden}"><input class="chbox" id="h_no" name="h_no" value="1" type="checkbox" {h_no_checked} onclick="reload()"/><label for="h_no">{{h_no_label}}</label></li>
			<li class="group" style="{filters_hidden}">{{from}}:	
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
			</select>
			</li>
			<li class="group" style="{filters_hidden}"><input class="chbox" id="h_noscore" name="h_noscore" value="1" type="checkbox" {h_noscore_checked} onclick="reload()"/><label for="h_noscore">{{show_noscore}}</label></li>
			</ul>
		</div>
	</td>
</tr>
</table>

</div>
	<script type="text/javascript" language="javascript"><!--
	var h_t = 0;
	var map=null;
	var tlo=null;
	var old_temp_unavail_value=null;
	var old_arch_value=null;
	var lastCoords = null; // hack for IE8, mouse click events have wrong coordinates but strangely enough mousemove is ok :)

// Draw circle with radius 150 m to show contain existing geocaches 
function okrag(srodek,promien)
		{
			if(!srodek || !promien)
				return;

			// default
			var wyp_kolor = '#0000ff';
			var wyp_alfa = 0.25;
			var obr_kolor = '#0000ff';
			var obr_grubosc = 1;
			var obr_alfa = 0.65;
			var dokladnosc = 24;
			
			switch(arguments.length)
			{
				case 8: dokladnosc = arguments[7];
				case 7: wyp_alfa = arguments[6];
				case 6: wyp_kolor = arguments[5];
				case 5: obr_alfa = arguments[4];
				case 4: obr_grubosc = arguments[3];
				case 3: obr_kolor = arguments[2];
			}
		
			var punkty=[];
			for(i=0;i<dokladnosc;i++)
			{
				var kat=360*i/dokladnosc;
				kat = Math.PI*kat/180;
				var srodekXY = map.fromLatLngToDivPixel(srodek);
				var nowyPunktXY = new GPoint(srodekXY.x+parseFloat(promien)*Math.cos(kat),srodekXY.y+parseFloat(promien)*Math.sin(kat));
				punkty.push(map.fromDivPixelToLatLng(nowyPunktXY));
			}
			
			punkty.push(punkty[0]); // powielamy jeszcze raz pierwszy punkt, aby zamknąć okrąg
			
			if(arguments.length>5)
				var poli = new GPolygon(punkty,obr_kolor,obr_grubosc,obr_alfa,wyp_kolor,wyp_alfa);
			else
				var poli = new GPolyline(punkty,obr_kolor,obr_grubosc,obr_alfa);
			return poli;
		}

function ShowCoordsControl() {
}
ShowCoordsControl.prototype = new GControl();
ShowCoordsControl.prototype.initialize = function(map) {
  var container = document.createElement("div");
  var showCoords = document.createElement("div");
  var hideControl = document.createElement("img");

  var icon = document.createElement("img");
  icon.src = "tpl/stdstyle/images/blue/compas20.png";
  icon.alt = "";
  icon.style.marginTop = "-2px"

  this.type = 1;

  this.showCoords = showCoords;

  hideControl.style.width = "16px";
  hideControl.style.height = "16px";
  hideControl.style.marginLeft = "95%";
  hideControl.style.position = "absolute";
  hideControl.style.backgroundColor = "yellow";
  hideControl.style.border = "1px solid black";
  hideControl.src =  "tpl/stdstyle/images/show_hide.png";
//  hideControl.style.backgroundImage = "tpl/stdstyle/images/show_hide.png";
//  container.appendChild(hideControl);

  this.setStyle_(showCoords);
  container.appendChild(showCoords);
  showCoords.appendChild(icon);
  var textNode = document.createTextNode("");
  showCoords.appendChild(textNode);
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
		latstr = lat.toFixed(5) + "°";
		lngstr = lng.toFixed(5) + "°";
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
		latstr = degs1 + "° " + (minutes1 | 0) + "' " + (seconds1.toFixed(2)) + "\"";
		lngstr = degs2 + "° " + (minutes2 | 0) + "' " + (seconds2.toFixed(2)) + "\"";;
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
  elem.style.fontWeight = "bold";
  elem.style.paddingTop = "2px";
  elem.style.width = "225px";
  elem.style.textAlign = "center";
  elem.style.cursor = "pointer";
}




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
			case "10":
				return "owncache"+statusToImageName(status)+".png";
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
						isPng:true,
						opacity:1.0
                    });
			tilelayer.getTileUrl = function(tile, zoom) { return "{cachemap_mapper}?userid={userid}&z="+zoom+"&x="+tile.x+"&y="+tile.y+"&sc={sc}&h_u="+document.getElementById('h_u').checked+"&h_t="+document.getElementById('h_t').checked+"&h_m="+document.getElementById('h_m').checked+"&h_v="+document.getElementById('h_v').checked+"&h_w="+document.getElementById('h_w').checked+"&h_e="+document.getElementById('h_e').checked+"&h_q="+document.getElementById('h_q').checked+"&h_o="+document.getElementById('h_o').checked+"&h_owncache="+document.getElementById('h_owncache').checked+"&h_ignored="+document.getElementById('h_ignored').checked+"&h_own="+document.getElementById('h_own').checked+"&h_found="+document.getElementById('h_found').checked+"&h_noattempt="+document.getElementById('h_noattempt').checked+"&h_nogeokret="+document.getElementById('h_nogeokret').checked+"&h_avail="+document.getElementById('h_avail').checked+"&h_temp_unavail="+document.getElementById('h_temp_unavail').checked+"&h_arch="+document.getElementById('h_arch').checked+"&signes="+document.getElementById('signes').checked+"&waypoints="+document.getElementById('waypoints').checked+"&be_ftf="+document.getElementById('be_ftf').checked+"&h_de="+document.getElementById('h_de').checked+"&h_pl="+document.getElementById('h_pl').checked+"&h_se="+document.getElementById('h_se').checked+"&h_no="+document.getElementById('h_no').checked+"&min_score="+document.getElementById('min_score').value+"&max_score="+document.getElementById('max_score').value+"&h_noscore="+document.getElementById('h_noscore').checked+"&mapid="+get_current_mapid()+"&{searchdata}"; };
			tlo = new GTileLayerOverlay(tilelayer);
	}

	function reload()
	{
		saveMapType();
		map.clearOverlays(tlo);
		addocoverlay();
		map.addOverlay(tlo);
	}
	
	function load() 
	{
	 if (GBrowserIsCompatible()) 
		{
			map = new GMap2(document.getElementById("map_canvas"), {draggableCursor: 'crosshair', draggingCursor: 'pointer'});

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
			var mapControl = new GHierarchicalMapTypeControl(true);
			map.addControl(mapControl);
			map.addControl(new GOverviewMapControl());			
			var showCoords = new ShowCoordsControl();
			map.addControl(showCoords);
			GEvent.addListener(map, "mousemove", function(latlng) {lastCoords = latlng; showCoords.setCoords(latlng);} );

			map.enableScrollWheelZoom();


                        var tileGeoTopo = createWMSLayer('http://sdi.geoportal.gov.pl/wms_topo/wmservice.aspx', 'TOPO_50_65,TOPO_50_42,TOPO_25_65,TOPO_100_80,TOPO_10_92,TOPO_10_65,TOPO_10_42', '',                                                                                                                                                                  
                                                      'image/jpeg');                                                                                                               
                        var tileGeoOrto = createWMSLayer('http://sdi.geoportal.gov.pl/wms_orto/wmservice.aspx', 'ORTOFOTO', '', 'image/jpeg');                                     
                        var layer1 = [tileGeoTopo];                                                                                                                                
                        var layer2 = [tileGeoOrto];                                                                                                                                
                        var layer3 = [tileGeoTopo, G_HYBRID_MAP.getTileLayers()[1]];                                                                                               
                        var layer4 = [tileGeoOrto, G_HYBRID_MAP.getTileLayers()[1]];                                                                                               
                        var custommap1 = new GMapType(layer1, G_SATELLITE_MAP.getProjection(), "Topo", G_SATELLITE_MAP);                                                           
                        var custommap2 = new GMapType(layer2, G_SATELLITE_MAP.getProjection(), "Orto", G_SATELLITE_MAP);                                                           
                        var custommap3 = new GMapType(layer3, G_SATELLITE_MAP.getProjection(), "Topo2", G_SATELLITE_MAP);                                                          
                        var custommap4 = new GMapType(layer4, G_SATELLITE_MAP.getProjection(), "Orto2", G_SATELLITE_MAP);                                                          
                        mapControl.addRelationship(custommap1, custommap3, "{{show_labels}}", true);                                                                               
                        mapControl.addRelationship(custommap2, custommap4, "{{show_labels}}", true);                                                                               
//                        map.addMapType(custommap1);                                                                                                                                
//                        map.addMapType(custommap2);                                                                                                                                
//                        map.addMapType(custommap3);                                                                                                                                
//                        map.addMapType(custommap4);

          // Create a search control
          var searchControl = new google.search.SearchControl();

          // Add in local search
          var localSearch = new google.search.LocalSearch();
          var options = new google.search.SearcherOptions();
          options.setExpandMode(GSearchControl.EXPAND_MODE_OPEN);
          searchControl.addSearcher(localSearch, options);

		            
			localSearch.setCenterPoint(map.getCenter());

          // Tell the searcher to draw itself and tell it where to attach
          searchControl.draw(document.getElementById("search_control"));

        searchControl.setSearchCompleteCallback(this, function(sc, searcher) {
            if(searcher.results.length < 1)
                return;
            var result = searcher.results[0];
            var p = new GLatLng(parseFloat(result.lat), parseFloat(result.lng));
			localSearch.setCenterPoint(p);
            map.setCenter(p, 13, map.getCurrentMapType());
            document.getElementById("search_control").getElementsByTagName("input")[0].value = "";
        });

			// draw circle with radius 150 m to check exist geocaches 
			var circle={circle};
			if(circle==1)  
			{
			var poli = {};
			var punktCentralny = new GLatLng({coords});   
				var poli = okrag(punktCentralny,150,'#0000FF',2,0.5,'#9999CC',0.2,55);
				map.addOverlay(poli);
				var pointc = new GLatLng({coords});
				var new_cache = new GMarker(pointc);
				map.addOverlay(new_cache);
			}		

			map.setMapType({map_type});
			map.addOverlay(tlo);
			GEvent.addListener(map, "moveend", function() 
			{
				localSearch.setCenterPoint(map.getCenter());
			});
			
			GEvent.addListener(map, "zoomend", function() 
			{
				var zoom = map.getZoom();
				if( zoom > 7) {
					document.getElementById('signes').disabled = false;
					document.getElementById('waypoints').disabled = false;
				}
				else {
					document.getElementById('signes').disabled = true;
					document.getElementById('waypoints').disabled = true;
				}
				
				// reset double click timer
				document.getElementById("zoom").value = map.getZoom();
				
			});
			



			var onClickFunc = function(overlay,point) 
			{
				point = lastCoords; // hack for IE8, get coords from last mousemove event instead of the click
				if( point==undefined )
					return;
				
				GDownloadUrl("lib/xmlmap.php?lat="+point.lat()+"&lon="+point.lng()+"&zoom="+map.getZoom()+"&userid={userid}&h_u="+document.getElementById('h_u').checked+"&h_t="+document.getElementById('h_t').checked+"&h_m="+document.getElementById('h_m').checked+"&h_v="+document.getElementById('h_v').checked+"&h_w="+document.getElementById('h_w').checked+"&h_e="+document.getElementById('h_e').checked+"&h_q="+document.getElementById('h_q').checked+"&h_o="+document.getElementById('h_o').checked+"&h_owncache="+document.getElementById('h_owncache').checked+"&h_ignored="+document.getElementById('h_ignored').checked+"&h_own="+document.getElementById('h_own').checked+"&h_found="+document.getElementById('h_found').checked+"&h_noattempt="+document.getElementById('h_noattempt').checked+"&h_nogeokret="+document.getElementById('h_nogeokret').checked+"&h_avail="+document.getElementById('h_avail').checked+"&h_temp_unavail="+document.getElementById('h_temp_unavail').checked+"&h_arch="+document.getElementById('h_arch').checked+"&signes="+document.getElementById('signes').checked+"&be_ftf="+document.getElementById('be_ftf').checked+"&h_pl="+document.getElementById('h_pl').checked+"&h_de="+document.getElementById('h_de').checked+"&h_no="+document.getElementById('h_no').checked+"&h_se="+document.getElementById('h_se').checked+"&min_score="+document.getElementById('min_score').value+"&max_score="+document.getElementById('max_score').value+"&h_noscore="+document.getElementById('h_noscore').checked+"&{searchdata}", function(data, responseCode) 
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
								case "7":
									domain = "http://www.opencaching.se/";
									break;
								case "8":
									domain = "http://www.opencaching.no/";
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


			var onRightClickFunc = function(overlay,point) 
			{
				point = lastCoords; // hack for IE8, get coords from last mousemove event instead of the click
				if( point==undefined )
					return;

				GDownloadUrl("lib/xmlmap.php?lat="+point.lat()+"&lon="+point.lng()+"&zoom="+map.getZoom()+"&userid={userid}&h_u="+document.getElementById('h_u').checked+"&h_t="+document.getElementById('h_t').checked+"&h_m="+document.getElementById('h_m').checked+"&h_v="+document.getElementById('h_v').checked+"&h_w="+document.getElementById('h_w').checked+"&h_e="+document.getElementById('h_e').checked+"&h_q="+document.getElementById('h_q').checked+"&h_o="+document.getElementById('h_o').checked+"&h_ignored="+document.getElementById('h_ignored').checked+"&h_own="+document.getElementById('h_own').checked+"&h_found="+document.getElementById('h_found').checked+"&h_noattempt="+document.getElementById('h_noattempt').checked+"&h_nogeokret="+document.getElementById('h_nogeokret').checked+"&h_avail="+document.getElementById('h_avail').checked+"&h_temp_unavail="+document.getElementById('h_temp_unavail').checked+"&h_arch="+document.getElementById('h_arch').checked+"&signes="+document.getElementById('signes').checked+"&be_ftf="+document.getElementById('be_ftf').checked+"&h_pl="+document.getElementById('h_pl').checked+"&h_de="+document.getElementById('h_de').checked+"&h_no="+document.getElementById('h_no').checked+"&h_se="+document.getElementById('h_se').checked+"&min_score="+document.getElementById('min_score').value+"&max_score="+document.getElementById('max_score').value+"&h_noscore="+document.getElementById('h_noscore').checked+"&{searchdata}", function(data, responseCode) 
					{
						var xml = GXml.parse(data);
						var caches = xml.documentElement.getElementsByTagName("cache");
						var cache_id = caches[0].getAttribute("cache_id");
						if(cache_id != "")
	    					    window.open("viewcache.php?cacheid="+cache_id,"_blank");
					});
			}
			GEvent.addListener(map, "singlerightclick", onRightClickFunc);

			
		}

		if({doopen})
			onClickFunc(tlo, new GLatLng({coords}));
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




/*		GEvent.addDomListener(document.getElementById("amtc_option_1"), "click", function() { saveMapType(); })
		GEvent.addDomListener(document.getElementById("amtc_option_2"), "click", function() { saveMapType(); })
		GEvent.addDomListener(document.getElementById("amtc_option_3"), "click", function() { saveMapType(); })
		GEvent.addDomListener(document.getElementById("amtc_option_4"), "click", function() { saveMapType(); })*/

		 GEvent.addListener(map, "tilesloaded", function() {
			   var mapSelector = document.getElementById("hmtctl");
				if(mapSelector) {
			   		mapSelector.onclick = saveMapType;
				}
	    });

	}
// -->
</script>
