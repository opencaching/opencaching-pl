<?php

/**
 *
 * details: https://cdnjs.com/libraries/openlayers
 */
return function ($debugVersion=null){
    //start of chunk

?>
  <?php if(!$debugVersion){ ?>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.5/ol.css"
          type="text/css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.5/ol.js"></script>

<?php } else { ?>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.5/ol-debug.css"
          type="text/css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/4.6.5/ol-debug.js"></script>

<?php } ?>

<?php
}; //end of chunk
