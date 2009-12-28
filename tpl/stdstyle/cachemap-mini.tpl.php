
<div id="map_canvas" style="width: 100%; height: 100%; position: absolute; top: 0p; bottom: 0px;">
</div>

<!--
http://rushbase.net:5580/~rush/ocpl/lib/cgi-bin/mapper.fcgi?userid=8595&z=11&x=1127&y=654&sc=false&h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_ignored=false&h_own=false&h_found=false&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=false&h_arch=false&signes=false&be_ftf=false&h_de=false&h_pl=false&min_score=1&max_score=5&h_noscore=false&mapid=0

http://rushbase.net:5580/~rush/ocpl/lib/cgi-bin/mapper.fcgi?userid=8595&z=13&x=4520&y=2616&sc=0&h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_ignored=false&h_own=false&h_found=false&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=true&h_arch=true&signes=true&be_ftf=false&h_de=true&h_pl=true&min_score=1&max_score=5&h_noscore=true&mapid=0

<input id="h_u" name="h_u" value="false" type="hidden"  />
<input id="h_t" name="h_t" value="false" type="hidden"  />
<input id="h_m" name="h_m" value="false" type="hidden"  />
<input id="h_v" name="h_v" value="false" type="hidden"  />
<input id="h_w" name="h_w" value="false" type="hidden"  />
<input id="h_e" name="h_e" value="false" type="hidden"  />
<input id="h_q" name="h_q" value="false" type="hidden"  />
<input id="h_o" name="h_o" value="false" type="hidden"  />
<input id="h_ignored" name="h_ignored" value="false" type="hidden"  />
<input id="h_own" name="h_own" value="false" type="hidden"  />
<input id="h_found" name="h_found" value="false" type="hidden"  />
<input id="h_noattempt" name="h_noattempt" value="false" type="hidden"  />
<input id="h_nogeokret" name="h_nogeokret" value="false" type="hidden"  />
<input id="h_avail" name="h_avail" value="false" type="hidden"  />
<input id="h_temp_unavail" name="h_temp_unavail" value="true" type="hidden"  />
<input id="h_arch" name="h_arch" value="true" type="hidden"  />
<input class="chbox" id="signes" name="signes" value="true" type="hidden" />
<input class="chbox" id="be_ftf" name="be_ftf" value="false" type="hidden" />
<input class="chbox" id="h_pl" name="h_pl" value="false" type="hidden" />
<input class="chbox" id="h_de" name="h_de" value="false" type="hidden" />
<input id="min_score" name="min_score" type="hidden" value="-3" />
<input id="max_score" name="max_score" type="hidden" value="3.0" />
<input class="chbox" id="h_noscore" name="h_noscore" value="false" type="hidden" />




-->



<input class="chbox" id="zoom" name="zoom" value="{zoom}" type="hidden" />

	<script type="text/javascript" language="javascript"><!--
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

			tilelayer.getTileUrl = function(tile, zoom) { return "lib/cgi-bin/mapper.fcgi?userid={userid}&z="+zoom+"&x="+tile.x+"&y="+tile.y+"&sc=0&h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_ignored=false&h_own=false&h_found=false&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=true&h_arch=true&signes=true&be_ftf=false&h_de=true&h_pl=true&min_score=-3&max_score=3&h_noscore=true&mapid="+get_current_mapid();};
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
			map.addControl(new GHierarchicalMapTypeControl(true));
			map.addControl(new GOverviewMapControl());			

			map.setMapType({map_type});
			map.addOverlay(tlo);
			GEvent.addListener(map, "moveend", function() 
			{
			});
			
			GEvent.addListener(map, "zoomend", function() 
			{
				var zoom = map.getZoom();
				if( zoom > 13 )
					document.getElementById('signes').disabled = false;
				else
					document.getElementById('signes').disabled = true;
				
				// reset double click timer
				document.getElementById("zoom").value = map.getZoom();
				
			});
			

			var onClickFunc = function(overlay,point) 
			{
				if( point==undefined )
					return;
				
				GDownloadUrl("lib/xmlmap.php?lat="+point.lat()+"&lon="+point.lng()+"&zoom="+map.getZoom()+"&userid={userid}&h_u=false&h_t=false&h_m=false&h_v=false&h_w=false&h_e=false&h_q=false&h_o=false&h_ignored=false&h_own=false&h_found=false&h_noattempt=false&h_nogeokret=false&h_avail=false&h_temp_unavail=true&h_arch=true&signes=true&be_ftf=false&h_de=true&h_pl=true&min_score=-3&max_score=3&h_noscore=true", function(data, responseCode) 
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

		if({doopen})
			onClickFunc(tlo, new GLatLng({coords}));
	}
// -->
</script>
