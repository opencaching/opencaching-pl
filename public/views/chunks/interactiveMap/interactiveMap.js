/**
 * This is a global object and an entry point to InteractiveMap collection
 */
var InteractiveMapServices = {
    /**
     * returns InteractiveMap object with given mapId
     * or if params is an object, creates a new one with given id and params
     */
    getInteractiveMap: function(mapId, params) {
        var result = null;
        if (
            typeof mapId !== "undefined"
        ) {
            if (typeof window["_interactiveMaps"] === "undefined") {
                window["_interactiveMaps"] = [];
            }
            if (
                typeof window["_interactiveMaps"][mapId]
                    === "undefined"
            ) {
                if (typeof params === "object") {
                    window["_interactiveMaps"][mapId] =
                        new InteractiveMap(mapId, params);
                    result = window["_interactiveMaps"][mapId];
                }
            } else {
                result = window["_interactiveMaps"][mapId];
            }
        }
        return result;
    },

    /**
     * returns OpenLayers map object used as a base of this chunk instance
     */
    getMapObject: function(mapId) {
        return (
            typeof mapId !== "undefined"
            && typeof window["_interactiveMaps"] !== "undefined"
            && typeof window["_interactiveMaps"][mapId] !== "undefined"
            ? window["_interactiveMaps"][mapId].map
            : null
        );
    },

    /**
     * returns the name of currently selected map (layer) name
     */
    getSelectedLayerName: function(mapId) {
        var map = this.getMapObject(mapId);

        var visibleLayers = [];
        if (map) {
            map.getLayers().forEach(function(layer) {
                if (
                    !OcLayerServices.isOcInternalLayer(layer)
                    && layer.getVisible()
                ) {
                    visibleLayers.push(OcLayerServices.getOcLayerName(layer));
                }
            });
        }

        if (visibleLayers.length <= 0) {
            console.err('--- no visible layer ?! ---');
            return '';
        }
        if (visibleLayers.length > 1) {
            console.err('--- many visible layers ?! ---');
        }

        return visibleLayers.pop();
    },

    /**
     * add callback which will be called on map layer change
     * callback should be a function with one input parameter: "selectedLayerName"
     */
    addMapLayerSwitchCallback: function(mapId, callback) {
        var correct = true;
        if (typeof mapId === "undefined") {
            console.error('mapId is required!');
            correct = false;
        }
        if (typeof callback !== "function") {
            console.error('callback must be a function!');
            correct = false;
        }

        if (correct) {
            var map = this.getMapObject(mapId);
            if (map) {
                if (typeof map.layerSwitchCallbacks !== "array") {
                    map.layerSwitchCallbacks = []
                }
                map.layerSwitchCallbacks.push(callback);
            }
        }
    },

    /**
     * Reorders sections of map with given id, see the InteractiveMap
     * corresponding method.
     */
    reorderSections: function (mapId, orders) {
        var correct = true;
        if (typeof mapId === "undefined") {
            console.error('mapId is required!');
            correct = false;
        }
        if (typeof orders !== "object") {
            console.error('orders must be an object!');
            correct = false;
        }
        if (correct) {
            var map = this.getInteractiveMap(mapId);
            if (map) {
                map.reorderSections(orders);
            }
        }
    },

    /**
     * Toggles visibility of section of map with given id, see the
     * InteractiveMap corresponding method.
     */
    toggleSectionVisibility: function(mapId, section) {
        var correct = true;
        if (typeof mapId === "undefined") {
            console.error('mapId is required!');
            correct = false;
        }
        if (typeof section === "undefined") {
            console.error('section is required!');
            correct = false;
        }
        if (correct) {
            var map = this.getInteractiveMap(mapId);
            if (map) {
                map.toggleSectionVisibility(section);
            }
        }
    },

    /**
     * Highlights a feature match given parameters: markerType, id
     * (cache or log) and section of map with given id, see the InteractiveMap
     * corresponding method.
     */
    highlightFeature: function(mapId, markerType, localId, section) {
        var correct = true;
        if (typeof mapId === "undefined") {
            console.error('mapId is required!');
            correct = false;
        }
        if (typeof markerType === "undefined") {
            console.error('markerType is required!');
            correct = false;
        }
        if (typeof localId === "undefined") {
            console.error('localId is required!');
            correct = false;
        }
        if (correct) {
            var interactiveMap = this.getInteractiveMap(mapId);
            if (interactiveMap) {
                interactiveMap.highlightFeature(markerType, localId, section);
            }
        }
    },

    /**
     * Tones down (de-emphasizes) previously highlighted feature of map with
     * given id, see the InteractiveMap corresponding method.
     */
    toneDownFeature: function(mapId) {
        var correct = true;
        if (typeof mapId === "undefined") {
            console.error('mapId is required!');
            correct = false;
        }
        if (correct) {
            var interactiveMap = this.getInteractiveMap(mapId);
            if (interactiveMap) {
                interactiveMap.toneDownFeature();
            }
        }
    },

    styler: { // styles in OL format
        fgColor: [100, 100, 255, 1], // main foreground color in OL format
        bgColor: [238, 238, 238, 0.4], // main background color in OL format
    }

};

