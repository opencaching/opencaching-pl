<?php
/**
 * This chunk is used to load bootstrap css to templates (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * This chunk should be load to all new common layouts templates
 *
 */
return function (){
    //start of chunk
?>

<!-- bootstrap CSS -->

<link rel="stylesheet"
  href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css"
  integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M"
  crossorigin="anonymous">

<!-- / bootstrap CSS -->

<?php
}; //end of chunk
