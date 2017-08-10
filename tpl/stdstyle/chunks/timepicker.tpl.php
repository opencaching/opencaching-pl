<?php
/**
 * This chunk is used to load timepicker to main template (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * The only thing to load timepicker (this chunk) in template header is to call:
 *
 *     $view->loadTimepicker()
 *
 * This chunk is autoloaded in View class
 * 
 * TODO: Change this timepicker to more beautiful one
 */

return function (){
    //start of chunk
    ?>

<!-- timepicker chunk -->
<link rel="stylesheet" property="stylesheet" href="/tpl/stdstyle/js/timepicker/jquery.ui.timepicker.css?v=0.3.3">
<script src="/tpl/stdstyle/js/timepicker/timepicker.js"></script>
<!-- End of timepicker chunk -->

<?php
}; //end of chunk