// =================================================================

/**
 * An InteractiveMap prototype
 */
function InteractiveMap(id, params) {
    this.id = id;
    this.targetDiv = undefined;
    this.centerOn = undefined;
    this.mapStartZoom = undefined;
    this.forceMapZoom = undefined;
    this.startExtent = undefined;
    this.mapLayersConfig = getMapLayersConfig();
    this.selectedLayerKey = undefined;
    this.infoMessage = undefined;
    this.markersData = undefined;
    this.sectionsProperties = undefined;
    this.sectionsNames = undefined;
    this.markerMgrs = undefined;
    this.markersFamily = undefined;

    // defaults for controls and markers
    this.enableScaleLine = true;
    this.enableLayerSwitcher = true;
    this.enableZoomControls = true;
    this.enableCompass = true;
    this.enableGPSLocator = true;
    this.enableCooordsUnderCursor = true;
    this.enableInfoMessage = true;
    this.enableMarkers = true;
    this.enablePopup = true;
    this.enableHighlight = true;

    this.compiledPopupTpls = [];
    this.layerSwitchCallbacks = [];
    // rewrite params entries to corresponding attributes
    if (typeof params === "object") {
        for (var p in params) {
            if (this.hasOwnProperty(p)) {
                this[p] = params[p];
            }
        }
    }
}

/**
 * Initializes OL map, controls, markers etc.
 */
InteractiveMap.prototype.init = function() {
    var mapDiv = $('#' + this.targetDiv);
    var attributionDiv = $('<div class="interactiveMap_attribution"></div>');
    mapDiv.addClass("interactiveMap_cursor");

    this.map = new ol.Map({
        target: this.targetDiv,
        view: new ol.View({
            center: ol.proj.fromLonLat(
                [this.centerOn.lon, this.centerOn.lat]
            ),
            zoom: this.mapStartZoom,
        }),
        controls: ol.control.defaults({
            attributionOptions: {
                collapsible: false,
                target: attributionDiv[0],
            },
            zoom: false,
            rotate: false,
        }),
    });

    // add attribution control
    this.map.addControl(new ol.control.Control({
        element: attributionDiv[0],
    }));

    if (this.enableScaleLine) {
        // note: scaleLine has two css OL classes:
        // .ol-scale-line .ol-scale-line-inner
        this.map.addControl(new ol.control.ScaleLine({ minWidth: 100 }));
    }

    if (this.startExtent) {
        // fit map to given extent
        var sw = ol.proj.fromLonLat([
            this.startExtent.sw.lon, this.startExtent.sw.lat
        ]);
        var ne = ol.proj.fromLonLat([
            this.startExtent.ne.lon, this.startExtent.ne.lat]
        );
        this.map.getView().fit([sw[0], sw[1], ne[0], ne[1]], { nearest:true });
    }

    if (this.enableLayerSwitcher) {
        // init layer switcher
        this._layerSwitcherInit();
    }
    if (this.enableZoomControls) {
        // init zoom controls
        this._mapZoomControlsInit();
    }
    if (this.enableCompass) {
        // init map compass (north-up reset button)
        this._compassControlInit();
    }
    if (this.enableGPSLocator) {
        // init localization control
        this._gpsLocatorInit();
    }
    if (this.enableCoordsUnderCursor) {
        // init mouse position coords
        this._coordsUnderCursorInit();
    }
    if (this.enableInfoMessage) {
        // init infoMessage control
        this._infoMessageInit();
    }

    if (this.enableMarkers) {
        // load markers data and create features
        this._loadMarkers();
    }
}

/**
 * Initializes layer switcher control with preconfigured map layers
 */
