<?php

/**
 * $map['jsConfig'] constains JS code with configuration for openlayers
 * Be sure that all changes here are well tested.
 */
$map['jsConfig'] = "
{
    OSM: new ol.layer.Tile ({
        source: new ol.source.OSM(),
    }),

    Osmapa: new ol.layer.Tile ({
        source: new ol.source.TileImage ({
            url: 'http://tile.openstreetmap.pl/osmapa.pl/{z}/{x}/{y}.png',
            attributions: \"&copy; <a href='https://www.openstreetmap.org/' target='_blank'>OpenStreetMap</a>\",
        })
    }),

    UMP: new ol.layer.Tile ({
        source: new ol.source.TileImage ({
            url: 'http://tiles.ump.waw.pl/ump_tiles/{z}/{x}/{y}.png',
            attributions: \"&copy; Mapa z <a href='http://ump.waw.pl/' target='_blank'>UMP-pcPL</a>\",
        })
    }),

    Topo: new ol.layer.Tile ({
        source: new ol.source.TileWMS({
            url: 'http://mapy.geoportal.gov.pl:80/wss/service/img/guest/TOPO/MapServer/WmsServer',
            attributions: \"&copy; <a href='http://geoportal.gov.pl/' target='_blank'>geoportal.gov.pl</a>\",
            params: {
                VERSION: '1.1.1',
                LAYERS: 'Raster',
                TILED: true,
                FORMAT: 'image/jpeg',
                BGCOLOR: '0xFFFFFF',
                TRANSPARENT: false
            },
            projection: 'EPSG:4326',
            tileGrid: ol.tilegrid.createXYZ({
                extent: ol.proj.get('EPSG:4326').getExtent(),
                tileSize: [768, 768]
            }),
        }),
    }),

    Orto: new ol.layer.Tile({
        source: new ol.source.TileWMS({
            url: 'http://mapy.geoportal.gov.pl:80/wss/service/img/guest/ORTO/MapServer/WmsServer',
            attributions: \"&copy; <a href='http://geoportal.gov.pl/' target='_blank'>geoportal.gov.pl</a>\",
            params: {
                VERSION: '1.1.1',
                LAYERS: 'Raster',
                TILED: true,
                FORMAT: 'image/jpeg',
                BGCOLOR: '0xFFFFFF',
                TRANSPARENT: false
            },
            projection: 'EPSG:4326',
            tileGrid: ol.tilegrid.createXYZ({
                extent: ol.proj.get('EPSG:4326').getExtent(),
                tileSize: [768, 768]
            }),
        }),
    }),

    ESRITopo: new ol.layer.Tile({
        source: new ol.source.XYZ({
            attributions: 'Tiles © <a href=\"https://services.arcgisonline.com/ArcGIS/' +
                'rest/services/World_Topo_Map/MapServer\">ArcGIS</a>',
            url: 'https://server.arcgisonline.com/ArcGIS/rest/services/' +
                'World_Topo_Map/MapServer/tile/{z}/{y}/{x}'
        })
    }),

    ESRIStreet: new ol.layer.Tile({
        source: new ol.source.XYZ({
            attributions: 'Tiles © <a href=\"https://services.arcgisonline.com/ArcGIS/' +
                'rest/services/World_Street_Map/MapServer\">ArcGIS</a>',
            url: 'https://server.arcgisonline.com/ArcGIS/rest/services/' +
                'World_Street_Map/MapServer/tile/{z}/{y}/{x}'
        })
    }),
}
";
