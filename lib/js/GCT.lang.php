<?php

$rootpath = '../../';
require_once $rootpath . 'lib/common.inc.php';

header("Content-Type: text/javascript");

?>
/* ************************************************************************
 * Provide proper translation strings to GCT javascript library
 * /lib/js/GCT.js
 * ************************************************************************
 */

var GCT_lang = new Array();
GCT_lang = {
    "prev"                  : "<?=tr('pagination_left')?>",
    "next"                  : "<?=tr('pagination_right')?>",
    "number_of_caches"      : "<?=tr('number_of_caches')?>",
};
