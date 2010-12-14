<?php
 define(‘BASE_DIR’, ‘../lib/zend’);

  require_once(BASE_DIR . ‘lib/bootstrap.php’);
#
   $main = Zend_Controller_Front::getInstance();

   $main->throwExceptions(false);
    //GO!
    $main->dispatch();
?>