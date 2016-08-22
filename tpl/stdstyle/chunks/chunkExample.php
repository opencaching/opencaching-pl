<?php
/**
 * This is example of chunk - It is only to demonstrate the chunk idea
 * The purpose of chunk is to prepare the "function" for template
 *
 * To use this chunk:
 * - call from controller file:
 *
 *      $view = tpl_getView();
 *      $view->loadChunk('chunkExample');
 *
 * - call chunk from template file for example:
 *
 *  <h1>Our girls:</h1>
 *      $view->chunkExample($view->girls);
 *  <h1>Our boys:</h1>
 *      $view->chunkExample($view->boys);
 *
 * where:
 *      - $view->girls was set as array('ann','olga') in controller
 *      - $view->boys was set as array('bob','luke') in controller
 *
 * List of arguments for chunk should be documented on chunk file
 *
 */
return function( array $persons ){
// start of chunk code
?>
  <ul>

    <?php foreach($persons as $p){ ?>
    <li>This chunk says hello to: <?=$p?>!</li>
    <?php }//foreach ?>

  </ul>
<?php
}; //end of chunk

