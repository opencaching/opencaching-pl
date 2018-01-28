<?php
/**
 * This chunk is used to load jQuery to main template (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * The only thing to load jQuery (this chunk) in template header is to call:
 *
 *     $view->loadJQuery()
 *
 * This chunk is autoloaded in View class
 */
return function (){
    //start of chunk
?>

<!-- jQuery chunk -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<!-- <script src="https://code.jquery.com/jquery-migrate-3.0.1.min.js"></script> -->
<!-- End of jQuery chunk -->

<?php
}; //end of chunk
