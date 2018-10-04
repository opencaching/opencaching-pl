
/**
 * This function is a DynamicMap-chunk entrypoint
 *
 * @param params - JS object with properites of dynamicMap-chunk
 */
function dynamicMapEntryPoint( params ) {

  var prefix = params.prefix;
  var mapDiv = $('#'+params.targetDiv);

  mapDiv.addClass("dynamicMap_cursor");

  params.map = new ol.Map({
    target: params.targetDiv,
    view: new ol.View({
      center: ol.proj.fromLonLat(
          [params.centerOn.lon, params.centerOn.lat]
          ),
      zoom: params.mapStartZoom,
    }),

    controls: ol.control.defaults({
      attributionOptions:
      { // attribution can't be display in custom control :( -
        // there is no simple way to get attribution from layer
        className: 'ol-attribution dynamicMap_attribution',
        collapsible: false,
      },
      zoom: false,
      rotate: false,
    }).extend([
      // note: scaleLine has two css OL classes: .ol-scale-line .ol-scale-line-inner
      new ol.control.ScaleLine({ minWidth: 100 })]
    ),
  });

  // init layer switcher
  layerSwitcherInit(params);

  // init zoom controls
  mapZoomControlsInit(params);

  // init map compass (north-up reset button)
  compassControlInit(params);

  // init localization control
  gpsLocatorInit(params);

  // init mouse position coords
  cordsUnderCursorInit(params);

  // initialize markers on map
  loadMarkers(params);
}

function layerSwitcherInit( params ) {
  var switcherDiv = $("<div class='ol-control dynamicMap_layerSwitcher'></div>");

  // prepare dropdown object
  var switcherDropdown = $('<select></select>');

  switcherDiv.append(switcherDropdown);

  // add layers from config to map
  $.each( params.mapLayersConfig, function(key, layer) {

    OcLayerServices.setOcLayerName(layer, key);
    layer.set('wrapX', true)
    layer.set('zIndex', 1)

    if (key == params.selectedLayerKey) {
      switcherDropdown.append('<option value='+key+' selected>'+key+'</option>');
      layer.setVisible(true);
    } else {
      switcherDropdown.append('<option value='+key+'>'+key+'</option>');
      layer.setVisible(false);
    }
    params.map.addLayer(layer);
  })

  // add switcher to map
  params.map.addControl(new ol.control.Control(
      {
        element: switcherDiv[0],
      }
  ));

  // init switcher change callback
  switcherDropdown.change(function(evt) {

    let selectedLayerName = switcherDropdown.val();

    params.map.getLayers().forEach(function(layer) {
        // first skip OC-internal layers (prefix oc_)
        if ( ! OcLayerServices.isOcInternalLayer(layer) ) {

          // this is external layer (like OSM)
          if ( OcLayerServices.getOcLayerName(layer) == selectedLayerName ) {
            layer.setVisible(true);
          } else {
            layer.setVisible(false);
          }
        } else { // this is OC-generated layer
          if ( layer.getVisible() != true) {
            layer.setVisible(true);
          }
        }
    })

    // run callback if present
    if ( typeof params.layerSwitchCallbacks !== 'undefined' ) {
      $.each(params.layerSwitchCallbacks, function(key, callback){
        callback(selectedLayerName);
      });
    }

  });
}

function gpsLocatorInit(params) {

  if (!("geolocation" in navigator)) {
    console.log('Geolocation not supported by browser.')
    return;
  }

  gpsDiv = $("<div class='ol-control dynamicMap_gpsLocator'></div>");
  gpsImg = $('<img id="dynamicMap_gpsPositionImg" src="/images/icons/gps.svg" alt="gps">');

  gpsDiv.append(gpsImg);

  params.map.addControl(new ol.control.Control(
      {
        element: gpsDiv[0],
      }
  ))

  var geolocationObj = new GeolocationOnMap(params.map, '.dynamicMap_gpsLocator');
  gpsImg.click(function() {
    geolocationObj.getCurrentPosition();
  });

}

function mapZoomControlsInit(params) {

  let zoomDiv = $('<div class="ol-control dynamicMap_mapZoom"></div>');
  let zoomIn = $('<img src="/images/icons/plus.svg" alt="+">');
  let zoomOut = $('<img src="/images/icons/minus.svg" alt="-">');

  zoomDiv.append(zoomIn);
  zoomDiv.append(zoomOut);

  params.map.addControl(new ol.control.Control(
      {
        element: zoomDiv[0],
      }
  ))

  zoomIn.click(function() {
    var view = params.map.getView()
    var zoom = view.getZoom()
    view.setZoom(zoom + 1)
  })

  zoomOut.click(function() {
    var view = params.map.getView()
    var zoom = view.getZoom()
    view.setZoom(zoom - 1)
  })

}

