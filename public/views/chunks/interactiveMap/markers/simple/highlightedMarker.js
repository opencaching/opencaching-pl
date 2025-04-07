/**
 * Marker for displaying highlight for another feature
 */
function HighlightedMarker(map, ocData) {
    OCMarker.call(this, map, ocData);
}

HighlightedMarker.prototype = Object.create(OCMarker.prototype);

HighlightedMarker.prototype.constructor = HighlightedMarker;

/**
 * Creates and returns style of the marker: a green square
 */
HighlightedMarker.prototype.getFeatureStyle = function(resolution) {
    if (this.currentStyle == undefined) {
        this.currentStyle = {
            style: new ol.style.Style({
                image: new ol.style.RegularShape({
                    stroke: new ol.style.Stroke({
                        color: 'green',
                        width: 2
                    }),
                    points: 4,
                    radius: 15,
                    angle: Math.PI / 4
                })
            })
        };
    }
    return this.currentStyle.style;
}
