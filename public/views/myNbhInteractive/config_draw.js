class ConfigDraw {

    constructor(map, minRadius, maxRadius) {
        if (map == null || ! map instanceof ol.Map) {
            throw "Invalid map parameter";
        }

        this.map = map;
        this.minRadius = minRadius;
        this.maxRadius = maxRadius;

        this.configDrawCommonStyles = {
            'Circle': [
                new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: [255, 255, 0, 0.35]
                    }),
                    stroke: new ol.style.Stroke({
                        color: [0, 0, 0, 0.8],
                        width: 1
                    }),
                }),
            ]
        };
        this.configDrawCommonStyles['GeometryCollection'] =
            this.configDrawCommonStyles['Circle'].concat(
                new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 5,
                        fill: new ol.style.Fill({
                            color: [255, 255, 255, 1]
                        }),
                        stroke: new ol.style.Stroke({
                            color: [0, 0, 0, 1],
                            width: 1
                        }),
                    }),
                })
            );

        this.configDrawModifyStyles = Object.assign(
            {}, this.configDrawCommonStyles
        );
        this.configDrawModifyStyles['Point'] = [
            new ol.style.Style({
                image: new ol.style.Circle({
                    radius: 4,
                    fill: new ol.style.Fill({
                        color: [255, 255, 255, 1]
                    }),
                    stroke: new ol.style.Stroke({
                        color: [0, 0, 0, 1],
                        width: 1
                    }),
                }),
            }),
        ];

        const instance = this;
        this.configSource = new ol.source.Vector();
        this.configLayer = new ol.layer.Vector({
            source: this.configSource,
            zIndex: 1000,
            style: function(feature, resolution) {
                return instance.configDrawModifyStyles[
                    feature.getGeometry().getType()
                ];
            },
        });
        this.configLayer.set('ocLayerName', 'oc_mynbh_config');

        this.modify = new ol.interaction.Modify({
            source: this.configSource,
            style: function(feature, resolution) {
                return instance.configDrawModifyStyles[
                    feature.getGeometry().getType()
                ];
            },
        });

        this.snap = new ol.interaction.Snap({
            source: this.configSource
        });

        this.hooks = {};
    }

    setHooks(hooks) {
        if (hooks != null && typeof(hooks) === "object") {
            this.hooks = hooks;
        }
    }

    addHook(name, hook) {
        this.hooks[name] = hook;
    }

    init(initLatLon, initRadius) {
        this.map.addLayer(this.configLayer);
        this.map.addInteraction(this.snap);

        if (initLatLon && initRadius) {
            let currentGeometry = this._configGeometry(
                this._computeShapePoints(initLatLon, initRadius)
            );
            this.configSource.addFeature(new ol.Feature({
                geometry: currentGeometry,
                style: this.configDrawCommonStyles['GeometryCollection']
            }));
            this.map.getView().fit(currentGeometry.getExtent());
            this._startModifications();
        } else {
            const instance = this;
            this.draw = new ol.interaction.Draw({
                type: 'Circle',
                source: this.configSource,
                stopClick: true,
                style: function(feature, resolution) {
                    return instance.configDrawCommonStyles[
                        feature.getGeometry().getType()
                    ];
                },
                freehand: true,
                geometryFunction: this._configGeometry
            });

            this.draw.on("drawend", function(ev) {
                return instance._drawComplete(ev.feature);
            });
        }
    }

    startDrawing() {
        if (this.draw) {
            this.map.addInteraction(this.draw);
        }
    }

    _configGeometry(coordinates, geometry) {
        let center = coordinates[0];
        let last = coordinates[1];
        let dx = center[0] - last[0];
        let dy = center[1] - last[1];
        let radius = Math.sqrt(dx * dx + dy * dy);
        if (!geometry) {
            geometry = new ol.geom.GeometryCollection();
        }
        geometry.setGeometries([
            new ol.geom.Point(center),
            new ol.geom.Circle(center, radius)
        ]);
        return geometry;
    }

    _drawComplete(feature) {
        feature.setStyle(this.configDrawCommonStyles['GeometryCollection']);
        this._applyRestrictions(feature);
        this._callUpdateConfigHook(feature);
        this.map.removeInteraction(this.draw);
        this._startModifications();
    }

    _callUpdateConfigHook(feature, newCoords, newRadius) {
        if (typeof(this.hooks["updateConfig"]) === "function") {
            if (!newCoords || !newRadius) {
                [ newCoords, newRadius ] =
                    this._computeNewConfigParams(feature);
            }
            this.hooks["updateConfig"](newCoords[0], newCoords[1], newRadius);
        }
    }

    _getConfigShapes(feature, onlyCircle = false) {
        let circle, point;
        feature.getGeometry().getGeometries().forEach(function(g) {
            if (g.getType() == 'Circle') {
                circle = g;
            } else if (!onlyCircle && g.getType() == 'Point') {
                point = g;
            }
        });
        return onlyCircle ? circle : [ circle, point ];
    }

    _applyRestrictions(feature) {
        let circle = this._getConfigShapes(feature, true);
        let radius = this._radiusToKm(circle);
        let adjust = false;
        if (radius > this.maxRadius) {
            radius = this.maxRadius;
            adjust = true;
        } else if (radius < this.minRadius) {
            radius = this.minRadius;
            adjust = true;
        }
        if (adjust) {
            feature.setGeometry(this._configGeometry(
                this._computeShapePoints(circle.getCenter(), radius, false)
            ));
        }
    }

    _startModifications() {
        this.map.addInteraction(this.modify);
        const instance = this;
        this.map.on('pointermove', function(ev) {
            return instance._pointerMoveOnModify(ev);
        });
        this.modify.on('modifyend', function(ev) {
            if (ev.features.getLength() > 0) {
                let feature = ev.features.item(0);
                instance._applyRestrictions(feature);
                instance._callUpdateConfigHook(feature);
            }
        });
    }

    _pointerMoveOnModify(ev) {
        const classNames = [
            'dynamicMap_cursorResize', 'dynamicMap_cursorMove'
        ];
        let c = ev.coordinate;
        let mf = this.map.getView().getResolution() * 10;

        let ft = this.configSource.getFeatures();
        if (ft.length) {
            let [circle, point] = this._getConfigShapes(ft[0]);
            let className, dist;
            if (circle) {
                let radius = circle.getRadius();
                dist = this._computeDist(circle.getCenter(), c);
                if (dist >= (radius - mf) && dist <= (radius + mf)) {
                    className = classNames[0];
                }
            }
            if (!className && point) {
                if (!dist) {
                    dist = this._computeDist(point.getCoordinates(), c);
                }
                if (dist <= mf) {
                    className = classNames[1];
                }
            }
            let classAdded = false;
            let currentClasses = this.map.getTargetElement().classList;
            classNames.forEach(function(cn) {
                if (!className || className != cn) {
                    currentClasses.remove(cn);
                } else {
                    classAdded = (className && currentClasses.contains(cn));
                }
            });
            if (className && !classAdded) {
                currentClasses.add(className);
            }
        }
    }

    _computeNewConfigParams(feature) {
        let circle = this._getConfigShapes(feature, true);
        let newCoords = this._toLatLon(circle.getCenter());
        let newRadius = this._radiusToKm(circle);
        return [ newCoords, newRadius ];
    }

    _computeDist(startCoords, finalCoords) {
        let dx = startCoords[0] - finalCoords[0];
        let dy = startCoords[1] - finalCoords[1];
        return Math.sqrt(dx * dx + dy * dy);
    }

    _computeShapePoints(centerCoords, radiusInKm, coordsAreLatLon = true) {
        let centerLatLon =
            coordsAreLatLon ? centerCoords : this._toLatLon(centerCoords);
        let centerPoint =
            coordsAreLatLon ? this._fromLatLon(centerCoords) : centerCoords;
        let factor = 1/Math.cos(centerLatLon[0] * Math.PI / 180);
        let radiusPoint = ol.extent.getTopRight(
            (new ol.geom.Circle(
                centerPoint, radiusInKm * 1000 * factor
            )).getExtent()
        );
        radiusPoint[1] = centerPoint[1];
        return [ centerPoint, radiusPoint ];
    }

    _radiusToKm(circle) {
        let centerLatLon = this._toLatLon(circle.getCenter());
        let factor = 1/Math.cos(centerLatLon[0] * Math.PI / 180);
        return Math.round( ( circle.getRadius() / factor ) / 1000 );
    }

    _fromLatLon(latlonPoint) {
        return ol.proj.transform(
            (latlonPoint.constructor === Array)
            ? [ latlonPoint[1], latlonPoint[0] ]
            : [ latlonPoint.lon, latlonPoint.lat ],
            "EPSG:4326",
            "EPSG:3857"
        );
    }

    _toLatLon(xyPoint) {
        let lonlat = ol.proj.transform(xyPoint, "EPSG:3857", "EPSG:4326");
        return [ lonlat[1], lonlat[0] ];
    }
}
