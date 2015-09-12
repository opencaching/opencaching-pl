<?php

if (!isset($rootpath))
    $rootpath = '../';

require_once($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/cachemap3lib.inc.php');

header('Content-Type: text/javascript; charset=UTF-8');

$cacheMap3Lib = new CacheMap3Lib();
$attributionMap = $cacheMap3Lib->generateAttributionMap();
echo 'var attributionMap = ' . $attributionMap . ';';
echo "\n";

$showMapsWhenMore = $cacheMap3Lib->generateShowMapsWhenMore();
echo 'var showMapsWhenMore = ' . $showMapsWhenMore . ';';
echo "\n";

$mapItems = $cacheMap3Lib->generateMapItems();
echo 'var mapItems = ' . $mapItems . ';';
echo "\n\n";

echo read_file(dirname(__FILE__) . '/cachemap3.js');
?>