function compassControlInit(params) {

  let compassDiv = $('<div class="ol-control dynamicMap_compassDiv"></div>');
  let compass = $('<img src="/images/icons/arrow.svg" alt="+">');
  //let compass = $('<span class="dynamicMap_compass">⇧</span>');
  compassDiv.append(compass);

  params.map.addControl(new ol.control.Control(
      {
        element: compassDiv[0],
      }
  ));

  compassDiv.click(function() {
    params.map.getView().setRotation(0);
  });

  params.map.on('moveend', function (evt){
    let roatation = evt.map.getView().getRotation()
    compass.css('transform', 'rotate('+roatation+'rad)');
  });

}

/* this is util used fo coords formatting */
var CoordinatesUtil = {

    FORMAT: Object.freeze({
      DECIMAL: 1,     /* decimal degrees: N 40.446321° W 79.982321° */
      DEG_MIN: 2,     /* degrees decimal minutes: N 40° 26.767′ W 79° 58.933′ */
      DEG_MIN_SEC: 3, /* degrees minutes seconds: N 40° 26′ 46″ W 79° 58′ 56″ */
    }),

    cmp: function(coordsA, coordsB) {
      return (
          Array.isArray(coordsA) &&
          Array.isArray(coordsB) &&
          coordsA[0] == coordsB[0] &&
          coordsA[1] == coordsB[1]);
    },

    toWGS84: function (map, coords, outFormat) {

      if (outFormat == undefined) {
        // set default output format
        outFormat = this.FORMAT.DEG_MIN;
      }

      // convert coords from map coords to WGS84
      mapCoordsCode = map.getView().getProjection().getCode();
      wgs84Coords = ol.proj.transform(coords,mapCoordsCode,'EPSG:4326');
      lon = wgs84Coords[0];
      lat = wgs84Coords[1];

      lonHemisfere = (lon < 0)?"W":"E";
      latHemisfere = (lat < 0)?"S":"N";

      lonParts = this.getParts(lon);
      latParts = this.getParts(lat);

      switch(outFormat) {
      case this.FORMAT.DEG_MIN:
        return latHemisfere + " " + Math.floor(latParts.deg) + "° " +
                  latParts.min.toFixed(3) + "' " +
               lonHemisfere + " " + Math.floor(lonParts.deg) + "° " +
                  lonParts.min.toFixed(3) + "'";

      case this.FORMAT.DECIMAL:
        return latHemisfere + " " + lonParts.deg.toFixed(5) + "° " +
               lonHemisfere + " " + lonParts.deg.toFixed(5) + "°";

      case this.FORMAT.DEG_MIN_SEC:
        return latHemisfere + " " + Math.floor(latParts.deg) + "° " +
                  Math.floor(latParts.min) + "' " +
                  latParts.sec.toFixed(2) + '" ' +
               lonHemisfere + " " + Math.floor(lonParts.deg) + "° " +
                  Math.floor(lonParts.min) + "' " +
                  lonParts.sec.toFixed(2) + '"';
      }
    },

    getParts: function(coordinate) {
      var deg = Math.abs(coordinate);
      var min = 60 * (deg - Math.floor(deg));
      var sec = 60 * (min - Math.floor(min));
      return {deg: deg, min: min, sec: sec};
    },
  };

function cordsUnderCursorInit(params) {

  if( jQuery(window).width() < 800 ){
    console.log('CordsUnderCursor control skipped because of window width.')
    return;
  }

  params.curPos = {};
  params.curPos.positionDiv = $('<div class="ol-control dynamicMap_mousePosition"></div>');

  params.map.addControl(new ol.control.Control(
      {
        element: params.curPos.positionDiv[0],
      }
  ));

  params.curPos.lastKnownCoords = null;
  params.curPos.coordsFormat = CoordinatesUtil.FORMAT.DEG_MIN;

  params.map.on('pointermove', function(event) {

    if (!CoordinatesUtil.cmp(params.curPos.lastKnownCoords, event.coordinate)) {
      params.curPos.lastKnownCoords = event.coordinate

      params.curPos.positionDiv.html(
          CoordinatesUtil.toWGS84(params.map, params.curPos.lastKnownCoords, params.curPos.coordsFormat));
    }
  });

  // switch coords format on dbl-click
  params.curPos.positionDiv.dblclick( function() {
    switch(params.curPos.coordsFormat) {
    case CoordinatesUtil.FORMAT.DEG_MIN:
      params.curPos.coordsFormat = CoordinatesUtil.FORMAT.DEG_MIN_SEC;
      break;
    case CoordinatesUtil.FORMAT.DEG_MIN_SEC:
      params.curPos.coordsFormat = CoordinatesUtil.FORMAT.DECIMAL;
      break;
    case CoordinatesUtil.FORMAT.DECIMAL:
      params.curPos.coordsFormat = CoordinatesUtil.FORMAT.DEG_MIN;
      break;
    default:
      params.curPos.coordsFormat = CoordinatesUtil.FORMAT.DEG_MIN;
    }
  });

}

