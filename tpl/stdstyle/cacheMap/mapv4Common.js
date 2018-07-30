
function mapEntryPoint(map, targetDiv) {

  // create main map file
  map = new ol.Map({
    target: targetDiv,
    view: new ol.View({
      center: ol.proj.fromLonLat([ocMapInputParams.centerOn.lon, ocMapInputParams.centerOn.lat]),
      zoom: ocMapInputParams.mapStartZoom,
    }),
    controls: ol.control.defaults({
      attributionOptions:
      {
        collapsible: false,
        target: $("#ocAttribution")[0],
      },
      zoom: false
    }),
  });

  map.addControl(new ol.control.Control(
      {
        element: $("#controlCombo")[0],
      }
  ));

  map.addControl(new ol.control.Control(
      {
        element: $("#ocAttribution")[0],
      }
  ));

  // init filter box - should be called before layers init
  filterBoxInit(map);

  // init map layers
  layerSwitcherInit(map);

  // init scaleLine
  scaleLineInit(map);

  // init zoom controls
  mapZoomControlsInit(map);

  // init map-click handlers
  mapClickInit(map);

  // init mouse position coords
  cordsUnderCursorInit(map);

  // init button of GPS position
  gpsPositionInit(map);

  // actions for resize map
  window.addEventListener('resize', function() {
    mapWindowResize(map);
  });
  mapWindowResize(map);

} // mapEntryPoint

