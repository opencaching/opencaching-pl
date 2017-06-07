<?php
/**
 * This chunk is used to load jQuery to main template (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * The only thing to load jQuery (this chunk) in template header is to call:
 *
 *     $view->loadJquery()
 *
 * This chunk is autoloaded in View class
 */
return function (){
    //start of chunk
?>

<!-- jQuery chunk -->
<script src="tpl/stdstyle/js/jquery-2.0.3.min.js"></script>
<!-- End of jQuery chunk -->

<?php
}; //end of chunk
