/**
 * This function is a DynamicMap-chunk entrypoint
 *
 * @param params - JS object with properites of dynamicMap-chunk
 */
function dynamicMapEntryPoint( params ) {

  var prefix = params.prefix;
  var mapDiv = $('#'+params.targetDiv);

  // add attribution div
  var attributionDiv = $('<div class="dynamicMap_attribution"></div>');

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
      {
        collapsible: false,
        target: attributionDiv[0],
      },
      zoom: false
    }),

  });

  mapDiv.addClass("dynamicMap_cursor");

  // add attributions control
  params.map.addControl(new ol.control.Control(
      {
        element: attributionDiv[0],
      }
  ));

  // init layer switcher
  layerSwitcherInit(params);

  // init scaleLine
  scaleLineInit(params);

  // init zoom controls
  mapZoomControlsInit(params);

  // init mouse position coords
  cordsUnderCursorInit(params);

  // initialize markers on map
  loadMarkers(params);
}

function layerSwitcherInit(params) {

  // prepare dropdown object
  var switcherDropdown = $('<select></select>');

  // add layers from config to map
  $.each( params.mapLayersConfig, function(key, layer) {

    layer.set('ocLayerName', key)
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
        element: switcherDropdown[0],
      }
  ));

  // wrap dropdow in div
  switcherDropdown.wrap('<div class="ol-control dynamicMap_layerSwitcher"></div>')

  // init switcher change callback
  switcherDropdown.change(function(a) {

    params.map.getLayers().forEach(function(layer) {
        // first skip OC-internal layers (prefix oc_)
        if ( ! /^oc_.*/.test( layer.get('ocLayerName') )) {

          // make sure selected layer is not internal OC layer is visible
          if ( layer.get('ocLayerName') == switcherDropdown.val() ) {
            layer.setVisible(true);
            //$("#ocAttribution").html(layer.getSource().attributions_[0].html_)
            //console.log(layer.getSource().get('attributions'));
          } else {
            layer.setVisible(false);
          }

        } else {
          if ( layer.getVisible() != true) {
            layer.setVisible(true);
          }
        }
    })

    // run callback if it is present
    if ( typeof dynamicMap_layerSwitchCallback !== 'undefined'
         && $.isFunction(dynamicMap_layerSwitchCallback) ) {

      dynamicMap_layerSwitchCallback(params);
    }

  });

}

function scaleLineInit(params) {
  element = $("<div class='ol-control dynamicMap_mapScale'></div>");

  params.map.addControl(new ol.control.Control(
      {
        element: element[0],
      }
  ))
  params.map.addControl(new ol.control.ScaleLine(
      {
        className: 'customScale',
        target: element[0],
        minWidth: 100,
      }
  ))
}

function mapZoomControlsInit(params) {

  zoomDiv = $('<div class="ol-control dynamicMap_mapZoom"></div>');
  zoomIn = $('<img class="dynamicMap_mapZoomIn" src="/images/icons/plus.svg" alt="+">');
  zoomOut = $('<img class="dynamicMap_mapZoomOut" src="/images/icons/minus.svg" alt="-">');

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
    ocLayerName: 'oc_markers',
    });

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

      popup.setPosition(feature.getGeometry().getCoordinates());

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
    } else {
      popup.setPosition(undefined);
    }
  });

  // change mouse cursor when over marker
  params.map.on('pointermove', function(e) {
    var o = e.map.getTargetElement();
    var isOverFeature = e.map.forEachFeatureAtPixel(e.pixel, function(feature) {
        o.style.cursor = "pointer";
        return true;
    });
    if (!isOverFeature) {
        o.style.cursor = "";
    }
    if (e.dragging) {
      popup.setPosition(undefined);
      return;
    }
  });

}