InteractiveMap.prototype._layerSwitcherInit = function() {
    var switcherDiv = $(
        "<div class='ol-control interactiveMap_layerSwitcher'></div>"
    );

    // prepare dropdown object
    var switcherDropdown = $('<select></select>');

    switcherDiv.append(switcherDropdown);

    var instance = this;

    // add layers from config to map
    $.each(this.mapLayersConfig, function(key, layer) {
        OcLayerServices.setOcLayerName(layer, key);
        layer.set('wrapX', true);
        layer.set('zIndex', 1);

        if (key == instance.selectedLayerKey) {
            switcherDropdown.append(
                '<option value=' + key + ' selected>' + key + '</option>'
            );
            layer.setVisible(true);
        } else {
            switcherDropdown.append(
                '<option value=' + key + '>' + key + '</option>'
            );
            layer.setVisible(false);
        }
        instance.map.addLayer(layer);
    });

    // add switcher to map
    this.map.addControl(
        new ol.control.Control({ element: switcherDiv[0] })
    );

    // init switcher change callback
    switcherDropdown.change(function(evt) {
        var selectedLayerName = switcherDropdown.val();
        instance.map.getLayers().forEach(function(layer) {
            // first skip OC-internal layers (prefix oc_)
            if (!OcLayerServices.isOcInternalLayer(layer)) {
                // this is external layer (like OSM)
                layer.setVisible(
                    OcLayerServices.getOcLayerName(layer) == selectedLayerName
                );
            } else {
                // this is OC-generated layer
                if (layer.getVisible() != true) {
                    layer.setVisible(true);
                }
            }
        });

        // run callback if present
        if (typeof instance.layerSwitchCallbacks === 'undefined') {
            $.each(instance.layerSwitchCallbacks, function(key, callback) {
                callback(selectedLayerName);
            });
        }
    });
}

/**
 * Initializes GPS locator control
 */
InteractiveMap.prototype._gpsLocatorInit = function() {
    if (!("geolocation" in navigator)) {
        console.log('Geolocation not supported by browser.')
        return;
    }
    var gpsDiv = $("<div class='ol-control interactiveMap_gpsLocator'></div>");
    var gpsImg = $(
        '<img id="interactiveMap_gpsPositionImg" '
        + 'src="/images/icons/gps.svg" alt="gps">'
    );

    gpsDiv.append(gpsImg);

    this.map.addControl(
        new ol.control.Control({ element: gpsDiv[0] })
    );

    var geolocationObj = new GeolocationOnMap(
        this.map, '.interactiveMap_gpsLocator'
    );
    gpsImg.click(function() {
        geolocationObj.getCurrentPosition();
    });
}

/**
 * Initializes Map zoom control
 */
InteractiveMap.prototype._mapZoomControlsInit = function() {
    var zoomDiv = $('<div class="ol-control interactiveMap_mapZoom"></div>');
    var zoomIn = $('<img src="/images/icons/plus.svg" alt="+">');
    var zoomOut = $('<img src="/images/icons/minus.svg" alt="-">');

    zoomDiv.append(zoomIn);
    zoomDiv.append(zoomOut);

    this.map.addControl(
        new ol.control.Control({ element: zoomDiv[0] })
    );

    var instance = this;

    zoomIn.click(function() {
        var view = instance.map.getView();
        var zoom = view.getZoom();
        view.setZoom(zoom + 1)
    });

    zoomOut.click(function() {
        var view = instance.map.getView()
        var zoom = view.getZoom();
        view.setZoom(zoom - 1)
    });
}

/**
 * Initializes Compass control
 */
InteractiveMap.prototype._compassControlInit = function() {
    var compassDiv = $('<div class="ol-control interactiveMap_compassDiv"></div>');
    var compass = $('<img src="/images/icons/arrow.svg" alt="+">');
    compassDiv.append(compass);

    this.map.addControl(
        new ol.control.Control({ element: compassDiv[0] })
    );

    var instance = this;
    compassDiv.click(function() {
        instance.map.getView().setRotation(0);
    });

    this.map.on('moveend', function (evt) {
        var rotation = evt.map.getView().getRotation();
        compass.css('transform', 'rotate(' + rotation + 'rad)');
    });
}

/**
 * Initializes a control showing coordinates under cursor
 */
