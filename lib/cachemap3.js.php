<?php
/**
 * This file provides complete JS code for cachemap_v3
 */
use lib\Objects\OcConfig\OcDynamicMapConfig;

require_once __DIR__ . '/ClassPathDictionary.php'; // class autoloader

header('Content-Type: text/javascript; charset=UTF-8');

?>

/* map configuration */

var attributionMap = <?=OcDynamicMapConfig::getJsAttributionMap()?>;
var mapItems = <?=OcDynamicMapConfig::getJsMapItems()?>;

/* This function is neccessary to load WMS configs */

<?=OcDynamicMapConfig::getWMSImageMapTypeOptions()?>

/* common JS for cachemap_v3 */
<?=file_get_contents(__DIR__ . '/cachemap3.js');?>

/* common JS for all dynamic maps */
<?=file_get_contents(
    __DIR__ . '/../tpl/stdstyle/chunks/dynamicMap/dynamicMapCommons.js');?>
