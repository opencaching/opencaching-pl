/**
 * Marker for displaying a cache
 */
function CacheMarker(map, ocData) {
    OCMarker.call(this, map, ocData);
}

CacheMarker.prototype = Object.create(OCMarker.prototype);

CacheMarker.prototype.constructor = CacheMarker;

/**
 * Creates and returns style of the marker, based on server-side set icon
 */
CacheMarker.prototype.getFeatureStyle = function(resolution) {
    if (this.currentStyle == undefined) {
        this.currentStyle = {
            style: new ol.style.Style({
                image: new ol.style.Icon({
                    anchor: [0.5, 0.5],
                    anchorXUnits: 'fraction',
                    anchorYUnits: 'fraction',
                    src: this.ocData.icon,
                    scale: 0.6,
                })
            }),
            popupOffsetY: undefined
        };
    }
    return this.currentStyle.style;
}