InteractiveMap.prototype._coordsUnderCursorInit = function() {
    if ($(window).width() < 800) {
        console.log(
            'CordsUnderCursor control skipped because of window width.'
        );
        return;
    }

    this.curPos = {};
    this.curPos.positionDiv = $(
        '<div class="ol-control interactiveMap_mousePosition"></div>'
    );

    this.map.addControl(
        new ol.control.Control({ element: this.curPos.positionDiv[0] })
    );

    this.curPos.lastKnownCoords = null;
    this.curPos.coordsFormat = CoordinatesUtil.FORMAT.DEG_MIN;

    var instance = this;
    this.map.on('pointermove', function(event) {
        if (
            !CoordinatesUtil.cmp(
                instance.curPos.lastKnownCoords, event.coordinate
            )
        ) {
            instance.curPos.lastKnownCoords = event.coordinate;
            instance.curPos.positionDiv.html(
                CoordinatesUtil.toWGS84(
                    instance.map,
                    instance.curPos.lastKnownCoords,
                    instance.curPos.coordsFormat
                )
            );
        }
    });

    // switch coords format on dbl-click
    this.curPos.positionDiv.dblclick(function() {
        switch(instance.curPos.coordsFormat) {
            case CoordinatesUtil.FORMAT.DEG_MIN:
                instance.curPos.coordsFormat =
                    CoordinatesUtil.FORMAT.DEG_MIN_SEC;
                break;
            case CoordinatesUtil.FORMAT.DEG_MIN_SEC:
                instance.curPos.coordsFormat = CoordinatesUtil.FORMAT.DECIMAL;
                break;
            case CoordinatesUtil.FORMAT.DECIMAL:
            default:
                instance.curPos.coordsFormat = CoordinatesUtil.FORMAT.DEG_MIN;
                break;
        }
    });
}

/**
 * Initializes a control showing info message
 */
InteractiveMap.prototype._infoMessageInit = function() {
    var infoMsgId = this.id + '_infoMsg';

    var msgDiv = $(
        '<div id="' + infoMsgId
        + '" class="ol-control interactiveMap_infoMsg"></div>'
    );
    var closeBtn = $('<div class="interactiveMap_infoMsgClose">✖</div>');
    msgDiv.append(closeBtn);

    this.map.addControl(
        new ol.control.Control( { element: msgDiv[0] } )
    );

    if (this.infoMessage ) {
        msgDiv.prepend(this.infoMessage);
        msgDiv.show();
    } else {
        msgDiv.hide(0);
    }

    closeBtn.click(function() {
        $('#' + infoMsgId).hide();
    });
}

/**
 * Loads markers from markersData, creates corresponding features and organizes
 * them in section-related sources and layers.
 * Then creates an InteractiveMapPopup instance and assignes it with the OL map.
 */
