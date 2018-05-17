/**
 * There will be present php generated code
 * with map configuration based on settings.inc.php
 *
 * var attributionMap = {
 *         OSMapa : '&copy; <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> contributors <a href="http://creativecommons.org/licenses/by-sa/2.0/" target="_blank">CC BY-SA</a> | Hosting:<a href="http://trail.pl/" target="_blank">trail.pl</a> i <a href="http://centuria.pl/" target="_blank">centuria.pl</a>',
 *         ...
 * };
 *
 * var mapItems = {
 *         OSMapa : function(){...}
 * };
 */

/**
 * Entry point to this script
 * This function loads OC map
 *
 * @param params - initial params for the map widget
 */
function loadOcMap(params) {

    // init global map object
    map = new google.maps.Map(
            document.getElementById(params.mapCanvasId), {}
    );

    // access to map object because of WMSImageMapTypeOptions
    window.getGoogleMapObject = function(){ return map; }

    // prepare OC map list
    var ocMapTypeIds = []; //list of mapTypeIds for control

    //add google maps
    for (var type in google.maps.MapTypeId) {
       ocMapTypeIds.push( google.maps.MapTypeId[type] );
    }

    //add custom OC maps
    for (var mapType in mapItems){ //mapItems contains custom OC maps
        map.mapTypes.set(mapType, mapItems[mapType]()); //add this OC map

        ocMapTypeIds.push(mapType);
    }

    //store list of mapTypesIds in params
    params.__ocMapTypesIds = ocMapTypeIds;

    //overrides for touch screens / small maps etc...
    var canvasWidth = $('#'+params.mapCanvasId).width();

    //combo + horizontal maps > 730px
    if( canvasWidth < 730 && params.customControls.hasOwnProperty('ctrlCombo')){

        //switch mapTypeControl to dropdown menu
        params.mapTypeControl.style = google.maps.MapTypeControlStyle.DROPDOWN_MENU;

        //hide search button and resize search input
        if(params.customControls.hasOwnProperty('search')){
            $('#'+params.customControls.search.but_id).hide();
            params.customControls.search.but_id = null;
            $('#'+params.customControls.search.input_id).width('3.5em');
        }

        //hide cords display
        if(params.customControls.hasOwnProperty('coordsUnderCursor')){
            params.customControls.coordsUnderCursor = {};
        }

        //hide refresh button
        if(params.customControls.hasOwnProperty('refreshButton')){
            params.customControls.refreshButton = {};
        }
    }

    // thin combo + dropdown map menu > 320px
    if( canvasWidth < 320 && params.customControls.hasOwnProperty('ctrlCombo')){
        //move dropdown below
        params.mapTypeControl.pos = google.maps.ControlPosition.LEFT_TOP;
    }

    // disable coordsUnderCursor display on small screens
    // usually there are no mouse cursor
    if( canvasWidth < 600 && isTouchScreen() ){
        params.mapTypeControl.coordsUnderCursor = {};
    }

    //default map settings
    var map_options = {
        center:
            new google.maps.LatLng(params.coords[0], params.coords[1]),
        zoom: params.zoom,

        disableDefaultUI: true, // by default disable all controls and show:
        scaleControl: true,     // show scale on the bottom of map
        zoomControl: true,      // +/- constrols
        mapTypeControl: true,   // list of the maps
        streetViewControl: true,// streetview guy
        rotateControl:true,     // this is visible only on huge zoom
        keyboardShortcuts: true,// for example key '+' = zoom+
        clickableIcons: false,  // POI on the map doesn't open balons on clicks
        gestureHandling: 'greedy', //disable ctrl+ zooming

        maxZoom: 21,            // maxZoom:21 because of OKAPI-mapper max-zoom

        draggableCursor: 'crosshair',
        draggingCursor: 'pointer',

        tilt: 0,                // disable auto-switch to tilted view
        mapTypeId:
            getMapTypeIdFromSettings(params),
        mapTypeControlOptions: {
            mapTypeIds: ocMapTypeIds,
            position: params.mapTypeControl.pos,
            style: params.mapTypeControl.style,
        },
        fullscreenControl: true      // fullscreen control is always seen

    };

    //set map options
    map.setOptions( map_options );

    //init custom controls on the map
    initCustomControlls(map, params);

    //add overlay with icons of caches
    addOCOverlay( map, params );

    //register map change custom callback
    google.maps.event.addListener(map, "maptypeid_changed", function() {
        //called when user switch the map
        onMapChangeCallback(map, params);
    });



    //display circle if requested
    if (params.circle === 1){
        drawCircle( map, params);
    }

    if(params.fromlat !== params.tolat) {
        var area = new google.maps.LatLngBounds();
        area.extend(new google.maps.LatLng(params.fromlat, params.fromlon));
        area.extend(new google.maps.LatLng(params.tolat,   params.tolon));
        map.fitBounds(area);
    }

    //init mouse clicks callbacks
    var mouseClickFuncs = getMouseClickCallbacks(map, params);
    google.maps.event.addListener(map, 'click', mouseClickFuncs[0]); //left click
    google.maps.event.addListener(map, 'rightclick', mouseClickFuncs[1]); //right click

    //init realod function if neccesary
    if(params.hasOwnProperty('reload_func')){
        initReloadFunc(map, params);
    }

    // show/hide Google specific content (we are allowed to display it only on Google maps!)
    controlGoogleContent(map);

    // ...and again when the map is fully loaded
    google.maps.event.addListenerOnce(map, 'idle', function(){
        // do something only the first time the map is loaded
        controlGoogleContent(map);
    });

    //add map attribution (desc.) div
    setAttributionOnMap(map);


}// loadOcMap

