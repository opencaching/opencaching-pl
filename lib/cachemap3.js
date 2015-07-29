/**
 * PHP generated code
 *
 * var attributionMap = {
 *         OSMapa : '&copy; <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> contributors <a href="http://creativecommons.org/licenses/by-sa/2.0/" target="_blank">CC BY-SA</a> | Hosting:<a href="http://trail.pl/" target="_blank">trail.pl</a> i <a href="http://centuria.pl/" target="_blank">centuria.pl</a>',
 *         ...
 * };
 *
 * var mapItems = {
 *         OSMapa : function(){...}
 * };
 * var showMapsWhenMore = {
 *         Topo : true
 * };
 */

var initial_params = null;

var map=null;
var infowindow=null;

var old_temp_unavail_value=null;
var old_arch_value=null;
var refresh_rand="r0";

(function() {
    var lib = document.createElement('script');
    lib.type = 'text/javascript';
    lib.src = '/lib/cachemap3lib.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(lib, s);
})();

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

    switch(arguments.length)
    {
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
    icon.style.marginTop = "-2px";

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

    if(type === 0) {
        latstr = lat.toFixed(5) + "°";
        lngstr = lng.toFixed(5) + "°";
    }
    else if(type === 1) {
        var degs1 = lat | 0;
        var degs2 = lng | 0;
        var minutes1 = ((lat - degs1)*60);
        var minutes2 = ((lng - degs2)*60);
        latstr = degs1 + "° " + minutes1.toFixed(3) + "'";
        lngstr = degs2 + "° " + minutes2.toFixed(3) + "'";
    }
    else if(type === 2) {
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
};

ShowCoordsControl.prototype.setStyle_ = function(elem) {
    elem.style.textDecoration = "none";
    elem.style.color = "#000000";
    elem.style.backgroundColor = "white";
    elem.style.font = "small Arial";
    elem.style.border = "1px solid #717B87";
    elem.style.fontWeight = "bold";
    elem.style.paddingTop = "2px";
    elem.style.width = "225px";
    elem.style.textAlign = "center";
    elem.style.cursor = "pointer";
};

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

function getCurrentOCMapId()
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

function getMapTypeFromOCMapId(value)
{
    switch (value) {
        case 0:
            return google.maps.MapTypeId.ROADMAP;
        case 1:
            return google.maps.MapTypeId.SATELLITE;
        case 2:
            return google.maps.MapTypeId.HYBRID;
        case 3:
            return google.maps.MapTypeId.TERRAIN;
        default:
            return google.maps.MapTypeId.ROADMAP;
    }
}

function prepareCommonFilterParams()
{
    return ""+
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
        "&h_avail=false"+ // used to be permanently set in a hidden input field - document.getElementById('h_avail').checked+
        "&h_temp_unavail="+document.getElementById('h_temp_unavail').checked+
        "&h_arch="+document.getElementById('h_arch').checked+
        "&be_ftf="+document.getElementById('be_ftf').checked+
        "&powertrail_only="+document.getElementById('powertrail_only').checked+
        "&min_score="+document.getElementById('min_score').value+
        "&max_score=3"+ // used to be permanently set in a hidden input field - document.getElementById('max_score').value+
        "&h_noscore="+document.getElementById('h_noscore').checked;
}

function saveMapSettings(mapTypeId)
{
    if (typeof mapTypeId !== 'undefined'){
        jQuery.cookie('mapTypeId', mapTypeId, {expires: 365});
    }
    if (initial_params.start.savesettings === false) return;

    var queryString = "?map_v=3&maptype="+getCurrentOCMapId()+
        prepareCommonFilterParams();
        // These settings are currently ignored
        //"&signes="+document.getElementById('signes').checked+
        //"&waypoints="+document.getElementById('waypoints').checked+
        //"&h_pl="+document.getElementById('h_pl').checked+
        //"&h_de="+document.getElementById('h_de').checked+
        //"&h_no="+document.getElementById('h_no').checked+
        //"&h_se="+document.getElementById('h_se').checked;

    jQuery.get("cachemapsettings.php" + queryString);
}

function get_current_rand() { return refresh_rand; }

function generate_new_rand() {
    t = new Date();
    refresh_rand = "r" + t.getHours() + ":" + t.getMinutes() + ":" + Math.floor(t.getSeconds() / 10) + "0";
}

function addOCOverlay()
{
    var tilelayer = new google.maps.ImageMapType({
        opacity: 1.0,
        tileSize: new google.maps.Size(256, 256),
        getTileUrl: function(tile, zoom) {
            return initial_params.start.cachemap_mapper+"?userid="+initial_params.start.userid+
                "&z="+zoom+"&x="+tile.x+"&y="+tile.y+
                prepareCommonFilterParams()+
                "&rand="+get_current_rand()+
                "&"+initial_params.start.searchdata;
        }
    });
    map.overlayMapTypes.insertAt(0, tilelayer);
}

function reload()
{
    map.overlayMapTypes.removeAt(0);
    addOCOverlay();
    saveMapSettings();
}

function prepareLibXmlMapUrl(clickBounds, rspFormat)
{
    var p1 = clickBounds.getSouthWest();
    var p2 = clickBounds.getNorthEast();

    return "lib/xmlmap.php"+
        "?rspFormat="+rspFormat+"&latmin="+p1.lat()+"&lonmin="+p1.lng()+"&latmax="+p2.lat()+"&lonmax="+p2.lng()+
        "&screenW="+window.innerWidth+
        "&userid="+initial_params.start.userid+
        prepareCommonFilterParams()+
        "&"+initial_params.start.searchdata;
}

function load(additionalControls, searchElement)
{
    // Check if this is a touch device (i.e. phone/tablet) --> if it is, simplify layout
    // Detection based on the following Stack Overflow question:
    // http://stackoverflow.com/questions/4817029/whats-the-best-way-to-detect-a-touch-screen-device-using-javascript
    // **** This check should be kept in sync with analogous check in tpl/stdstyle/lib/menu.php ****
    var isTouchScreen = (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0));

    var ocMapTypeIds = [];
    for (var type in google.maps.MapTypeId) {
        ocMapTypeIds.push(google.maps.MapTypeId[type]);
    }

    map = new google.maps.Map(
        document.getElementById("map_canvas"),
        {
            center: new google.maps.LatLng(initial_params.start.coords[0], initial_params.start.coords[1]),
            zoom: initial_params.start.zoom,
            mapTypeId: getMapTypeFromOCMapId(initial_params.start.map_type),
            mapTypeControlOptions: {
                mapTypeIds: ocMapTypeIds
            },
            scaleControl: true,
            draggableCursor: 'crosshair',
            draggingCursor: 'pointer',
            overviewMapControl: initial_params.start.largemap
        }
    );

    if (initial_params.start.largemap === false) {
        // Disable some controls on a small map to save screen space
        map.setOptions({panControl: false, mapTypeControlOptions: { mapTypeIds: ocMapTypeIds, style: google.maps.MapTypeControlStyle.DROPDOWN_MENU } });
    }

    if (isTouchScreen) {
        // Special options for touch devices (to make UI more touch-friendly)
        if (initial_params.start.fullscreen) {

            if( window.innerWidth < 400) /* hide maps on small screens only */
                initial_params.start.moremaptypes = false;

            // Full-screen mode
            map.setOptions({
                scaleControl: false,
                overviewMapControl: false,
                streetViewControl: false,
                mapTypeControlOptions: {
                    mapTypeIds: ocMapTypeIds,
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.RIGHT_BOTTOM
                }
            });
        } else {
            // Normal mode
            map.setOptions({
                scaleControl: false,
                overviewMapControl: false,
                streetViewControl: false
            });
        };
    }

    addOCOverlay();

    var attributionDiv = createAttributionDiv();
    map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(attributionDiv);
    var mapTypeId2 = jQuery.cookie('mapTypeId');
    if (typeof mapTypeId2 !== 'undefined' && mapTypeId2 !== '' && typeof mapItems[mapTypeId2] !== 'undefined'){
        map.setMapTypeId(mapTypeId2);
        attributionDiv.innerHTML = attributionMap[mapTypeId2] || '';
    }
    for (var mapType in mapItems){
        var mapObj = mapItems[mapType]();
        map.mapTypes.set(mapType, mapObj);

        if (!initial_params.start.moremaptypes && (!showMapsWhenMore[mapType])
             || initial_params.start.moremaptypes
             || mapTypeId2 === mapType)
        {
            ocMapTypeIds.push(mapType);
        }
    }


    google.maps.event.addListener(map, "maptypeid_changed", function() {
        var newMapTypeId = map.getMapTypeId();
        attributionDiv.innerHTML = attributionMap[newMapTypeId] || '';
        saveMapSettings(newMapTypeId);
    });

    document.getElementById("zoom").value = map.getZoom();

    google.maps.event.addListener(map, "zoom_changed", function() {
        document.getElementById("zoom").value = map.getZoom();
    });

    if (initial_params.start.largemap && !isTouchScreen) {
        var showCoords = new ShowCoordsControl(map);
        google.maps.event.addListener(map, "mousemove", function(event) {
            showCoords.setCoords(event.latLng);
        });
    }

    if (searchElement) {
        var placeSearchText = document.getElementById("place_search_text");
        var placeSearchButton = document.getElementById("place_search_button");
        var geocoder = new google.maps.Geocoder();

        var doSearchHandler = function() {
            geocoder.geocode({ address: placeSearchText.value }, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    map.fitBounds(results[0].geometry.viewport);
                    placeSearchText.value = results[0].address_components[0].short_name;
                    placeSearchText.style.backgroundColor = "#FFFFFF";
                } else if (status === google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
                    // Google Maps API limit reached
                    placeSearchText.style.backgroundColor = "#FFFF99";
                } else {
                    // Other geocoding error (e.g. wrong location/address)
                    placeSearchText.style.backgroundColor = "#FFCCCC";
                }
            });
        };

        google.maps.event.addDomListener(placeSearchButton, "click", doSearchHandler);
        google.maps.event.addDomListener(placeSearchText, "keydown", function(ev) {
            if (ev.keyCode === 13) {
                doSearchHandler();
            }
        });
    }

    if (initial_params.start.circle === 1)
    {
        // draw circle with radius 150 m to check existing geocaches
        var punktCentralny = new google.maps.LatLng(initial_params.start.coords[0], initial_params.start.coords[1]);
        var poli = okrag(punktCentralny,150,'#0000FF',2,0.5,'#9999CC',0.2,55);
        poli.setMap(map);
        var new_cache = new google.maps.Marker({position: punktCentralny, map: map});
    }

    // This is only necessary to do pixel <-> lat/lng calculations
    var overlay = new google.maps.OverlayView();
    overlay.draw = function() {};
    overlay.setMap(map);

    var calcClickBounds = function(ll) {
        var proj = overlay.getProjection();
        if (typeof proj === "undefined") {
            // Projection is not yet available when map is not fully positioned
            // This may happen when showing the info window on initial page load (doopen is true)
            // Send same bounds as min and max - will be handled by xmlmap.php
            return new google.maps.LatLngBounds(ll, ll);
        } else {
            var xyCenter = proj.fromLatLngToContainerPixel(ll);
            var xy1 = new google.maps.Point(xyCenter.x - 16, xyCenter.y + 16);
            var xy2 = new google.maps.Point(xyCenter.x + 16, xyCenter.y - 16);
            var ll1 = proj.fromContainerPixelToLatLng(xy1);
            var ll2 = proj.fromContainerPixelToLatLng(xy2);
            return new google.maps.LatLngBounds(ll1, ll2);
        }
    };

    // Allow only one pending click or right-click request
    var pendingClickRequest = null;
    var pendingClickRequestTimeout = 10000; // default timeout - in milliseconds

    var onClickFunc = function(event) {

        if (pendingClickRequest) {
            pendingClickRequest.abort();
            pendingClickRequest = null;
        }

        var clickBounds = calcClickBounds(event.latLng);
        var clickRect = new google.maps.Rectangle({bounds: clickBounds, strokeColor: '#080', fillColor: '#9c9', map: map});

        var pendingClickRequest = jQuery.ajax({
            url: prepareLibXmlMapUrl(clickBounds, 'html')
        });

        pendingClickRequest.always(function() {
            clickRect.setMap(null);
            pendingClickRequest = null;
        });

        pendingClickRequest.done(function( data ) {
            if(data === "" ){ //nothing to display
                if (infowindow){ //previous info window is displayed
                    infowindow.close();
                }
                return; //nothing more to do here
            }

            if (infowindow === null) {
                infowindow = new google.maps.InfoWindow();
            }

            infowindow.setContent(data);
            //find cords in cache data
            cords = $($.parseHTML(data)).filter('input[name="cache_cords"]').val();
            cords = jQuery.parseJSON(cords);

            infowindow.setPosition(new google.maps.LatLng(cords.latitude,cords.longitude));
            infowindow.open(map);
        });

    };

    google.maps.event.addListener(map, 'click', onClickFunc);

    var onRightClickFunc = function(event)
    {
        if (pendingClickRequest) {
            pendingClickRequest.abort();
            pendingClickRequest = null;
        }

        var clickBounds = calcClickBounds(event.latLng);
        var clickRect = new google.maps.Rectangle({bounds: clickBounds, strokeColor: '#008', fillColor: '#99c', map: map});
        var url = prepareLibXmlMapUrl(clickBounds,'url');

        pendingClickRequest = jQuery.ajax({
            url: url,
            timeout: pendingClickRequestTimeout
        });

        pendingClickRequest.always(function() {
            clickRect.setMap(null);
            pendingClickRequest = null;
        });

        pendingClickRequest.done(function( data ) {
            if(Object.keys(data).length > 0){
                var url = data[Object.keys(data)[0]].url;
                window.open(url,"_blank");
            }
        });

    };

    google.maps.event.addListener(map, 'rightclick', onRightClickFunc);

    if(initial_params.start.doopen)
        onClickFunc({ latLng: new google.maps.LatLng(initial_params.start.coords[0], initial_params.start.coords[1]) });

    if(initial_params.start.fromlat !== initial_params.start.tolat) {
        var area = new google.maps.LatLngBounds();
        area.extend(new google.maps.LatLng(initial_params.start.fromlat, initial_params.start.fromlon));
        area.extend(new google.maps.LatLng(initial_params.start.tolat,   initial_params.start.tolon));
        map.fitBounds(area);
    }

    for (var i = 0; i < additionalControls.length; i++) {
        var ac = additionalControls[i];
        map.controls[ac.position].push(ac.control);
    }

    if (initial_params.start.fullscreen && $.browser.msie && (parseInt($.browser.version, 10) < 8)) {
        // Dirty hack for IE7 in full-screen mode only. For unknown reason map does not initially fill entire window.
        // Triggering the 'resize' event fixes this problem.
        setTimeout(function() {
            google.maps.event.trigger(map, 'resize');
            map.setCenter(new google.maps.LatLng(initial_params.start.coords[0], initial_params.start.coords[1]));
        }, 1000);
    }
}