InteractiveMap.prototype._loadMarkers = function() {
    this.markersBaseZIndex = 100;

    if (!this.markersData || this.markersData.length == 0) {
        return;
    }

    var frontViewFeatures = {};
    var sources = {};
    var currentZIndex = this.markersBaseZIndex;
    var allExtent;
    var renderOrderingReverse = true;
    var instance = this;
    Object.keys(this.markersData).forEach(function(section) {
        var featuresArr = [];
        var props = (
            typeof instance.sectionsProperties !== "undefined"
            && typeof instance.sectionsProperties[section] !== "undefined"
            ? instance.sectionsProperties[section]:
            undefined
        );
        // set zIndex according to section order if defined
        var zIndex = currentZIndex;
        if (props && typeof props["order"] !== "undefined") {
            zIndex = instance.markersBaseZIndex - parseInt(props["order"]);
        } else {
            currentZIndex++;
        }
        // true (default) if section is visible
        var visible =
            props && typeof props["visible"] !== "undefined"
            ? props["visible"]
            : true;
        Object.keys(instance.markersData[section]).forEach(function(markerType) {
            instance.markersData[section][markerType].forEach(
                function(markerData, id) {
                    var feature = instance.markerMgrs[markerType].markerFactory(
                        instance.map, markerType, id, markerData, section
                    );
                    if (visible) {
                        var geom = feature.getGeometry();
                        if (typeof geom["getCoordinates"] === "function") {
                            var coords = geom.getCoordinates();
                            var key = "" + coords[0] + "," + coords[1];
                            var isFirst = (
                                typeof frontViewFeatures[key] === "undefined"
                            );
                            if (
                                !isFirst
                                && (
                                    renderOrderingReverse
                                    ? frontViewFeatures[key].zIndex < zIndex
                                    : frontViewFeatures[key].zIndex <= zIndex
                                )
                            ) {
                                frontViewFeatures[key].feature.set(
                                    "isFirst", false
                                );
                                isFirst = true;
                            }
                            if (isFirst) {
                                feature.set("isFirst", true);
                                frontViewFeatures[key] = {
                                    feature: feature,
                                    zIndex: zIndex
                                }
                            }
                        }
                    }
                    featuresArr.push(feature);
                }
            );
        });
        sources[section] = {
            src: new ol.source.Vector({ features: featuresArr }),
            zIndex: zIndex,
            visible: visible
        }
        if (!allExtent) {
            allExtent = sources[section].src.getExtent();
        } else {
            allExtent = ol.extent.extend(
                allExtent, sources[section].src.getExtent()
            );
        }
    });

    Object.keys(sources).forEach(function(section) {
        var source = sources[section];
        var markersLayer = new ol.layer.Vector ({
            zIndex: source.zIndex,
            visible: source.visible,
            source: source.src,
            ocLayerName: 'oc_markers_' + section,
            renderOrder: function(f1, f2) {
                return (
                    renderOrderingReverse
                    ? (
                        (f1["ol_uid"] > f2["ol_uid"])
                        ? -1
                        : +(f1["ol_uid"] < f2["ol_uid"])
                    )
                    : (
                        (f1["ol_uid"] < f2["ol_uid"])
                        ? -1
                        : +(f1["ol_uid"] > f2["ol_uid"])
                    )
                );
            }
        });
        instance.map.addLayer(markersLayer);
    });

    // zoom map to see all markers
    if (!this.forceMapZoom && !ol.extent.isEmpty(allExtent)) {
        // there are markers
        this.map.getView().fit(allExtent);
    }

    if (this.enableHighlight) {
        // source, layer and marker for highlightning,
        // placed at the bottom of active oc layers
        var highlightedSource = new ol.source.Vector();

        var highlightedPointMarker_ocData = {
            id: this.id,
            lat: 0,
            lon: 0
        };
        this._highlightedPointMarker = createOCMarkerFeature(
            "_highlighted",
            "_highlighted" + this.id,
            highlightedPointMarker_ocData,
            new HighlightedMarker(this.map, highlightedPointMarker_ocData)
        );
        highlightedSource.addFeature(this._highlightedPointMarker);

        this._highlightedLayer = new ol.layer.Vector ({
            zIndex: Math.min(this.markersBaseZIndex, currentZIndex) - 10,
            visible: false,
            source: highlightedSource,
            ocLayerName: 'oc__highlighted',
        });
        this.map.addLayer(this._highlightedLayer);
    }

    if (this.enablePopup) {
        // most top, foreground source and layer, where popped up features will
        // be temporarily placed
        this.foregroundSource = new ol.source.Vector();

        var foregroundLayer = new ol.layer.Vector ({
            zIndex: 500,
            visible: true,
            source: this.foregroundSource,
            ocLayerName: 'oc__foreground',
        });

        this.map.addLayer(foregroundLayer);

        this._popup = new InteractiveMapPopup()
        this._popup.addToMap(this);
    }
}

/**
 * Returns a feature identified by given parameters: markerType, feature local
 * id (cache or log id f.ex.) and section; null is returned if not found.
 */
InteractiveMap.prototype._getFeature = function(markerType, localId, section) {
    var result = null;
    var featureId = createFeatureId(
        markerType, localId, section ? section : "_DEFAULT_"
    );

    this.map.getLayerGroup().getLayersArray().some(function(layer) {
        if (OcLayerServices.isOcInternalLayer(layer)) {
            result = layer.getSource().getFeatureById(featureId);
        }
        return (result != null);
    });

    return result;
}

/**
 * Reorders sections basing on given orders array, by setting z-index
 */
InteractiveMap.prototype.reorderSections = function(orders) {
    var instance = this;
    this.map.getLayerGroup().getLayersArray().forEach(function(layer) {
        var match = /^oc_markers_(.+)/.exec(layer.get('ocLayerName'));
        if (match != null) {
            var section= match[1];
            if (orders[section] !== undefined) {
                layer.setZIndex(instance.markersBaseZIndex - orders[section]);
            }
        }
    });
    this.map.renderSync();
}

/**
 * Toggles visibility of given section
 */