/**
 * Functions returns mapId string value
 * based on mapId int value stored in oc user settings (db)
 *
 * Yeah! Google uses string ids, oc has numeric ids in DB...
 *
 * @param params
 */
function getMapTypeIdFromSettings(params){

    var mapTypesIds = params.__ocMapTypesIds;
    var intId = params.map_type;

    if( mapTypesIds.length > intId )
        return mapTypesIds.slice(intId,intId+1)[0];
    else{
        return google.maps.MapTypeId.ROADMAP;
    }
}

 /**
  * Functions returns mapId string value
  * based on mapId int value stored in oc user settings (db)
  *
  * Yeah! Google uses string ids, oc has int ids in DB...
  *
  * @param map    - main google map object
  * @param params - init params from template file
  */
 function getOcMapTypeIdFromMap(map, params){
     var stringMapId = map.getMapTypeId();
     var mapTypesIds = params.__ocMapTypesIds;

     var intId = mapTypesIds.indexOf(stringMapId);
     if( intId >= 0 ){
        return intId;
     }else{
        console.error("Can't find current mapTypeId="+stringMapId+" on the list:");
        console.error(mapTypesIds);
        return 0;
     }
 }

/**
 * Adds OC overlay with caches to display on map
 * based on curent filter settings etc.
 *
 * @param map    - main google map object
 * @param params - init params from template file
 */
function addOCOverlay(map, params)
{

    map.overlayMapTypes.insertAt(0, new google.maps.ImageMapType(
        {
            opacity: 1.0,
            tileSize: new google.maps.Size(256, 256),
            getTileUrl: function(coord, zoom) {
                var scale = 1 << zoom;

                // wrap tiles horizontally
                var x = ((coord.x % scale) + scale) % scale;

                // don't wrap tiles vertically
                var y = coord.y;
                if (y < 0 || y >= scale)
                    return null;

                if ( zoom > 21 ){
                  return null;
                }

                var url = params.cachemap_mapper +
                    "?userid=" + params.userid +
                    "&z=" + zoom + "&x=" + x + "&y=" + y +
                    prepareCommonFilterParams();

                //if __random is not set - skip random elements - this is init url
                if( params.__random != undefined ){
                    // add this random value to make this url unique
                    // this random is generated on 'refresh' button click
                    // rand param is never used
                    url += "&rand=" + params.__random;
                }

                if( params.searchdata != "")
                    url += "&searchdata="+ params.searchdata;

                if( params.powertrail_ids != "")
                    if( $('#pt_selection').is(':checked')) //skip to see all caches
                        url += '&powertrail_ids='+ params.powertrail_ids;

                return url;
            }
        }
        ));
}

/**
 * This function builds part of the url used for get map overlay tailes
 * or cache desc. after click on map
 *
 */
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

/**
 * Init map custom controlls
 *
 * @arg map         - Google map object
 * @arg controls    - 'controls' object from init params
 */
