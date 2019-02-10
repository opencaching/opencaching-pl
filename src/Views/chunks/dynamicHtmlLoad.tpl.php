<?php
/**
 * This is just a wrapper for ajax load html content into element
 */
return function ($url, $domContainerId){
    //start of chunk
?>

  <?php if(!is_null($url) && !is_null($domContainerId)){ ?>
    <script>
      $('#<?=$domContainerId?>').load("<?=$url?>");
    </script>
  <?php } else { ?>
    <!-- dynamicHtmlChunk with null url or null container?! -->
  <?php } ?>

<?php
}; //end of chunk
