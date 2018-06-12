<html>
<head>

<?php if($view->mapType == 'leafLet'){ ?>

  <?=$view->callChunk('leafLet'); ?>

<?php } else { ?>

  <?=$view->callChunk('openLayers'); ?>

<?php } ?>

<title>TEST MAP</title>
<style>
#mapCanvas {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0px;
}
</style>
</head>

<div id="mapCanvas">
</div>

<?php if($view->mapType == 'leafLet'){ ?>

<script>
    var mymap = L.map('mapCanvas').setView([54, 18], 10);

    var osmapa = L.tileLayer(
            'http://tile.openstreetmap.pl/osmapa.pl/{z}/{x}/{y}.png',
        {
            attribution: 'atr...',
            maxZoom: 18,
            id: 'OSMapa'
        }
    );
/*
    var ump = L.tileLayer(
            'http://tiles.ump.waw.pl/ump_tiles/{z}/{x}/{y}.png',
        {
            attribution: 'atr...',
            maxZoom: 18,
            id: 'UMP'
        }
    );

    var topo = L.tileLayer.wms(
        'http://mapy.geoportal.gov.pl:80/wss/service/img/guest/TOPO/MapServer/WmsServer?',
        {
            layers: 'Raster',
            format: 'image/jpeg',
            width: 768,
            height: 768
        }
    );
*/
    var ocMap = L.tileLayer(
        '/lib/mapper_okapi.php?userid=1&z={z}&x={x}&y={y}',
        {
          attribution: 'atr...',
          maxZoom: 21,
          id: 'OC'
        }
    );

    mymap.addLayer(osmapa);
    //mymap.addLayer(ump);
    //mymap.addLayer(topo);
    mymap.addLayer(ocMap);

/*
    var baseMaps = {
            "osmap": osmapa,
            "ump": ump,
            "yopo":topo
        };
*/
    var ctrlOptions = {
        collapsed: false
    }

    // put ocmap on top
//    mymap.on('baselayerchange',function(baselayer){
//      ocMap.bringToFront();
//    });


//    L.control.layers(baseMaps, null, ctrlOptions).addTo(mymap);

</script>

<?php } else { ?>

<script>

    var map = new ol.Map({

      target: 'mapCanvas',
      layers: [
        //new ol.layer.Tile({
        //  source: new ol.source.OSM()
        //}),
        new ol.layer.Tile({
          source: new ol.source.TileImage({
            url: 'http://tile.openstreetmap.pl/osmapa.pl/{z}/{x}/{y}.png',
            wrapDateLine: true,
            wrapX: true,
            noWrap: false
          })
        }),
        new ol.layer.Tile({
          source: new ol.source.TileImage({
            url: '/lib/mapper_okapi.php?userid=1&z={z}&x={x}&y={y}',
            wrapDateLine: false,
            wrapX: true,
            noWrap: false
          })
        }),

      ],
      view: new ol.View({
        center: ol.proj.fromLonLat([18.0, 54.0]),
        zoom: 10
      })
    });


</script>
<?php } ?>


</html>