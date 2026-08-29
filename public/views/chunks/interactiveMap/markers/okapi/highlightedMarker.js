/**
 * Marker for displaying highlight for another feature. It is an OKAPI-based
 * marker, but a special one, so some common functions are redefined here
 */
function HighlightedMarker(map, ocData) {
    OkapiBasedMarker.call(this, map, ocData);
    this.iconsDir = '/images/map_markers/okapi/';
}

HighlightedMarker.prototype = Object.create(OkapiBasedMarker.prototype);

HighlightedMarker.prototype.constructor = HighlightedMarker;

/**
 * Returns a size-based icon image src path
 */
HighlightedMarker.prototype.getIconSrc = function(size, showCaption) {
    return this.iconsDir + 'highlighted_' + size + '.png';
}

/**
 * Returns a size-based icon style
 */
HighlightedMarker.prototype.getIconStyle = function(size, src) {
    var result;
    if (size == 'large') {
        result = new ol.style.Style({
            image: new ol.style.Icon({
                anchorOrigin: 'bottom-left',
                anchorXUnits: 'pixel',
                anchorYUnits: 'pixel',
                anchor: [ 18,  4 ],
                src: src,
            }),
        });
    } else if (size == 'medium') {
        result = new ol.style.Style({
            image: new ol.style.Icon({
                anchorOrigin: 'bottom-left',
                anchorXUnits: 'pixel',
                anchorYUnits: 'pixel',
                anchor: [ 9,  8 ],
                src: src,
            }),
        });
    } else {
        result = new ol.style.Style({
            image: new ol.style.Icon({
                src: src,
            }),
        });
    }
    return result;
}

HighlightedMarker.prototype.getCaptionStyle = function(showCaption) {
    // no caption in any case
}

/**
 * Returns current feature style, overriding a common one.
 * Highlight style is dependant on currently highlighted other markers, so it
 * cannot be cached and has to be computed out every time.
 */
HighlightedMarker.prototype.getFeatureStyle = function(resolution) {
    var zoom = this.map.getView().getZoom();
    var showCaption = this.canShowCaption(zoom);
    this.currentStyle = this.computeNewStyle(zoom, showCaption ? 2 : 0);
    return this.currentStyle.style;
}