InteractiveMap.prototype.toggleSectionVisibility = function(section) {
    var instance = this;
    this.map.getLayerGroup().getLayersArray().some(function(layer) {
        var match = /^oc_markers_(.+)/.exec(layer.get('ocLayerName'));
        if (match != null && match[1] == section) {
            layer.setVisible(!layer.getVisible());
            if (typeof instance._popup !== "undefined") {
                instance.map.once("postrender", function(evt) {
                    instance._popup.adjustFeaturesByLayer(layer);
                });
            }
            return true;
        }
    });
}

/**
 * Highlights a feature identified by given parameters, if found
 */
InteractiveMap.prototype.highlightFeature = function(
    markerType, localId, section
) {
    if (
        this.enableHighlight
        && typeof this._highlightedPointMarker !== "undefined"
        && typeof this._highlightedLayer !== "undefined"
    ) {
        var feature = this._getFeature(markerType, localId, section);
        if (feature) {
            var coords = feature.getGeometry().getCoordinates();
            if (coords) {
                this._highlightedPointMarker.getGeometry().setCoordinates(
                    coords
                );
                this._highlightedLayer.setVisible(true);
            }
        }
    }
}

/**
 * Tones down previously highlighted feature
 */
InteractiveMap.prototype.toneDownFeature = function() {
    if (
        this.enableHighlight
        && typeof this._highlightedLayer !== "undefined"
    ) {
        this._highlightedLayer.setVisible(false);
    }
}

// =================================================================

/**
 * An InteractiveMapPopup prototype
 */
function InteractiveMapPopup() {
    this.popup = new ol.Overlay({
        element: $('<div class="interactiveMap_popup"></div>')[0],
        positioning: 'bottom-center',
        stopEvent: true,
        insertFirst: false,
        offset: [0, -50],
        autoPan: true,
        autoPanAnimation: {
            duration: 250
        },
        offsetYAdjusted: false,
    });
    this.features = [];
    this.featureIndex = -1;
    this.offsetYAdjusted = false;
}

/**
 * Assigns this popup instance to given InteractiveMap instance, setting 'click'
 * event handler.
 */
InteractiveMapPopup.prototype.addToMap = function(interactiveMap) {
    this.interactiveMap = interactiveMap
    this.interactiveMap.map.addOverlay(this.popup);
    var instance = this;
    this.interactiveMap.map.on('click', function(evt) {
        instance._mapClickEvent(evt);
    });
}

/**
 * Should be called after any oc layer visibility change to adjust current
 * features available in popup to the changed visibility. A layer where
 * visibility has been changed is passed as a parameter.
 */
InteractiveMapPopup.prototype.adjustFeaturesByLayer = function(layer) {
    var s = layer.getSource();
    if (s && this.features.length > 0) {
        var instance = this;
        var oldFeatureSource = this.currentFeatureSource;
        instance._clearFeature();
        instance.interactiveMap.map.once("postrender", function(evt) {
            var features = instance._getMapFeatures(
                instance.interactiveMap.map.getPixelFromCoordinate(
                    instance.popup.getPosition()
                )
            ).filter(function(feature) {
                return (
                    feature.getId()
                    && (
                        layer.getVisible()
                        || !s.getFeatureById(feature.getId())
                    )
                );
            });
            if (!layer.getVisible() && s === oldFeatureSource) {
                // currently displayed pop feature is hidden in layer,
                // so reset index
                instance.featureIndex = -1;
            } else {
                // currently displayed pop feature is still visible,
                // so determine its index in new array of features
                var currentFeature = instance.features[instance.featureIndex];
                features.some(function(feature, index) {
                    if (feature == currentFeature) {
                        // set new index before desired feature,
                        // to be correctly used in _switchPopupContent
                        instance.featureIndex = index -1;
                        return true;
                    }
                });
            }
            instance.features = features;
            if (instance.features.length > 0) {
                instance._switchPopupContent(true);
            } else {
                instance.popup.setPosition(undefined);
            }
        });
    };
}

/**
 * Returns map features being under given pixel, with exclusion of foreground
 * (popup) layer.
 */
