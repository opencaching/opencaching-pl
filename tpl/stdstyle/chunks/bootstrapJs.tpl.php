<?php
/**
 * This chunk is used to load bootstrap JS to templates (main.tpl/mini.tpl etc.),
 * so there is no need to call it in ordinary content templates.
 *
 * This chunk should be load to all new common layouts templates
 *
 */
return function (){
    //start of chunk
?>

<!-- bootstrap JS -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"
  integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh"
  crossorigin="anonymous"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"
  integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ"
  crossorigin="anonymous"></script>


<!-- / bootstrap JS -->

<?php
}; //end of chunk


