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
    <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
<!-- End of jQuery chunk -->

<?php
}; //end of chunk
