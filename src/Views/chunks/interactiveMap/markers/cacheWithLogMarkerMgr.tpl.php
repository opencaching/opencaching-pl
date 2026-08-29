<?php
/**
 * This is JS object containing functions used to handle markers
 * - markerFactory - creates marker based on data row
 * - infoWindowFactory - creates inforWindow based on row data
 * - data - data rows of this type
 *
 * All functions are used from within dynamic map chunk.
 */

?>
{
    markerFactory: function(map, type, id, ocData) {
        if (typeof section === "undefined") {
            section = "_DEFAULT_";
        }
        var iconFeature = new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat([parseFloat(ocData.lon), parseFloat(ocData.lat)])),
            ocData: {
                markerSection: section,
                markerType: type,
                markerId: id
            }
        });
        feature.setId(section + '_' + type + '_' + ocData.id);
        iconFeature.setStyle(new ol.style.Style({
            image: new ol.style.Icon( {
                anchor: [0.5, 0.5],
                anchorXUnits: 'fraction',
                anchorYUnits: 'fraction',
                src: ocData.icon,
                scale: 0.5,
            })
        }));
        return iconFeature;
    },
}
