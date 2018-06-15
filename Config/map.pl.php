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

  BingMap: new ol.layer.Tile ({
    source: new ol.source.BingMaps({
      key: '{Key-BingMap}',
      imagerySet: 'Road'
    })
  }),

  BingSatelite: new ol.layer.Tile ({
    source: new ol.source.BingMaps({
      key: '{Key-BingMap}',
      imagerySet: 'Aerial'
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
}
";

/**
 * Bing map key is set in node-local config file
 */
$map['keys']['BingMap'] = 'NEEDS-TO-BE-SET-IN-LOCAL-CONFIG-FILE';


/**
 * This is function which is called to inject keys from "local" config
 * to default node-configurations map configs.
 *
 * Here this is only a simple stub.
 *
 * @param array complete configureation merged from default + node-default + local configs
 * @return true on success
 */
$map['keyInjectionCallback'] = function(array &$mapConfig){

    // change string {Key-BingMap} to proper key value

    $mapConfig['jsConfig'] = str_replace(
        '{Key-BingMap}',
        $mapConfig['keys']['BingMap'],
        $mapConfig['jsConfig']);

    return true;
};


