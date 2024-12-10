/**
 * Marker for displaying a log of a cache
 */
function LogMarker(map, ocData) {
    OCMarker.call(this, map, ocData);
}

LogMarker.prototype = Object.create(OCMarker.prototype);

LogMarker.prototype.constructor = LogMarker;

/**
 * Creates and returns style of the marker, based on server-side set log icon
 */
LogMarker.prototype.getFeatureStyle = function(resolution) {
    if (this.currentStyle == undefined) {
        this.currentStyle = {
            style: new ol.style.Style({
                image: new ol.style.Icon({
                    anchor: [0.5, 0.5],
                    anchorXUnits: 'fraction',
                    anchorYUnits: 'fraction',
                    src: this.ocData.logIcon,
                    scale: 1,
                })
            }),
            popupOffsetY: undefined
        };
    }
    return this.currentStyle.style;
}