InteractiveMapPopup.prototype._getMapFeatures = function(pixel) {
    var result = [];

    var fC;
    var instance = this;
    this.interactiveMap.map.forEachFeatureAtPixel(pixel, function(feature) {
        if (
            feature.getId()
            && (feature.get('ocData')) != undefined
            && !instance.interactiveMap.foregroundSource.getFeatureById(
                feature.getId()
            )
        ) {
            var canAdd = true;
            if (fC == undefined) {
                // save the first feature coordinates
                fC = feature.getGeometry().getCoordinates();
            } else {
                // add another features only if coordinates match the first one
                var nFC = feature.getGeometry().getCoordinates();
                canAdd = (
                    (nFC[0] == fC[0]) && (nFC[1] == fC[1])
                    || feature.getGeometry().intersectsCoordinate(fC)
                );
            }
            if (canAdd) {
                result.push(feature);
            }
        }
    });

    return result;
}

/**
 * Clears from popup currently selected feature, movin it from the foreground
 * source to its original one.
 */
InteractiveMapPopup.prototype._clearFeature = function(feature) {
    if (feature === undefined && this.featureIndex >= 0) {
        feature = this.features[this.featureIndex];
    }
    if (feature !== undefined && this.currentFeatureSource !== undefined) {
        this.interactiveMap.foregroundSource.removeFeature(feature);
        feature.set("isFirst", false);
        this.currentFeatureSource.addFeature(feature);
        this.currentFeatureSource = undefined;
    }
}

/**
 * Should be called on popup hide or just before possible show, to clear all
 * variables and states related to previously displayed popup.
 */
InteractiveMapPopup.prototype._onHide = function() {
    this._clearFeature();
    this.featureIndex = -1;
    this.features = [];
    this.offsetYAdjusted = false;
}

/**
 * An event handler to be assigned to OL map 'click' event.
 * Checks all features being under clicked pixel and selects them if coordinates
 * match the first one selected.
 * If an array of selected features is not empty, the popup is shown.
 */
InteractiveMapPopup.prototype._mapClickEvent = function(evt) {
    this._onHide();

    var features = this._getMapFeatures(evt.pixel);
    if (features.length > 0) {
        this.features = features;
        this._switchPopupContent(true);
    } else {
        this.popup.setPosition(undefined);
    }
}

/**
 * Computes popup offset Y to place it in a right place above current
 * popup features. The computing result is based if applicable on 'popupOffsetY'
 * attribute of features' markers, which are consecutively computed if missing.
 */
InteractiveMapPopup.prototype._computePopupOffsetY = function() {
    var result;

    this.features.forEach(function(feature) {
        var ocData = feature.get("ocData");

        var ocMarker = feature.get('ocMarker');
        if (
            ocMarker != undefined
            && typeof(ocMarker['currentStyle']) != "undefined"
        ) {
            if (typeof(ocMarker.currentStyle['popupOffsetY']) === "undefined") {
                ocMarker.computePopupOffsetY();
            }
            if (typeof(ocMarker.currentStyle['popupOffsetY']) !== "undefined") {
                if (
                    result == undefined
                    || result > ocMarker.currentStyle.popupOffsetY
                ) {
                    result = ocMarker.currentStyle.popupOffsetY;
                }
            }
        } else {
            var im;
            var s = feature.getStyle();
            if (typeof(s["getImage"]) == "function") {
                im = s.getImage();
            }
            if (im && im instanceof ol.style.Icon) {
                var anc = im.getAnchor();
                var offset = -(anc[1] * im.getScale()) - 2;
                if (result == undefined || result > offset) {
                    result = offset;
                }
            }
        }
    });

    return result;
}

/**
 * Selects the next or previous feature from features being under a popup point
 * when the map was clicked. The selection depends on a 'forward' parameter.
 * If there is no feature previously selected, the first one is chosen. Next,
 * the content and position of popup is set according to the selected feature
 * values.
 */
