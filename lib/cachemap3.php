<?php

if (!isset($rootpath))
    $rootpath = '../';

require_once($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/cachemap3lib.inc.php');

header('Content-Type: text/javascript; charset=UTF-8');

echo "\n\n// --- cachemap3lib.js ---\n\n";
    echo read_file(dirname(__FILE__) . '/cachemap3lib.js');
echo "\n\n// --- cachemap3lib.js end ---\n\n";

echo 'var attributionMap = ' . CacheMap3Lib::GenerateAttributionMap() . ';';
echo "\n";

echo 'var showMapsWhenMore = ' . CacheMap3Lib::GenerateShowMapsWhenMore() . ';';
echo "\n";

echo 'var mapItems = ' . CacheMap3Lib::GenerateMapItems() . ';';
echo "\n\n";

echo read_file(dirname(__FILE__) . '/cachemap3.js');

