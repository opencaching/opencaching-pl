<?php
/**
 * This chunk is used to load Google Maps API to main template (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * The only thing to load G Maps API (this chunk) in template header is to call:
 *
 *     $view->loadGMapApi()
 *
 * This chunk is autoloaded in View class
 */
return function ($gMapKey, $lang){
    //start of chunk
?>

    <!-- Google Maps API chunk -->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.27&amp;key=<?=$gMapKey?>&amp;language=<?=$lang?>"></script>
    <!-- End of Google Maps API chunk -->

<?php
}; //end of chunk