function loadMarkers(params) {

    if (!params.markerData || params.markerData.length == 0) {
        return;
    }

  // extract markers from params
  var featuresArr = [];
  Object.keys(params.markerData).forEach(function(markerType) {
    params.markerData[markerType].forEach(function(markerData, id){
      featuresArr.push(params.markerMgr[markerType].markerFactory(markerType, id, markerData));
    });
  });

  var markersLayer = new ol.layer.Vector ({
    zIndex: 100,
    visible: true,
    source: new ol.source.Vector({ features: featuresArr }),
    });

  OcLayerServices.setOcLayerName(markersLayer, 'oc_markers');


  var ext = markersLayer.getSource().getExtent();

  //load markers layer
  params.map.addLayer( markersLayer );

  //zoom map to see all markers
  if(!params.forceMapZoom && !ol.extent.isEmpty(ext)){
    // there are markers
    params.map.getView().fit(ext);
  }


  // popup init.
  var popup = new ol.Overlay({
    element: $('<div class="dynamicMap_mapPopup"></div>')[0],
    positioning: 'bottom-center',
    stopEvent: true,
    insertFirst: false,
    offset: [0, -50],
    autoPan: true,
    autoPanAnimation: {
      duration: 250
    },
  });

  params.map.addOverlay(popup);

  params.map.on('click', function(evt) {
    var feature = params.map.forEachFeatureAtPixel(evt.pixel, function(feature) {
        return feature;
      });

    if (feature) {
      if( (ocData = feature.get('ocData')) == undefined){
        popup.setPosition(undefined);
        return true;
      }

        im = feature.getStyle().getImage();
        if (im && im instanceof ol.style.Icon) {
            anc = im.getAnchor();
            popup.setOffset([0, -(anc[1] * im.getScale()) - 2]);
        }

      var markerType = ocData.markerType;
      var markerId = ocData.markerId;

      if(!params.compiledPopupTpls[markerType]){
        var popupTpl = $('script[type="text/x-handlebars-template"].'+markerType).html();
        params.compiledPopupTpls[markerType] = Handlebars.compile(popupTpl);
      }

      var markerContext = params.markerData[markerType][markerId];
      $(popup.getElement()).html(params.compiledPopupTpls[markerType](markerContext));

      $(".dynamicMap_mapPopup-closer").click(function(evt) {
          popup.setPosition(undefined);
      });

      popup.setPosition(feature.getGeometry().getCoordinates());
    } else {
      popup.setPosition(undefined);
    }
  });

}

/**
 * Object used in processing geolocation on the map
 * It allows to show current position read from GPS
 */
