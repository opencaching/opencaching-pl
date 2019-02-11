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
  href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
  integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB"
  crossorigin="anonymous">

<!-- / bootstrap CSS -->

<?php
}; //end of chunk
