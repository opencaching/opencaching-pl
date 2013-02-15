<div class="content2-pagetitle">
	<img src="tpl/stdstyle/images/blue/world.png" class="icon32" style='margin: 0 4px 3px 6px'/>
	{{user_map}} <b style='color: #000'>{username}</b>
</div>

<style>
	#shortcut_icons { position: relative }
	#shortcut_icons img { position: absolute; top: -23px; cursor: pointer; }
	.opt_table input { border: 0; }
	.opt_table { background: #eee; border: 1px solid #ccc; }
	.opt_table th { background: #888; padding: 3px 8px 5px 8px; font-family: Tahoma; font-size: 13px; font-weight: bold; color: #fff; }
	.opt_table td { padding: 6px; font-family: Tahoma; font-size: 13px; vertical-align: top; }
	.opt_table select { padding: 1px; font-family: Tahoma; font-size: 13px; border: 1px solid #888; }
	.opt_table td.i { position: relative; width: 35px; display: block; }
	.opt_table td.i img { position: absolute; top: 0; }
	.opt_table .dim { color: #888; }
	.opt_table .dim img { opacity: .3; }
	img.dim { opacity: .3; }
</style>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script>
	$(function() {
		/*var left = 0;
		var $shortcuts = $('#shortcut_icons');
		$('#cache_types input').each(function() {
			var $img = $("<img/>");
			$img.attr('src', $(this).closest('tr').find('img').attr('src'));
			$img.css('left', left + "px");
			$img.addClass($(this).attr('name'));
			$img.data('type', $(this).attr('name'));
			$shortcuts.append($img);
			left += 28;
		});*/
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
		/*$shortcuts.find('img').click(function() {
			var $check = $('#' + $(this).data('type'));
			$check.prop('checked', !$check.prop('checked'));
			$check.each(checkbox_changed);
			reload();
		});*/
	});
</script>

<div style='margin-right: 6px;' style='position: relative'>
	<div id='shortcut_icons'></div>
	<table style='border: 1px solid #ccc; background: #eee; padding: 3px 6px 3px 8px; width: 100%; margin-bottom: 10px;'>
		<tr>
			<td>
				<div id="ext_search">
					<div id="search_control" style="float: left;">&nbsp;</div>
				</div>
			</td>
			<td>
				<table style='float: right;'><tr>
					<td style='font-size: 13px;'>
						{{current_zoom}}:
						<input type="text" id="zoom" size="2" value="{zoom}" disabled="disabled" style='border: 0; font-weight: bold; font-size: 13px; background: transparent'/>
					</td>
					<td><a onclick="fullscreen();" style='cursor: pointer'><img src="images/fullscreen.png" alt="{{fullscreen}}"/></a></td>
					<td><a onclick="generate_new_rand(); reload();" style='cursor: pointer'><img src="images/refresh.png"/></a></td>
				</tr></table>
			</td>
		</tr>
	</table>
</div>

<div id="map_canvas" style="width: {map_width}; height: {map_height}; border: 1px solid #888;"></div>

<div style='margin: 10px auto'>
	<table id='cache_types' class='opt_table' cellspacing="0" style='float: left'>
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
						<td><input class="chbox" id="h_q" name="h_q" value="1" type="checkbox" {h_q_checked} onclick="reload()"/><label for="h_q">&nbsp;Quiz</label></td>
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

	<div style='display:none'>
		<!-- These filters are permanently hidden. However, some scripts use them, that's why they're not deleted. -->
		<input class="chbox" id="h_avail" name="h_avail" value="1" type="checkbox" onclick="reload()"/><label for="h_avail">{{ready_to_find}}</label>
		<input class="chbox" id="signes" name="signes" value="1" type="checkbox" {signes_checked} onclick="reload()" disabled="disabled"/><label for="signes">{{show_signes}}</label>
		<input class="chbox" id="waypoints" name="waypoints" value="1" type="checkbox" {waypoints_checked} onclick="reload()" disabled="disabled"/><label for="waypoints">{{show_waypoints}}</label>
		<input class="chbox" id="h_pl" name="h_pl" value="1" type="checkbox" {h_pl_checked} onclick="reload()"/><label for="h_pl">{{h_pl_label}}</label>
		<input class="chbox" id="h_de" name="h_de" value="1" type="checkbox" {h_de_checked} onclick="reload()"/><label for="h_de">{{h_de_label}}</label>
		<input class="chbox" id="h_se" name="h_se" value="1" type="checkbox" {h_se_checked} onclick="reload()"/><label for="h_se">{{h_se_label}}</label>
		<input class="chbox" id="h_no" name="h_no" value="1" type="checkbox" {h_no_checked} onclick="reload()"/><label for="h_no">{{h_no_label}}</label>
		<select id="max_score" name="max_score" onchange="reload()" style='display:none'>
			<!--<option value="0.499" {max_sel1}>{{rating_poor}}</option>
			<option value="1.199" {max_sel2}>{{rating_mediocre}}</option>
			<option value="1.999" {max_sel3}>{{rating_avarage}}</option>
			<option value="2.499" {max_sel4}>{{rating_good}}</option>-->
			<option value="3.000" selected>{{rating_excellent}}</option>
		</select>
	</div>
	
	<table id='other_options' class='opt_table' cellspacing="0" style='float: left; margin-left: 10px'>
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
					<input class="chbox" id="be_ftf" name="be_ftf" value="1" type="checkbox" {be_ftf_checked} onclick="reload();check_field()"/><label for="be_ftf">&nbsp;pokaż tylko FTFy!</label>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<div>
					<center>
						Minimalna ocena:
						<select id="min_score" name="min_score" onchange="reload()">
							<option value="-3" {min_sel1}>dowolna ocena</option>
							<!--<option value="0.5" {min_sel2}>pomiń najsłabsze skrzynki</option>-->
							<option value="1.2" {min_sel3}>przynajmniej "{{rating_avarage}}"</option>
							<option value="2" {min_sel4}>przynajmniej "{{rating_good}}" :)</option>
							<option value="2.5" {min_sel5}>tylko {{rating_excellent}} :D</option>
						</select>
					</center>
				</div>
				<div style='margin-top: 5px'><center>
					<input class="chbox" id="h_noscore" name="h_noscore" value="1" type="checkbox" {h_noscore_checked} onclick="reload()"/><label for="h_noscore">&nbsp;pokaż również skrzynki bez oceny</label>
				</center></div>
			</td>
		</tr>
	</table>
	<div style='clear: both'></div>
</div>
	
<script type="text/javascript" language="javascript"><!--

	var h_t = 0;
	var map=null;
	var infowindow=null;
	var tlo=null;
	var old_temp_unavail_value=null;
	var old_arch_value=null;
	var refresh_rand="r0";

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

		return new google.maps.Circle({
			center: srodek, radius: promien,
			strokeColor: obr_kolor, strokeWeight: obr_grubosc, strokeOpacity: obr_alfa,
			fillColor: wyp_kolor, fillOpacity: wyp_alfa,
			clickable: false
		});
	}

	function ShowCoordsControl(map) {
		var container = document.createElement("div");
		var showCoords = document.createElement("div");

		var icon = document.createElement("img");
		icon.src = "tpl/stdstyle/images/blue/compas20.png";
		icon.alt = "";
		icon.style.marginTop = "-2px"

		this.type = 1;

		this.showCoords = showCoords;

		this.setStyle_(showCoords);
		container.appendChild(showCoords);
		showCoords.appendChild(icon);
		var textNode = document.createTextNode("");
		showCoords.appendChild(textNode);
		showCoords.owner = this;

		map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(container);
		
		google.maps.event.addDomListener(showCoords, "click", function() {
			this.owner.type = ((this.owner.type + 1) % 3);
			this.owner.setCoords(this.owner.lastLatLng);
		});

		this.setCoords(map.getCenter());

		return this;
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
		switch (map.getMapTypeId()) {
			case google.maps.MapTypeId.ROADMAP:
				return 0;
			case google.maps.MapTypeId.SATELLITE:
				return 1;
			case google.maps.MapTypeId.HYBRID:
				return 2;
			case google.maps.MapTypeId.TERRAIN:
				return 3;
			default:
				return 0;
		}
	}

	function get_current_rand() { return refresh_rand; }
	
	function generate_new_rand() {
		t = new Date();
		refresh_rand = "r" + t.getHours() + ":" + t.getMinutes() + ":" + Math.floor(t.getSeconds() / 10) + "0";
	}

	function addocoverlay()
	{
		var tilelayer = new google.maps.ImageMapType({
			opacity: 1.0,
			tileSize: new google.maps.Size(256, 256),
			getTileUrl: function(tile, zoom) {
			return "{cachemap_mapper}?userid={userid}"+
				"&z="+zoom+
				"&x="+tile.x+
				"&y="+tile.y+
				"&h_u="+document.getElementById('h_u').checked+
				"&h_t="+document.getElementById('h_t').checked+
				"&h_m="+document.getElementById('h_m').checked+
				"&h_v="+document.getElementById('h_v').checked+
				"&h_w="+document.getElementById('h_w').checked+
				"&h_e="+document.getElementById('h_e').checked+
				"&h_q="+document.getElementById('h_q').checked+
				"&h_o="+document.getElementById('h_o').checked+
				"&h_owncache="+document.getElementById('h_owncache').checked+
				"&h_ignored="+document.getElementById('h_ignored').checked+
				"&h_own="+document.getElementById('h_own').checked+
				"&h_found="+document.getElementById('h_found').checked+
				"&h_noattempt="+document.getElementById('h_noattempt').checked+
				"&h_nogeokret="+document.getElementById('h_nogeokret').checked+
				"&h_avail="+document.getElementById('h_avail').checked+
				"&h_temp_unavail="+document.getElementById('h_temp_unavail').checked+
				"&h_arch="+document.getElementById('h_arch').checked+
				"&be_ftf="+document.getElementById('be_ftf').checked+
				"&min_score="+document.getElementById('min_score').value+
				"&max_score="+document.getElementById('max_score').value+
				"&h_noscore="+document.getElementById('h_noscore').checked+
				"&rand="+get_current_rand()+
				"&{searchdata}"+
				"";
			}
		});
		map.overlayMapTypes.insertAt(0, tilelayer); 
	}

	function reload()
	{
		map.overlayMapTypes.removeAt(0);
		addocoverlay();
	}

	function prepareLibXmlMapUrl(point)
	{
		return "lib/xmlmap.php"+
			"?lat="+point.lat()+
			"&lon="+point.lng()+
			"&zoom="+map.getZoom()+
			"&userid={userid}"+
			"&h_u="+document.getElementById('h_u').checked+
			"&h_t="+document.getElementById('h_t').checked+
			"&h_m="+document.getElementById('h_m').checked+
			"&h_v="+document.getElementById('h_v').checked+
			"&h_w="+document.getElementById('h_w').checked+
			"&h_e="+document.getElementById('h_e').checked+
			"&h_q="+document.getElementById('h_q').checked+
			"&h_o="+document.getElementById('h_o').checked+
			"&h_owncache="+document.getElementById('h_owncache').checked+
			"&h_ignored="+document.getElementById('h_ignored').checked+
			"&h_own="+document.getElementById('h_own').checked+
			"&h_found="+document.getElementById('h_found').checked+
			"&h_noattempt="+document.getElementById('h_noattempt').checked+
			"&h_nogeokret="+document.getElementById('h_nogeokret').checked+
			"&h_avail="+document.getElementById('h_avail').checked+
			"&h_temp_unavail="+document.getElementById('h_temp_unavail').checked+
			"&h_arch="+document.getElementById('h_arch').checked+
			"&be_ftf="+document.getElementById('be_ftf').checked+
			"&min_score="+document.getElementById('min_score').value+
			"&max_score="+document.getElementById('max_score').value+
			"&h_noscore="+document.getElementById('h_noscore').checked+
			"&{searchdata}";
	}

	function load() 
	{
		var ocMapTypeIds = [];
		for (var type in google.maps.MapTypeId) {
			ocMapTypeIds.push(google.maps.MapTypeId[type]);
		}
		ocMapTypeIds.push("OSM");
		ocMapTypeIds.push("UMP");

		map = new google.maps.Map(
			document.getElementById("map_canvas"),
			{
				center: new google.maps.LatLng({coords}),
				zoom: {zoom},
				mapTypeId: {map_type},
				mapTypeControlOptions: {
					mapTypeIds: ocMapTypeIds
				},
				draggableCursor: 'crosshair',
				draggingCursor: 'pointer'
			}
		);

		addocoverlay();

	    var osmMapType = new google.maps.ImageMapType({
			getTileUrl: function (point, zoom) {
				return "http://tile.openstreetmap.pl/"  + zoom + "/" + point.x + "/" + point.y + ".png";
				// return "http://tile.openstreetmap.org/" + zoom + "/" + point.x + "/" + point.y + ".png";
			},
			tileSize: new google.maps.Size(256, 256),
			name: "OSM",
			maxZoom: 18
		});
	    map.mapTypes.set("OSM", osmMapType);
	    
	    var umpMapType = new google.maps.ImageMapType({
			getTileUrl: function (point, zoom) {
				return "http://tiles.ump.waw.pl/ump_tiles/" + zoom + "/" + point.x + "/" + point.y + ".png";
			},
			tileSize: new google.maps.Size(256, 256),
			name: "UMP",
			maxZoom: 18
		});
		map.mapTypes.set("UMP", umpMapType);

		var attributionDiv = document.createElement('div');
		attributionDiv.id = "map-copyright";
		attributionDiv.style.fontSize = "10px";
		attributionDiv.style.fontFamily = "Arial, sans-serif";
		attributionDiv.style.padding = "3px 6px";
		attributionDiv.style.whiteSpace = "nowrap";
		attributionDiv.style.opacity = "0.7";
		attributionDiv.style.background = "#fff";
		map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(attributionDiv);

		google.maps.event.addListener(map, "maptypeid_changed", function() {
			var newMapTypeId = map.getMapTypeId();
			if (newMapTypeId === "OSM") {
				attributionDiv.innerHTML = '&copy; <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> contributors <a href="http://creativecommons.org/licenses/by-sa/2.0/" target="_blank">CC BY-SA</a> | Hosting:<a href="http://trail.pl/" target="_blank">trail.pl</a>';
			}
			else if (newMapTypeId === "UMP") {
				attributionDiv.innerHTML = '&copy; Mapa z <a href="http://ump.waw.pl/" target="_blank">UMP-pcPL</a>';
			}  
			else {
				attributionDiv.innerHTML = '';
			}
			saveMapType();
		});

		document.getElementById("zoom").value = map.getZoom();

		google.maps.event.addListener(map, "zoom_changed", function() {
			document.getElementById("zoom").value = map.getZoom();
		});

		var showCoords = new ShowCoordsControl(map);
		google.maps.event.addListener(map, "mousemove", function(event) {
			showCoords.setCoords(event.latLng);
		});

		/*
		var tileGeoTopo = createWMSLayer('http://sdi.geoportal.gov.pl/wms_topo/wmservice.aspx', 'TOPO_50_65,TOPO_50_42,TOPO_25_65,TOPO_100_80,TOPO_10_92,TOPO_10_65,TOPO_10_42', '', 'image/jpeg');
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
		//map.addMapType(custommap1);
		//map.addMapType(custommap2);
		//map.addMapType(custommap3);
		//map.addMapType(custommap4);
		*/

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
			var p = new google.maps.LatLng(parseFloat(result.lat), parseFloat(result.lng));
			localSearch.setCenterPoint(p);
			map.setCenter(p);
			map.setZoom(13);
			document.getElementById("search_control").getElementsByTagName("input")[0].value = "";
		});

		google.maps.event.addListener(map, "idle", function() {
			localSearch.setCenterPoint(map.getCenter());
		});

		var circle={circle};
		if (circle==1)  
		{
			// draw circle with radius 150 m to check existing geocaches 
			var punktCentralny = new google.maps.LatLng({coords});   
			var poli = okrag(punktCentralny,150,'#0000FF',2,0.5,'#9999CC',0.2,55);
			poli.setMap(map);
			var new_cache = new google.maps.Marker({position: punktCentralny, map: map});
		}

		var onClickFunc = function(event) {

			point = event.latLng;
			
			jQuery.get(prepareLibXmlMapUrl(point), function(data, status, jqxhr) {
				var xml = jqxhr.responseXML;

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
				var size = caches[0].getAttribute("size");
				var status = caches[0].getAttribute("status");
				var user_id = caches[0].getAttribute("user_id");
				var founds = caches[0].getAttribute("founds");
				var notfounds = caches[0].getAttribute("notfounds");
				var node = caches[0].getAttribute("node");
				
				if( cache_id != "" )
				{
					var show_score;
					var show_size;
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
					
					if( type == 6 ) // event
					{
						found_attended = "{{attendends}}";
						notfound_will_attend = "{{will_attend}}";
						show_size = "";
					}
					else
					{
						found_attended = "{{found}}";
						notfound_will_attend = "{{not_found}}";
						show_size = "<br><b>{{size}}:<\/b> " + size;
					}

					infoWindowContent += "<table border=\"0\" width=\"350\" height=\"120\" class=\"table\">";
					infoWindowContent += "<tr><td colspan=\"2\" width=\"100%\"><table cellspacing=\"0\" width=\"100%\"><tr><td width=\"90%\">";
					infoWindowContent += "<center><img align=\"left\" width=\"20\" height=\"20\" src=\"tpl/stdstyle/images/cache/"+typeToImageName(type, status)+"\" /><\/center>";
					infoWindowContent += "&nbsp;<a href=\""+domain+"viewcache.php?cacheid=" + cache_id + "\" target=\"_blank\">" + name + "<\/a>";
					infoWindowContent += "<\/td><td width=\"10%\">";
					infoWindowContent += "<b>"+wp+"<\/b><\/td><\/tr><\/table>";
					infoWindowContent += "<\/td><\/tr>";
					infoWindowContent += "<tr><td width=\"70%\" valign=\"top\">";
					infoWindowContent += "<b>{{created_by}}:<\/b> " + username + show_size + show_score + print_topratings;
					
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

					if (infowindow === null) {
						infowindow = new google.maps.InfoWindow();
					}

					infowindow.setContent(infoWindowContent);
					infowindow.setPosition(new google.maps.LatLng(lat,lon));

					infowindow.open(map);
				}
			});
		};

		google.maps.event.addListener(map, 'click', onClickFunc);

		var onRightClickFunc = function(event) 
		{
			point = event.latLng;

			jQuery.get(prepareLibXmlMapUrl(point), function(data, status, jqxhr) {
				var caches = jqxhr.responseXML.documentElement.getElementsByTagName("cache");
				var cache_id = caches[0].getAttribute("cache_id");
				if(cache_id != "")
					window.open("viewcache.php?cacheid="+cache_id, "_blank");
			});
		};

		google.maps.event.addListener(map, 'rightclick', onRightClickFunc);

		if({doopen})
			onClickFunc({ latLng: new google.maps.LatLng({coords}) });

		if({fromlat} != {tolat}) {
			var area = new google.maps.LatLngBounds();
			area.extend(new google.maps.LatLng({fromlat}, {fromlon}));
			area.extend(new google.maps.LatLng({tolat}, {tolon}));
			map.fitBounds(area);
		}

	}
	
	function fullscreen() {
		window.location = "cachemap-full.php"+
			"?lat="+map.getCenter().lat()+
			"&lon="+map.getCenter().lng()+
			"&inputZoom="+map.getZoom()+
			"&{searchdata}{boundsurl}{extrauserid}";
	}
// -->
</script>