function GeolocationOnMap(map, iconSelector) {

    this.map = map
    this.positionMarkersCollection = new ol.Collection();
    this.positionMarkersLayer = null;

    this.STATUS = Object.freeze({
      INIT:              '', /* initial state */
      IN_PROGRESS:       'rgb(255,255,177,.5)', /* position reading in progress */
      POSITION_ACQUIRED: 'rgb(170,255,127,.5)', /* positions has been read */
      ERROR:             'rgb(0,255,255,.5)', /* some error occured */
    })


    this.getCurrentPosition = function() {
        console.log('get position...')

        if (!("geolocation" in navigator)) {
          console.error('Geolocation not supported by browser!');
          this.changeGeolocIconStatus(obj.STATUS.ERROR);
          return;
        }

        this.changeGeolocIconStatus(this.STATUS.IN_PROGRESS);

        navigator.geolocation.getCurrentPosition(
                this.getSuccessCallback(),
                this.getErrorCallback(),
                { enableHighAccuracy: true }
        );
    }

    // set new status and its icon
    this.changeGeolocIconStatus = function(newStatus) {
        $(iconSelector).css('background-color', newStatus);
    }

    this.getSuccessCallback = function() {

        var obj = this;

        return function(position) {
            console.log('position read: ', position);

            obj.changeGeolocIconStatus(obj.STATUS.POSITION_ACQUIRED);

            lat = position.coords.latitude;
            lon = position.coords.longitude;

            currCoords = ol.proj.fromLonLat([lon, lat]);
            accuracy = position.coords.accuracy;

            view = obj.map.getView();
            view.setCenter(currCoords);
            view.setZoom(obj.calculateZoomForAccuracy(accuracy));

            // draw position marker
            var accuracyFeature = new ol.Feature({
              geometry: new ol.geom.Circle(currCoords, accuracy),
            });

            accuracyFeature.setStyle([
                new ol.style.Style({ //circle
                    stroke: new ol.style.Stroke({
                      color: DynamicMapServices.styler.fgColor,
                      width: 2}),
                }),
                new ol.style.Style({ //center marker
                  geometry: function(feature){
                    return new ol.geom.Point(feature.getGeometry().getCenter());
                  },
                  image: new ol.style.RegularShape({
                    stroke: new ol.style.Stroke({
                      color: DynamicMapServices.styler.fgColor,
                      width: 2
                    }),
                    points: 4,
                    radius: 10,
                    radius2: 0,
                    angle: Math.PI / 4
                  })
                }),
                ]
            )

            obj.positionMarkersCollection.clear();
            obj.positionMarkersCollection.push(accuracyFeature);

            if (obj.positionMarkersLayer == null) {
              obj.positionMarkersLayer = new ol.layer.Vector({
                map: obj.map,
                source: new ol.source.Vector({
                  features: obj.positionMarkersCollection,
                }),
              });
            }
        }
    }

    this.getErrorCallback = function() {
        var obj = this;

        return function(positionError) {
            console.error('OC Map: positions reading error!', positionError);

            if (positionError.code === 1) { // Permission denied
                // User has denied geolocation - return to initial state
                obj.changeGeolocIconStatus(obj.STATUS.INIT);
            } else {
                // Indicate actual problem with getting position
                obj.changeGeolocIconStatus(obj.STATUS.ERROR);
            }
        }
    }

    this.calculateZoomForAccuracy = function(accuracy) {
        // accuracy is in meters

        if (accuracy <   300) return 16;
        if (accuracy <   600) return 15;
        if (accuracy <  1200) return 14;
        if (accuracy <  2400) return 13;
        if (accuracy <  5000) return 12;
        if (accuracy < 10000) return 11;
        return 10; // otherwise
    }

    return this;
}


/**
 * This is global interface to DynamicMapChunk
 */
var DynamicMapServices = {

  /**
   * returns OpenLayers map object used as a base of this chunk instance
   */
  getMapObject: function (mapId){
    return window['dynamicMapParams_'+mapId].map;
  },

  /**
   * returns the name of currently selected map (layer) name
   */
  getSelectedLayerName: function (mapId){

    map = this.getMapObject(mapId);

    let visibleLayers = [];
    map.getLayers().forEach(function(layer){

      if ( !OcLayerServices.isOcInternalLayer(layer)
           && layer.getVisible()) {

        visibleLayers.push(OcLayerServices.getOcLayerName(layer));
      }
    });

    if (visibleLayers.lenght <= 0){
      console.err('--- no visible layer ?! ---');
      return '';
    }

    if (visibleLayers.lenght > 1){
      console.err('--- many visible layers ?! ---');
    }

    return visibleLayers.pop();
  },

  /**
   * add callback which wil be call on map layer change
   * callback should be function with one input parameter: "selectedLayerName"
   */
  addMapLayerSwitchCallback: function (mapId, callback){
    if(typeof mapId === undefined){
      console.error('mapId is required!');
    }

    if(typeof callback !== 'function'){
      console.error('callback must be a function!');
    }

    params = window['dynamicMapParams_'+mapId];

    if( ! params.layerSwitchCallbacks ) {
      params.layerSwitchCallbacks = [];
    }

    params.layerSwitchCallbacks.push(callback);
  },

  styler: { // styles in OL format
    fgColor: [100, 100, 255, 1], // main foreground color in OL format
    bgColor: [238, 238, 238, 0.4], // main background color in OL fomrat
  }

};

var OcLayerServices = {

    isOcInternalLayer: function (layer){
      return ( /^oc_.*/.test( layer.get('ocLayerName') ));
    },

    setOcLayerName: function (layer, name){
      layer.set('ocLayerName', name);
    },

    getOcLayerName: function(layer) {
      return layer.get('ocLayerName');
    }

};

