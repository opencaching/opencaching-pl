<?php
/**
 * This is just a wrapper for ajax load html content into element
 */
return function ($url, $domContainerId){
    //start of chunk
?>
    <script>
      $('#<?=$domContainerId?>').load("<?=$url?>");
    </script>
<?php
}; //end of chunk