InteractiveMapPopup.prototype._switchPopupContent = function(forward) {
    var oldFeature;
    var i = this.featureIndex;
    if (i >= 0) {
        oldFeature = this.features[i];
        // (+/-1) modulo features.length, negative value workaround
        i = (
                ((forward ? (i + 1) : (i - 1)) % this.features.length)
                + this.features.length
            ) % this.features.length;
    } else if (i < 0) {
        i = 0;
    }

    var feature = this.features[i];
    var ocData = feature.get("ocData");

    if (!this.offsetYAdjusted) {
        var popupOffsetY = this._computePopupOffsetY();
        if (popupOffsetY) {
            this.popup.setOffset([0, popupOffsetY]);
        }
        this.offsetYAdjusted = true;
    }

    // move currect feature to the foreground layer, replacing the previous
    // one
    var featureId = feature.getId();
    if (featureId) {
        var instance = this;
        this.interactiveMap.map.getLayerGroup().getLayersArray().some(
            function(layer) {
                if (/^oc_[^_].*/.test( layer.get('ocLayerName') )) {
                    var s = layer.getSource();
                    if (s.getFeatureById(featureId) === feature) {
                        if (oldFeature != undefined) {
                            instance._clearFeature(oldFeature);
                        }
                        s.removeFeature(feature);
                        feature.set("isFirst", true);
                        instance.interactiveMap.foregroundSource.addFeature(
                            feature
                        );
                        instance.currentFeatureSource = s;
                        return true;
                    }
                }
            }
        );
    }

    var markerSection = ocData.markerSection;
    var markerType = ocData.markerType;
    var markerId = ocData.markerId;

    if (!this.interactiveMap.compiledPopupTpls[markerType]) {
        var popupTpl =
            $('script[type="text/x-handlebars-template"].' + markerType).html();
        this.interactiveMap.compiledPopupTpls[markerType] =
            Handlebars.compile(popupTpl);
    }

    var markerContext =
        this.interactiveMap.markersData[markerSection][markerType][markerId];
    if (
        typeof this.interactiveMap.sectionsNames !== "undefined"
        && typeof this.interactiveMap.sectionsNames[markerSection]
            !== "undefined"
    ) {
        markerContext['sectionName'] =
            this.interactiveMap.sectionsNames[markerSection];
    }
    if (this.features.length > 1) {
        markerContext['showNavi'] = true;
    } else {
        markerContext['showNavi'] = undefined;
    }
    $(this.popup.getElement()).html(
        this.interactiveMap.compiledPopupTpls[markerType](markerContext)
    );

    var instance = this;
    $(".imp-closer").click(function(evt) {
        instance._onHide();
        instance.popup.setPosition(undefined);
    });

    if (this.features.length > 1) {
        $(".imp-navi .imp-backward > img").click(function(evt) {
            instance._switchPopupContent(false);
        });
        $(".imp-navi .imp-forward > img").click(function(evt) {
            instance._switchPopupContent(true);
        });
    }

    this.featureIndex = i;
    this.popup.setPosition(feature.getGeometry().getCoordinates());
}

// =================================================================
// Utils and helper objects
// =================================================================

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
            coordsA[1] == coordsB[1]
        );
    },

    toWGS84: function (map, coords, outFormat) {
        if (outFormat == undefined) {
            // set default output format
            outFormat = this.FORMAT.DEG_MIN;
        }

        // convert coords from map coords to WGS84
        mapCoordsCode = map.getView().getProjection().getCode();
        wgs84Coords = ol.proj.transform(coords, mapCoordsCode, 'EPSG:4326');
        lon = wgs84Coords[0];
        lat = wgs84Coords[1];

        lonHemisfere = (lon < 0)?"W":"E";
        latHemisfere = (lat < 0)?"S":"N";

        lonParts = this.getParts(lon);
        latParts = this.getParts(lat);

        switch(outFormat) {
            case this.FORMAT.DEG_MIN:
                return (
                    latHemisfere + " " + Math.floor(latParts.deg) + "° "
                    + latParts.min.toFixed(3) + "' "
                    + lonHemisfere + " " + Math.floor(lonParts.deg) + "° "
                    + lonParts.min.toFixed(3) + "'"
                );

            case this.FORMAT.DECIMAL:
                return (
                    latHemisfere + " " + latParts.deg.toFixed(5) + "° "
                    + lonHemisfere + " " + lonParts.deg.toFixed(5) + "°"
                );

            case this.FORMAT.DEG_MIN_SEC:
                return (
                    latHemisfere + " " + Math.floor(latParts.deg) + "° "
                    + Math.floor(latParts.min) + "' "
                    + latParts.sec.toFixed(2) + '" '
                    + lonHemisfere + " " + Math.floor(lonParts.deg) + "° "
                    + Math.floor(lonParts.min) + "' "
                    + lonParts.sec.toFixed(2) + '"'
                );
        }
    },

    getParts: function(coordinate) {
        var deg = Math.abs(coordinate);
        var min = 60 * (deg - Math.floor(deg));
        var sec = 60 * (min - Math.floor(min));
        return {deg: deg, min: min, sec: sec};
    },
};

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