function initCustomControlls( map, params ){

    var controls = params.customControls;

    for ( var c in controls ) {

        switch ( c ){
        case 'ctrlCombo':
            map.controls[controls[c].pos]
                .push( $('#'+controls[c].id)[0] );
            break;

        case 'fullscreenButton':
            google.maps.event.addDomListener($('#'+controls[c].id )[0], "click", function() {
                if( params.fullscreen ){
                    document.cookie = "forceFullScreenMap=off;"; //remember user decision in cookie
                    if($.urlParam('calledFromPt') == 1) {
                        window.location = "powerTrail.php?ptAction=showSerie&ptrail="+$.urlParam('pt');
                    } else {
                        window.location = "cachemap3.php"+getMapParamsToUrl(map, params);
                    }
                }else{
                    document.cookie = "forceFullScreenMap=on;";
                    window.location = "cachemap-full.php"+getMapParamsToUrl(map, params);
                }
            });
            break;

        case 'refreshButton':
            $('#'+controls[c].id ).css("display", "block");

            //add onlick function to refresh button
            $('#'+controls[c].id ).click(function(){

                //generate 'temporary' uniq string and save it for use in mapper url build process
                t = new Date();
                params.__random = "r" + t.getHours() + t.getMinutes() + t.getSeconds();

                //reload the map
                params.__reloadMap();
            });
            break;

        case 'gpsPositionButton':
            var button = $('#'+controls[c].id);
            if (("geolocation" in navigator)){

                //geolocation present in browser
                button.css('display','block'); //show position button
                var geolocationObj = new GeolocationOnMap(map);
                google.maps.event.addDomListener(button[0], "click", function() {
                    geolocationObj.getCurrentPosition();
                });
            } else {
                //geolocation not supported
                button.css('display','none'); //hide position button
            }
            break;

        case 'ocFilters':

            // check if map has a filter button (as fullscreen map)

            if( controls[c].hasOwnProperty('buttonId') ){
                var button = $('#'+controls[c].buttonId )[0];
                google.maps.event.addDomListener( button, "click", function() {
                    if ( filters_div.css('display') == 'none' ) {
                        filters_div.css('display','block');
                    }else{
                        filters_div.css('display','none');
                    }
                });
            }

            var filters_div = $('#'+controls[c].boxId );

            // add dim to checked input in filters box
            var checkbox_changed = function() {
                var $related = $("." + $(this).attr('name'));
                if ($(this).is(':checked'))
                    $related.addClass('dim');
                else
                    $related.removeClass('dim');
            };

            // attach checkbox_changed as callback to all inputs
            // in opt_table to changed event
            $('#'+ controls[c].boxId +' input')
                .each(checkbox_changed)
                .change(checkbox_changed);

            break;
        case 'search':

            var placeSearchText = document.getElementById(controls[c].input_id);
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


            google.maps.event.addDomListener(placeSearchText, "keydown", function(ev) {
                if (ev.keyCode === 13) { //on enter...
                    doSearchHandler();
                }
            });

            if( controls[c].but_id !== null ){ //handle search button
                var placeSearchButton = document.getElementById(controls[c].but_id);
                google.maps.event.addDomListener(placeSearchButton, "click", doSearchHandler);
            }

            break;

        case 'zoom_display':
            //zoom control - displays current zoom
            var zoomId = controls[c].id;
            document.getElementById( zoomId ).value = map.getZoom();
            google.maps.event.addListener(map, "zoom_changed", function() {
                document.getElementById( zoomId ).value = map.getZoom();
            });
            break;

        case 'coordsUnderCursor':

            //display current coords on map
            if( controls[c].hasOwnProperty('pos')){ //if pos not set skip it
                var showCoords = new ShowCoordsControl(map, controls[c].pos);
                google.maps.event.addListener(map, "mousemove", function(event) {
                    showCoords.setCoords(event.latLng);
                });
            }
            break;

        default:
            console.error("Unknown control key: "+c);
        }
    }
}

/**
 * Callback from "maptypeid_changed" event
 * It switch attribution (map descripotion)
 * and save the user settings in DB
 *
 * @param map - currently used Google map object
 * @param params - init params from template
 */
function onMapChangeCallback(map, params){

    //show/hide Google specific content (we are allowed to display it only on Google maps!)
    controlGoogleContent(map);

    //update attribution
    setAttributionOnMap(map);

    //save last used map
    saveMapSettings(map, params);


}

/**
 * This function displays map attribution on the bottom of the map
 * @param map - currently used Google map object
 */
function setAttributionOnMap(map){

    //locate attributionDiv
    var attributionDiv = document.getElementById('map-copyright');
    if( attributionDiv == null) {

        //no such element - create it
        attributionDiv = document.createElement('div');
        attributionDiv.id = "map-copyright";
        attributionDiv.style.fontSize = "10px";
        attributionDiv.style.fontFamily = "Arial, sans-serif";
        attributionDiv.style.padding = "3px 6px";
        attributionDiv.style.margin = "0 100px 0 0";
        attributionDiv.style.whiteSpace = "nowrap";
        attributionDiv.style.opacity = "0.7";
        attributionDiv.style.background = "#fff";

        map.controls[google.maps.ControlPosition.BOTTOM_RIGHT]
            .push(attributionDiv);
    }

    //display attribution
    var newMapTypeId = map.getMapTypeId();
    attributionDiv.innerHTML = attributionMap[newMapTypeId] || '';

}

