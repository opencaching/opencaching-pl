<?php
use Utils\Uri\Uri;

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
return function ($gMapKey, $lang, $callback=null){
    //start of chunk

    $uri = 'https://maps.googleapis.com/maps/api/js';
    $uri = Uri::setOrReplaceParamValue('v', '3.31', $uri);
    $uri = Uri::setOrReplaceParamValue('key', $gMapKey, $uri);
    $uri = Uri::setOrReplaceParamValue('language', $lang, $uri);
    if(!is_null($callback)){
        $uri = Uri::setOrReplaceParamValue('callback', $callback, $uri);
    }

?>

    <!-- Google Maps API chunk -->
    <script src="<?=$uri?>"></script>
    <!-- End of Google Maps API chunk -->

<?php
}; //end of chunk