function layerSwitcherInit(map) {

  // add oc-Tiles layer
  map.addLayer(
    new ol.layer.Tile({
      source: getOcTailSource(),
      visible: true,
      zIndex: 100,
      wrapX: true,
      ocLayerName: 'ocTiles',
    })
  )

  // add maps to switcher
  var switcherDropdown = $("#layerSwitcher select")
  $.each( ocMapConfig.getExtMapConfigs(), function(key, val) {

    val.set('ocLayerName', key)
    val.set('wrapX', true)
    val.set('zIndex', 1)

    if (key == ocMapConfig.getUserSettings().map) {
      switcherDropdown.append('<option value='+key+' selected>'+key+'</option>');
      val.setVisible(true);
    } else {
      switcherDropdown.append('<option value='+key+'>'+key+'</option>');
      val.setVisible(false);
    }

    map.addLayer(val);
  })


  // add switcher to map
  map.addControl(new ol.control.Control(
      {
        element: $("#layerSwitcher")[0],
      }
  ))

  // init switcher event
  switcherDropdown.change(function(a) {

    map.getLayers().forEach(function(layer) {
        if ( layer.get('ocLayerName') != 'ocTiles') {

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

    saveUserSettings()
  })
}

function scaleLineInit(map) {
  element = $("#mapScale")

  map.addControl(new ol.control.Control(
      {
        element: element[0],
      }
  ))
  map.addControl(new ol.control.ScaleLine(
      {
        className: 'customScale',
        target: element[0],
        minWidth: 100,
      }
  ))
}

/**
 * init zoom controls
 */
function mapZoomControlsInit(map) {

  map.addControl(new ol.control.Control(
      {
        element: $("#mapZoom")[0],
      }
  ))

  $("#mapZoomIn").click(function() {
    var view = map.getView()
    var zoom = view.getZoom()
    view.setZoom(zoom + 1)
  })

  $("#mapZoomOut").click(function() {
    var view = map.getView()
    var zoom = view.getZoom()
    view.setZoom(zoom - 1)
  })

}

function getOcTailSource(addRandomParam) {

  var ocTilesUrl = '/lib/mapper_okapi.php'

  ocTilesUrl += '?userid='+ocMapConfig.getUserId()
  ocTilesUrl += '&z={z}&x={x}&y={y}'
  ocTilesUrl += dumpFiltersToStr()

  if ( addRandomParam != undefined ) {
    t = new Date();
    ocTilesUrl += "&rand=" + "r" + t.getTime();
  }

  // add searchdata param if necessary
  if ( searchData = ocMapConfig.getSearchData() ) {
    ocTilesUrl += "&searchdata="+searchdata;
  }

  // add powertrail ids list if necessary
  if ( ptIds = ocMapConfig.getPowerTrailIds() ) {
    if ( $('#pt_selection').is(":checked") ) { //skip to see all caches
      ocTilesUrl += "&powertrail_ids="+ptIds
    }
  }

  return new ol.source.TileImage({
    url: ocTilesUrl,
    opacity: 1,
    wrapDateLine: false,
    wrapX: true,
    noWrap: false
  })

}

function mapClickInit(map) {

  var pendingClickRequest = null; //last click ajax request
  var pendingClickRequestTimeout = 10000; // default timeout - in milliseconds

  /**
   * Cancel previous ajax requests
   */
  var _abortPreviousRequests = function () {
    if (pendingClickRequest) {
      pendingClickRequest.abort();
      pendingClickRequest = null;
    }
  }

  /**
   * Returns extent 32px x 32px with center in coordinates
   */
  var _getClickBounds = function (coords) {
    unitsPerPixel = map.getView().getResolution();
    circleClicked = new ol.geom.Circle(coords, 16*unitsPerPixel)
    return circleClicked.getExtent()
  }

  var _displayClickMarker = function (coords) {

    clickMarker = map.getOverlayById('mapClickMarker')
    if (clickMarker==null) { //clickMarker is undefined
      // prepare map click marker overlay.
      map.addOverlay( new ol.Overlay(
          {
            id: 'mapClickMarker',
            element: $("#mapClickMarker")[0],
            position: coords,
          }
      ))
    } else {
      clickMarker.setPosition(coords)
    }
  }

  var _hideClickMarker = function () {
    clickMarker = map.getOverlayById('mapClickMarker')
    if ( clickMarker ) { // clickMarker is present
      clickMarker.setPosition(undefined)
    }
  }

  var _hidePopup = function() {
    popup = map.getOverlayById('mapPopup')
    if ( popup ) { // clickMarker is present
      popup.setPosition(undefined)
    }
  }

  var _getPopupDataUrl = function(coords) {

    var extent = _getClickBounds(coords)

    swCorner = ol.proj.transform(ol.extent.getBottomLeft(extent),'EPSG:3857','EPSG:4326')
    neCorner = ol.proj.transform(ol.extent.getTopRight(extent),'EPSG:3857','EPSG:4326')

    var url="/CacheMapBalloon/json"+
                "?rspFormat="+"html"+
                "&latmin="+swCorner[1]+"&lonmin="+swCorner[0]+
                "&latmax="+neCorner[1]+"&lonmax="+neCorner[0]+
                "&screenW="+window.innerWidth+
                "&userid="+ocMapConfig.getUserId()+
                dumpFiltersToStr()

    // add searchdata param if necessary
    if (searchData = ocMapConfig.getSearchData()) {
      url += "&searchdata="+searchdata
    }

    // add powertrail ids list if necessary
    if ( ptIds = ocMapConfig.getPowerTrailIds() ) {
      if ( $('#pt_selection').is(":checked")) { //skip to see all caches
          url += "&powertrail_ids="+ptIds;
      }
    }

    return url;
  }


  var onLeftClickFunc = function(coords) {
    _abortPreviousRequests();

    _displayClickMarker(coords)

    pendingClickRequest = jQuery.ajax({
      url: _getPopupDataUrl(coords)
    })

    pendingClickRequest.always( function() {
      _hideClickMarker()
      pendingClickRequest = null;
    })

    pendingClickRequest.done( function( data ) {

      console.log(data);

      if (data === null ) { // nothing to display
          _hidePopup();
          return; //nothing more to do here
      }

      //cacheCords = $($.parseHTML(data)).filter('input[name="cache_cords"]').val();
      //cacheCords = jQuery.parseJSON(cacheCords);
      //cacheCords = ol.proj.fromLonLat([cacheCords.longitude,cacheCords.latitude])

      //cacheCords = ol.proj.transform([cacheCords.longitude,cacheCords.latitude],'EPSG:4326','EPSG:3857')
      cacheCords = ol.proj.transform([data.coords.lon,data.coords.lat],'EPSG:4326','EPSG:3857')


      popup = map.getOverlayById('mapPopup')
      if (popup == null) {

        // there is no popup object - create it
        map.addOverlay( popup = new ol.Overlay(
            {
              id: 'mapPopup',
              element: $("#mapPopup")[0],
              position: cacheCords,
              autoPan: true,
              autoPanAnimation: {
                duration: 250
              },
            }
        ));

        // assign click on popup close button handler
        $("#mapPopup-closer").click(function() {
          popup.setPosition(undefined);
          return false;
        })

      } else {
        popup.setPosition(cacheCords)
      }

      var balloonTpl = $("#cacheInforBallonTpl").html();
      var balloonTplCompiled = Handlebars.compile(balloonTpl);

      $("#mapPopup-content").html(balloonTplCompiled(data));

    });

  }

  var onRightClickFunc = function(coords) {
    _abortPreviousRequests();


    _displayClickMarker(coords)

    //todo
  }


  /**********************************/
  /** init handlers                          */
  /**********************************/


  // assign left-click handler
  map.on("singleclick", function(evt) {
    onLeftClickFunc(evt.coordinate)
  })

  // asign right-click handler
  map.getViewport().addEventListener('contextmenu', function (evt) {
    evt.preventDefault()
    onRightClickFunc(map.getEventCoordinate(evt))
  })

}

function cordsUnderCursorInit(map) {

  element = $("#mousePosition")
  map.addControl(new ol.control.Control(
      {
        element: element[0],
      }
  ))

  var lastCoords = null
  var currentFormat = CoordinatesUtil.FORMAT.DEG_MIN

  map.on('pointermove', function(event) {
    if (!CoordinatesUtil.cmp(lastCoords, event.coordinate)) {
      lastCoords = event.coordinate

      $("#mousePosition").html(
          CoordinatesUtil.toWGS84(map, lastCoords,currentFormat))
    }
  })

  element.dblclick(function() {
    switch(currentFormat) {
    case CoordinatesUtil.FORMAT.DEG_MIN:
      currentFormat = CoordinatesUtil.FORMAT.DEG_MIN_SEC
      break
    case CoordinatesUtil.FORMAT.DEG_MIN_SEC:
      currentFormat = CoordinatesUtil.FORMAT.DECIMAL
      break
    case CoordinatesUtil.FORMAT.DECIMAL:
      currentFormat = CoordinatesUtil.FORMAT.DEG_MIN
      break
    default:
      currentFormat = CoordinatesUtil.FORMAT.DEG_MIN
    }
  })

}


/**
 * init filters box
 */
function filterBoxInit(map) {

  var filtersControlAdded = false;

  /**
   * Filters changed - ocLayer should be refreshed
   */
  var refreshOcTiles = function (refreshTiles) {

    ocLayer = map.getLayers().item(0) // item0=layer with ocTiles
    ocLayer.setSource(getOcTailSource(refreshTiles))

    // save user map settings to server
    saveUserSettings()
  }

  // init

  // set values saved at server side
  $.each(ocMapConfig.getUserSettings().filters, function(key, val) {
    var el = $("#"+key);
    if (el.is("input")) {
      el.prop('checked', val);
    } else if (el.is("select")) {
      el.val(val)
    }
  })

  $('#filtersToggle').click(function() {

    if (!filtersControlAdded) {
      // init filters at the begining
      filtersControlAdded = true;

      // add filters as map control
      map.addControl(new ol.control.Control(
          {
            element: $("#mapFilters")[0],
          }
      ))

      // be sure filters are hidden now
      $('#mapFilters').toggle(false)


    }

    // hide/display filters box
    $('#mapFilters').toggle()
  });

  // add filters click handlers
  $('#mapFilters input').click(function() {
    refreshOcTiles()
  })

  $('#mapFilters select').change(function() {
    refreshOcTiles()
  })

  $('#refreshButton').click(function() {
    refreshOcTiles(true);
  });
  
  if (document.getElementById('fullscreenToggle')) {
	$('#fullscreenToggle').click(function() {
		fullScreenToggle(map);
	})
  }

}

function fullScreenToggle(map) {
	zoomCurrent = map.getView().getZoom();
	coordsCurrent = map.getView().getCenter();
	mapCoordsCode = map.getView().getProjection().getCode();
	wgs84Coords = ol.proj.transform(coordsCurrent, mapCoordsCode, 'EPSG:4326');
	uri = ocMapInputParams.fullScreenToggleUri;
	uri += '?lon=' + wgs84Coords[0];
	uri += '&lat=' + wgs84Coords[1];
	uri += '&inputZoom=' + zoomCurrent;
	window.location.href = uri;
}

function saveUserSettings() {
  $.ajax({
    type: "POST",
    dataType: 'json',
    url: "/CacheMap/saveMapSettingsAjax",
    data: { userMapSettings: JSON.stringify(getUserMapSettingsJson()) },
    success: function() {
      console.log("data saved")
    },
    error: function() {
      console.error("Cant save user settings!")
    },
  })
}

function getUserMapSettingsJson() {

  return {
    filters: dumpFiltersToJson(),
    map: $("#layerSwitcher select").val()
  }

}

function dumpFiltersToJson() {

  var json = {};
  $('#mapFilters input').each(function() {
    json[$(this).prop('id')] = $(this).prop("checked")
  })

  $('#mapFilters select').each(function() {
    json[$(this).prop('id')] = $(this).val()
  })

  return json;
}

function dumpFiltersToStr() {

  var str = ""

  $.each(dumpFiltersToJson(), function(key,val) {
    str += '&'+key+'='+val
  })

  str += "&max_score=3"   // used to be permanently set in a hidden input field - document.getElementById('max_score').value+
  str += "&h_avail=false" // used to be permanently set in a hidden input field - document.getElementById('h_avail').checked+

  return str
}

function gpsPositionInit(map) {
  // check if geolocation is supported by the browser
  if (("geolocation" in navigator)) {

    var geolocationObj = new GeolocationOnMap(map, 'gpsPosition');
    $('#gpsPosition').click(function() {
      geolocationObj.getCurrentPosition();
    })

  } else { //geolocation not supported
    //hide GPS position button
    $('#currentPosition').hide()
  }

}

var ocMapConfig = {

  getUserId: function () {
    //TODO: parse value
    return ocMapInputParams.userId;
  },

  getSearchData: function () {
    //TODO: parse value
    return ocMapInputParams.searchData;
  },

  getPowerTrailIds: function () {
    //TODO: parse value
    return ocMapInputParams.searchData;
  },

  getUserSettings: function () {
    return ocMapInputParams.userSettings;
  },

  getExtMapConfigs: function () {
    return ocMapInputParams.extMapConfigs;
  }

};

/**
 * Object used in processing geolocation on the map
 * It allows to show current position read from GPS
 */
function GeolocationOnMap(map, iconId) {

    this.iconId = iconId
    this.map = map
    this.positionMarkersCollection = new ol.Collection()
    this.positionMarkersLayer = null

    this.STATUS = Object.freeze({
      INIT:              0, /* initial state */
      IN_PROGRESS:       1, /* position reading in progress */
      POSITION_AQQUIRED: 2, /* positions has been read */
      ERROR:             3, /* some error occured */
    })


    this.getCurrentPosition = function() {
        console.log('get position...')

        if (!("geolocation" in navigator)) {
          console.error('Geolocation not supported by browser!')
          return;
        }

        this.changeGeolocIconStatus(this.STATUS.IN_PROGRESS);

        navigator.geolocation.getCurrentPosition(
                this.getSuccessCallback(),
                this.getErrorCallback(),
                { enableHighAccuracy: true }
        )
    }

    // set new status and its icon
    this.changeGeolocIconStatus = function(newStatus) {

        // change icon for given status
        $('#'+this.iconId).attr('src','/images/map_geolocation_' +
            newStatus + '.png')
    }

    this.getSuccessCallback = function() {

        var obj = this;

        return function(position) {
            console.log('position readed')
            console.log(position)

            lat = position.coords.latitude
            lon = position.coords.longitude

            currCoords = ol.proj.fromLonLat([lon, lat])
            accuracy = position.coords.accuracy


            view = obj.map.getView()
            view.setCenter(currCoords)
            view.setZoom(obj.calculateZoomForAccuracy(accuracy))

            // draw position marker

            var accuracyFeature = new ol.Feature({
              geometry: new ol.geom.Circle(currCoords, accuracy)
            })

            accuracyFeature.setStyle(
                new ol.style.Style({
                    stroke: new ol.style.Stroke({color: 'blue', width: 2}),
                })
            )

            var positionFeature = new ol.Feature({
              geometry: new ol.geom.Point(currCoords)
            })

            positionFeature.setStyle(
                new ol.style.Style({
                  image: new ol.style.RegularShape({
                    /*fill: new ol.style.Fill({color: 'red'}),*/
                    stroke: new ol.style.Stroke({color: 'blue', width: 2}),
                    points: 4,
                    radius: 10,
                    radius2: 0,
                    angle: Math.PI / 4
                  })
                })
            )

            obj.positionMarkersCollection.clear()
            obj.positionMarkersCollection.push(accuracyFeature)
            obj.positionMarkersCollection.push(positionFeature)

            if (obj.positionMarkersLayer == null) {
              obj.positionMarkersLayer = new ol.layer.Vector({
                map: obj.map,
                source: new ol.source.Vector({
                  features: obj.positionMarkersCollection
                })
              });
            }

        }
    }

    this.getErrorCallback = function() {
        var obj = this;

        return function(positionError) {
            console.error('OC Map: positions reading error!')
            console.error(positionError)

            if (positionError.code === 1) { // Permission denied
                // User has denied geolocation - return to initial state
                obj.changeGeolocStatus(obj.STATUS.INIT);
            } else {
                // Indicate actual problem with getting position
                obj.changeGeolocStatus(obj.STATUS.ERROR);
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

function mapWindowResize(map) {

  var mapIsSmall = map.getSize()[0] < 600;

  if (mapIsSmall) {
    $('#mousePosition').hide();
    $("#mapScale").css( {left:'0'} );
  } else {
    $('#mousePosition').show();
    $("#mapScale").css( {left:'40%'} );
  }
}