/**
 * This function adds circle at the center of the map
 * @param map - currently used Google map object
 * @param params - init params from template
 */
function drawCircle( map, params){
    // draw circle with radius 150 m to check existing geocaches
    var central_point = new google.maps.LatLng(params.coords[0], params.coords[1]);
    var circle_obj = createGMapCircleObject(central_point, 150,'#0000FF',2,0.5,'#9999CC',0.2,55);
    circle_obj.setMap(map);
    new google.maps.Marker({position: central_point, map: map});
}

/**
 * Return function handlers for left and right click
 *
 * @param map - currently used Google map object
 * @param params - init params from template
 * @returns {Array} [ leftClickFunc, rightClickFunc ]
 */
function getMouseClickCallbacks(map, params){

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


    var infowindow = null; //popup with cache description

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
            url: prepareLibXmlMapUrl(params, clickBounds, 'html')
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


    var onRightClickFunc = function(event)
    {
        if (pendingClickRequest) {
            pendingClickRequest.abort();
            pendingClickRequest = null;
        }

        var clickBounds = calcClickBounds(event.latLng);
        var clickRect = new google.maps.Rectangle({bounds: clickBounds, strokeColor: '#008', fillColor: '#99c', map: map});
        var url = prepareLibXmlMapUrl(params, clickBounds,'url');

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

    //preselected cache - map is opened with visible balon
    if(params.doopen){
        onClickFunc(
            {
                latLng: new google.maps.LatLng(
                        params.coords[0],
                        params.coords[1]
                        )
            });
    }

    return [onClickFunc, onRightClickFunc];
}

/**
 * This function reads current map attributs to set in url
 *
 * @param map - currently used Google map object
 * @param params - init params from template
 * @returns - part of the url with arguments
 */
function getMapParamsToUrl(map, params){

    var mapUrlParams = "?lat="+map.getCenter().lat()+
                       "&lon="+map.getCenter().lng()+
                       "&inputZoom="+map.getZoom();

    if(params.extrauserid != "")
        mapUrlParams += "&userid="+ params.extrauserid;

    if(params.boundsurl != "")
        mapUrlParams += params.boundsurl;

    if(params.searchdata != "")
        mapUrlParams += "&searchdata="+params.searchdata;

    if(params.powertrail_ids != "")
        mapUrlParams += "&pt="+params.powertrail_ids;

    return mapUrlParams;
}

/**
 * Map template needs JS function which reload the map
 * for example on cache filter change
 * This function create JS function with name requested by template
 *
 * If there is function with requested name inside global scope
 * only console error is generated.
 *
 * @param map - currently used Google map object
 * @param params - init params from template
 */
function initReloadFunc(map, params){

    var fn = params.reload_func;

    if(window.hasOwnProperty(fn)){
        console.error(
            "Can't register function with name: "+fn+" - such name is in use!");
        console.errot(
            "\tChange arg. 'reload_func' value!");
        return;
    }

    params.__reloadMap = function(){
        //this is "reload" function
        map.overlayMapTypes.removeAt(0);   //remove previous overlay
        addOCOverlay(map, params);         //add new overlay
        saveMapSettings(map, params);      //save curren settings
    }

    //register function with fn name in global scope
    window[fn] = params.__reloadMap;
}

/**
 * This function save user settings from the map to DB
 *
 * @param map - currently used Google map object
 * @param params - init params from template
 */
function saveMapSettings(map, params){

    if (params.savesettings === false){
        //no save mode...
        return;
    }

    var queryString =
        "?map_v=3&maptype=" +
        getOcMapTypeIdFromMap(map, params) +
        prepareCommonFilterParams();

    jQuery.get("cachemapsettings.php" + queryString);
}

/**
 * Form url to retrive cache balon
 *
 * @param params - init params from template
 * @param clickBounds - object stored coords rectange to find cache inside
 * @param rspFormat - format of the respose (html balon / url to cache)
 * @returns {String} - url of request
 */
function prepareLibXmlMapUrl(params, clickBounds, rspFormat){
    var p1 = clickBounds.getSouthWest();
    var p2 = clickBounds.getNorthEast();

    var url="lib/xmlmap.php"+
                "?rspFormat="+rspFormat+
                "&latmin="+p1.lat()+"&lonmin="+p1.lng()+"&latmax="+p2.lat()+"&lonmax="+p2.lng()+
                "&screenW="+window.innerWidth+
                "&userid="+params.userid+
                prepareCommonFilterParams();

    if(params.searchdata != "")
        url += "&searchdata="+params.searchdata;

    if(params.powertrail_ids != "")
        if( $('#pt_selection').is(":checked")){ //skip to see all caches
            url += "&powertrail_ids="+params.powertrail_ids;
        }

    return url;
}

/**
 *
 */
function ShowCoordsControl(map, pos) {

    this.setCoords = function(latlng) {
        this.lastLatLng = latlng;
        this.showCoords.childNodes[1].data = toWGS84(this.type, latlng);
    };

    this.setStyle_ = function(elem) {
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

    map.controls[pos].push(container);

    google.maps.event.addDomListener(showCoords, "dblclick", function() {
        this.owner.type = ((this.owner.type + 1) % 3);
        this.owner.setCoords(this.owner.lastLatLng);
    });

    this.setCoords(map.getCenter());

    return this;
}


/**
 * Object used in processing geolocation on the map
 * allow to show current position reads from GPS
 */
function GeolocationOnMap(map){

    this.gMapObj = map;
    /*
     * Possible values of geoloc_status:
     *   0 - initial
     *   1 - get position in progress
     *   2 - position acquired
     *   3 - error - position not available
     */
    this.geoloc_status = 0;

    this.getCurrentPosition = function() {
        if (!("geolocation" in navigator))
            return;

        this.changeGeolocStatus(1); //set state to: In-progress
        navigator.geolocation.getCurrentPosition(
                this.processCurrentPosition(),
                this.handleGetPositionError(),
                { enableHighAccuracy: true }
        );
    };

    this.changeGeolocStatus = function(new_status) {
        this.geoloc_status = new_status;
        document.getElementById("current_position_icon").src = "/images/map_geolocation_" + this.geoloc_status + ".png";
    };

    this.processCurrentPosition = function(){

        var obj = this;
        var curr_pos_marker = {
                circle: null,
                point: null
        };

        return function(position) {
            var pt = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            var accuracy = position.coords.accuracy;

            if (curr_pos_marker.circle === null) {
                curr_pos_marker.circle = createGMapCircleObject(pt, accuracy, '#0044CC', 2, 0.5, '#99AACC', 0.2, 55);
                curr_pos_marker.circle.setMap(obj.gMapObj);
            } else {
                curr_pos_marker.circle.setCenter(pt);
                curr_pos_marker.circle.setRadius(accuracy);
            }

            if (curr_pos_marker.point === null) {
                curr_pos_marker.point = new google.maps.Marker({
                    position: pt,
                    map: obj.gMapObj,
                    icon: {
                        url: "/images/markers/map_current_location.png",
                        size: new google.maps.Size(32, 32),
                        anchor: new google.maps.Point(16, 16)
                    }
                });
            } else {
                curr_pos_marker.point.setPosition(pt);
            }

            obj.gMapObj.setCenter(pt);
            obj.gMapObj.setZoom(obj.calculateZoomLevel(accuracy));

            obj.changeGeolocStatus(2); // Success
        };
    };

    this.handleGetPositionError = function() {
        var obj = this;
        return function(positionError){

            if (positionError.code === 1) { // Permission denied
                obj.changeGeolocStatus(0); // User has denied geolocation - return to initial state
            } else {
                obj.changeGeolocStatus(3); // Indicate actual problem with getting position
            }
        }
    };

    this.calculateZoomLevel = function(accuracy) {

        if (accuracy > 10000) return 10; //zoom = 10
        if (accuracy >  5000) return 11;
        if (accuracy >  2400) return 12;
        if (accuracy >  1200) return 13;
        if (accuracy >   600) return 14;
        if (accuracy >   300) return 15;
        return 16; //otherwise
    };

    return this;
}


/**
 *
 */



/* --- UTIL FUNCTIONS --- */
function isTouchScreen(){
    // Check if this is a touch device (i.e. phone/tablet) --> if it is, simplify layout
    // Detection based on the following Stack Overflow question:
    // http://stackoverflow.com/questions/4817029/whats-the-best-way-to-detect-a-touch-screen-device-using-javascript
    // **** This check should be kept in sync with analogous check in tpl/stdstyle/lib/menu.php ****
    return (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0));
}

/**
 * This function returns Google map circle object
 * with radius 150m to show contain existing geocaches
 *
 * TODO: arguments list can be longer! - needs documentation
 */
function createGMapCircleObject( srodek, promien )
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

/**
 * TODO
 * @param type
 * @param latlng
 * @returns {String}
 */
function toWGS84(type, latlng){
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

$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}
