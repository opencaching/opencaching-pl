<?php
/**
 * This scripts display generator of "additional cache attributes"
 */

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');

//Preprocessing
if ($error == false) {
    $tplname = 'cache-atr';
}
//make the template and send it out

tpl_BuildTemplate();
