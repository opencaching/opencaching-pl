function createOCMarkerFeature(type, id, ocData, ocMarker, section) {
    if (typeof section === "undefined") {
        section = "_DEFAULT_";
    }
    var feature = new ol.Feature({
        geometry: new ol.geom.Point(
            ol.proj.fromLonLat([parseFloat(ocData.lon), parseFloat(ocData.lat)])
        ),
        ocData: {
            markerSection: section,
            markerType: type,
            markerId: id
        },
    });
    feature.setId(createFeatureId(type, ocData.id, section));
    if (typeof ocMarker["getFeatureStyle"] === "function") {
        ocMarker.feature = feature;
        feature.set('ocMarker', ocMarker);
        feature.setStyle(function(feature, resolution) {
            return ocMarker.getFeatureStyle(resolution)
        });
    }
    return feature;
}

/**
 * Creates id for feature, basing on given parameters
 */
function createFeatureId(markerType, localId, section) {
    if (typeof section === "undefined") {
        section = "_DEFAULT_";
    }
    return section + '_' + markerType + '_' + localId;
}

/**
 * A most top marker prototype. It is not advisable to instatiate it directly,
 * rather instatiate one of derived subclasses
 */
function OCMarker(map, ocData) {
    this.map = map;
    this.ocData = ocData;
    this.feature = undefined;
    this.currentStyle = undefined;
}

/**
 * Computes offset for possible popup placement, basing on current styles
 */
OCMarker.prototype.computePopupOffsetY = function() {
    if (
        this.currentStyle != undefined
        && typeof(this.currentStyle["style"]) !== "undefined"
    ) {
        if (typeof(this.currentStyle.style["length"]) !== "undefined") {
            var self = this;
            this.currentStyle.style.forEach(function(s) {
                var im  = s.getImage();
                if (im && im instanceof ol.style.Icon) {
                    var anchor = im.getAnchor();
                    var scale = im.getScale();
                    var cOfsY = -(anchor[1] * scale)
                    if (
                        typeof(self.currentStyle["popupOffsetY"]) === "undefined"
                        || self.currentStyle.popupOffsetY > cOfsY
                    ) {
                        self.currentStyle.popupOffsetY = cOfsY;
                    }
                }
            });
        } else if (typeof(this.currentStyle.style["getImage"]) == "function") {
            var im  = this.currentStyle.style.getImage();
            if (im && im instanceof ol.style.Icon) {
                var anchor = im.getAnchor();
                this.currentStyle.popupOffsetY = -(anchor[1] * im.getScale());
            }
        }
    }
}
