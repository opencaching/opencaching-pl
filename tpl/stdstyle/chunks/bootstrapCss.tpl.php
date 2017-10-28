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
  href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"
  integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb"
  crossorigin="anonymous">


<!-- / bootstrap CSS -->

<?php
}; //end of chunk
