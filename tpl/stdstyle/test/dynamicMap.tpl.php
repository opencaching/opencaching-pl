
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
    draw();
  });

  function draw(){
    var drawingDone = null; // this is used to allow draw only one thimg at once
    var map = dynamicMapParams_drawingMapCanvas.map; // get right map object

    var drawingManager = new google.maps.drawing.DrawingManager({
          drawingMode: null,      // nothing is drown by default
          drawingControl: false,  // hide drawing controls

          circleOptions: {
            fillColor: '#ffff00',
            fillOpacity: 0.35,
            strokeWeight: 3,
            clickable: true,
            editable: true,
            draggable: true,
          },
          rectangleOptions: {
            fillColor: '#ffff00',
            fillOpacity: 0.35,
            strokeWeight: 3,
            clickable: true,
            editable: true,
            draggable: true,
          }
    });

    drawingManager.setMap(map);

    google.maps.event.addListener(drawingManager, 'rectanglecomplete', function(rectangle) {
      // rectangle is done
      drawingManager.setDrawingMode(null); //disable drawing (to prevent next rect.)
      drawingDone = true;

      $('#rectCornerNE').val(rectangle.getBounds().getNorthEast().toString());
      $('#rectCornerSW').val(rectangle.getBounds().getSouthWest().toString());

      google.maps.event.addListener(rectangle, 'bounds_changed', function (){
        if(drawingDone){
          $('#rectCornerNE').val(rectangle.getBounds().getNorthEast().toString());
          $('#rectCornerSW').val(rectangle.getBounds().getSouthWest().toString());
        }
      });

      // use rightclick to delete drawings
      google.maps.event.addListener(rectangle, 'rightclick', function (){
        rectangle.setMap(null);
        drawingDone = null;

        $('#rectCornerNE').val('-');
        $('#rectCornerSW').val('-');

      });
    });


    google.maps.event.addListener(drawingManager, 'circlecomplete', function(circle) {
      drawingManager.setDrawingMode(null);
      drawingDone = true;

      $('#circleRadius').val(circle.getRadius());
      $('#circleCenter').val(circle.getCenter().toString());

      google.maps.event.addListener(circle, 'radius_changed', function (){
        if(drawingDone){
          $('#circleRadius').val(circle.getRadius());
        }
      });

      google.maps.event.addListener(circle, 'center_changed', function (){
        if(drawingDone){
          $('#circleCenter').val(circle.getCenter().toString());
        }
      });

      google.maps.event.addListener(circle, 'rightclick', function (){
        circle.setMap(null);
        drawingDone = null;
        $('#circleRadius').val('-');
        $('#circleCenter').val('-');
      });
    });


    $('#drawRectangle').click(function(){
      if(!drawingDone){
        drawingManager.setDrawingMode('rectangle');
      }
    });

    $('#drawCircle').click(function(){
      if(!drawingDone){
        drawingManager.setDrawingMode('circle');
      }
    });

  }
  </script>

<hr/>