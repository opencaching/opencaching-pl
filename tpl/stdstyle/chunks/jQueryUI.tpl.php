<?php
/**
 * This chunk is used to load jQuery UI to main template (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * The only thing to load jQuery UI (this chunk) in template header is to call:
 *
 *     $view->loadJQueryUI()
 *
 * This chunk is autoloaded in View class
 */
return function (){
    //start of chunk
    ?>

<!-- jQuery UI chunk -->
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/i18n/jquery-ui-i18n.min.js"></script>
<!-- End of jQuery UI chunk -->

<?php
}; //end of chunk
