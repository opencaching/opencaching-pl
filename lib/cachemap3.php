<?php

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************** */

if (!isset($rootpath))
    $rootpath = '../';

require_once($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/cachemap3lib.inc.php');

header('Content-Type: text/javascript; charset=UTF-8');

$attributionMap = CacheMap3Lib::generateAttributionMap();
echo 'var attributionMap = ' . $attributionMap . ';';
echo "\n";

$showMapsWhenMore = CacheMap3Lib::generateShowMapsWhenMore();
echo 'var showMapsWhenMore = ' . $showMapsWhenMore . ';';
echo "\n";

$mapItems = CacheMap3Lib::generateMapItems();
echo 'var mapItems = ' . $mapItems . ';';
echo "\n\n"; 

echo read_file(dirname(__FILE__) . '/cachemap3.js');
?>