/**
 * A prototype of marker where style is dependant on current zoom level.
 * The zoom-based styles, including caption or caption-less ones, are cached on
 * create for future use.
 * It is not advisable to instatiate it directly, rather instatiate one of
 * derived subclasses
 */
function OCZoomCachedMarker(map, ocData) {
    OCMarker.call(this, map, ocData);
    // zoomRanges contain named styles with an array of minimum and maximum zoom
    // (inclusive) where each style is applicable
    this.zoomRanges = {};
    this.zoomStyles = [];
    this.captionStyle;
    this.noCaptionStyle;
}

OCZoomCachedMarker.prototype = Object.create(OCMarker.prototype);

OCZoomCachedMarker.prototype.constructor = OCZoomCachedMarker;

/**
 * Returns the first one of zoomRanges attributes, where zoom given as a
 * parameter lies within the attribute's value range.
 */
OCZoomCachedMarker.prototype.getZoomRange = function(zoom) {
    var result;
    var self = this;
    Object.keys(this.zoomRanges).some(function(r) {
        if (
            self.zoomRanges[r][0] <= zoom
            && self.zoomRanges[r][1] >= zoom
        ) {
            result = {
                name: r,
                range: self.zoomRanges[r],
            };
            return true;
        }
    });
    return result;
}

/**
 * Returns cached zoom style basing on zoom value given as a parameter. If no
 * style is cached for given zoom, a new style is computed by calling
 * newStyleCallback.
 * The newStyleCallback parameter should be an instance method with parameters:
 * - zoom value
 * - caption level: 0 - no caption, 1 - caption hidden, 2 - caption visible
 */
OCZoomCachedMarker.prototype.getCachedZoomStyle = function (
    zoom, newStyleCallback
) {
    var markerStyle;
    var markerStyle =
        (typeof(this.zoomStyles[zoom]) !== "undefined")
        ? this.zoomStyles[zoom]
        : undefined;
    if (
        markerStyle == this.captionStyle
        && !this.feature.get("isFirst")
        && this.noCaptionStyle != undefined
    ) {
        markerStyle = this.noCaptionStyle;
    } else if (markerStyle == undefined) {
        var newMarkerStyle;
        var showCaption = this.canShowCaption(zoom);
        if (showCaption && this.captionStyle !== undefined) {
            newMarkerStyle = this.captionStyle;
        } else if (!showCaption) {
            var zoomRange = this.getZoomRange(zoom);
            var instance = this;
            this.zoomStyles.some(function(s, z) {
                if (
                    zoomRange.range[0] <= z
                    && zoomRange.range[1] >= z
                    && s !== instance.captionStyle
                ) {
                    newMarkerStyle = s;
                    return true;
                }
            });
        }
        if (newMarkerStyle == undefined) {
            newMarkerStyle = newStyleCallback.call(
                this, zoom, showCaption ? 2 : 0
            );
            if (showCaption) {
                this.captionStyle = newMarkerStyle;
                this.noCaptionStyle = newStyleCallback.call(this, zoom, 1);
            }
        }
        this.zoomStyles[zoom] = newMarkerStyle;
        if (showCaption && !this.feature.get("isFirst")) {
            markerStyle = this.noCaptionStyle;
        } else {
            markerStyle = newMarkerStyle;
        }
    }
    this.currentStyle = markerStyle;
    return markerStyle.style;
}

/**
 * Returns true if created style should contain caption, basing on zoom value.
 * This method should be overwritten in subclass.
 * Notice: caption visibility is dependant not only on this method output but on
 * visible features order too.
 */
OCZoomCachedMarker.prototype.canShowCaption = function (zoom) {
    return false;
}
