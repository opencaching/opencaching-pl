
<hr/>
  <h3>Map with caches and cacheSets</h3>
  <div id="mapCanvas"></div>
  <!-- map-chunk start -->
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->mapModel, "mapCanvas");?>
  <!-- map-chunk end -->

<hr/>
  <h3>Just empty map</h3>
  <div id="emptyMapCanvas"></div>
  <!-- map-chunk start -->
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->emptyMap, "emptyMapCanvas");?>
  <!-- map-chunk end -->

<hr/>

  <h3>Drawing on the map example</h3>
  <div id="drawingMapCanvas"></div>

   <!-- map-chunk start -->
  <?php $view->callChunk('dynamicMap/dynamicMap', $view->emptyMap, "drawingMapCanvas");?>
  <!-- map-chunk end -->

  <div>
    <button id="drawCircle">draw circle</button>
    <button id="drawRectangle">draw rectangle</button>
  </div>
  <div>
    <label for="circleRadius">Circle radius</label>
    <input type="text" id="circleRadius" value="-"/>

    <label for="circleCenter">Circle center</label>
    <input type="text" id="circleCenter" value="-" size="50"/>
  </div>
  <div>
    <label for="rectCornerNE">Recatngle NE corner</label>
    <input type="text" id="rectCornerNE" value="-" size="50"/>

    <label for="rectCornerSW">Recatngle SW corner</label>
    <input type="text" id="rectCornerSW" value="-" size="50"/>
  </div>

<script>

  $(document).ready(function(){
    // start drawing whene everything is ready
    initDrawing();
  });


  function initDrawing(params)
  {
    var map = dynamicMapParams_drawingMapCanvas.map; // get right map object

    var drawingLayer = new ol.layer.Vector ({
      zIndex: 100,
      visible: true,
      source:  new ol.source.Vector([]),
      ocLayerName: 'oc_drawing', // name of layer with oc_prefix = internal OC layer

      style: new ol.style.Style({ // style for the whole layer (all features of this layer)
        stroke: new ol.style.Stroke({
          color: 'pink',
          width: 3
        }),
        fill: new ol.style.Fill({
          color: 'rgba(255, 0, 0, 0.5)'
        })
      }),
    });
    map.addLayer(drawingLayer);

    var draw;


    $('#drawRectangle').click(function(){
      map.removeInteraction(draw);
      drawingLayer.getSource().clear(true);
      $('#rectCornerNE').val('-');
      $('#rectCornerSW').val('-');

      draw = new ol.interaction.Draw({
        source: drawingLayer.getSource(),
        geometryFunction: ol.interaction.Draw.createBox(), //box
        type: "Circle",
        style: new ol.style.Style({
          stroke: new ol.style.Stroke({
            color: 'blue',
            width: 3
          }),
          fill: new ol.style.Fill({
            color: 'rgba(0, 0, 255, 0.1)'
          })
        }),
      });

      draw.on('drawend', function(evt){
        map.removeInteraction(draw);
        [minx, miny, maxx, maxy] = evt.feature.getGeometry().getExtent();
        $('#rectCornerNE').val(CoordinatesUtil.toWGS84(map, [maxx, maxy]));
        $('#rectCornerSW').val(CoordinatesUtil.toWGS84(map, [minx, miny]));
      });

      map.addInteraction(draw);
    });

    $('#drawCircle').click(function(){
      map.removeInteraction(draw);
      drawingLayer.getSource().clear(true);

      $('#circleRadius').val('-');
      $('#circleCenter').val('-');

      draw = new ol.interaction.Draw({
        source: drawingLayer.getSource(),
        type: "Circle",
        style: new ol.style.Style({
          stroke: new ol.style.Stroke({
            color: 'red',
            width: 3
          }),
          fill: new ol.style.Fill({
            color: 'rgba(0, 255, 0, 0.5)'
          })
        }),
      });

      draw.on('drawend', function(evt){
        map.removeInteraction(draw);
        var circle = evt.feature.getGeometry();
        if(circle.getType() == 'Circle'){
          $('#circleRadius').val((Math.round(circle.getRadius()/1000)) + ' km');
          $('#circleCenter').val(CoordinatesUtil.toWGS84(map, circle.getCenter()));
        }
      });

      map.addInteraction(draw);
    });
  }

  </script>

<hr/>