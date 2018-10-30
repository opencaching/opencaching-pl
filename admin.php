<?php

//prepare the templates and include all neccessary
require_once(__DIR__.'/lib/common.inc.php');

if ($usr['admin']) {
    $tplname = 'admin';
    tpl_BuildTemplate();
}
?>