function fullscreen() {
    document.cookie = "forceFullScreenMap=on;";
    window.location = "cachemap-full.php"+
        "?lat="+map.getCenter().lat()+
        "&lon="+map.getCenter().lng()+
        "&inputZoom="+map.getZoom()+
        "&"+initial_params.start.searchdata+initial_params.start.boundsurl+initial_params.start.extrauserid;
}


var geoloc_status = 0;
/*
 * Possible values of geoloc_status:
 *   0 - initial
 *   1 - get position in progress
 *   2 - position acquired
 *   3 - error - position not available
 */

var curr_pos_marker = { point: null, circle: null };

function changeGeolocStatus(new_status) {
    geoloc_status = new_status;
    document.getElementById("current_position_icon").src = "/images/map_geolocation_" + geoloc_status + ".png";
}

function calculateZoomLevel(accuracy) {
    var zoom = 16;
    if (accuracy >   300) zoom = 15;
    if (accuracy >   600) zoom = 14;
    if (accuracy >  1200) zoom = 13;
    if (accuracy >  2400) zoom = 12;
    if (accuracy >  5000) zoom = 11;
    if (accuracy > 10000) zoom = 10;
    return zoom;
}

function processCurrentPosition(position) {
    var pt = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
    var accuracy = position.coords.accuracy;

    if (curr_pos_marker.circle === null) {
        curr_pos_marker.circle = okrag(pt, accuracy, '#0044CC', 2, 0.5, '#99AACC', 0.2, 55);
        curr_pos_marker.circle.setMap(map);
    } else {
        curr_pos_marker.circle.setCenter(pt);
        curr_pos_marker.circle.setRadius(accuracy);
    }

    if (curr_pos_marker.point === null) {
        curr_pos_marker.point = new google.maps.Marker({
            position: pt, map: map,
            icon: {
                url: "/images/markers/map_current_location.png",
                size: new google.maps.Size(32, 32),
                anchor: new google.maps.Point(16, 16)
            }
        });
    } else {
        curr_pos_marker.point.setPosition(pt);
    }

    map.setCenter(pt);
    map.setZoom(calculateZoomLevel(accuracy));

    changeGeolocStatus(2); // Success
}

function handleGetPositionError(positionError) {
    if (positionError.code === 1) { // Permission denied
        changeGeolocStatus(0); // User has denied geolocation - return to initial state
    } else {
        changeGeolocStatus(3); // Indicate actual problem with getting position
    }
}

function getCurrentPosition() {
    if (!("geolocation" in navigator))
        return;

    changeGeolocStatus(1); // In progress
    navigator.geolocation.getCurrentPosition(processCurrentPosition, handleGetPositionError, { enableHighAccuracy: true });
}
