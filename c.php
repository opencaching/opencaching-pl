<?php
//example of coordtotile.php use

include('./lib/coordtotile.php');

$zoom = 3;

$test = new GMapTile(55.396510, 10.390310, $zoom);

var_dump($test);

$p = $test->getTileCoord();

$test2 = GMapTile::fromTileCoord($p->x, $p->y, $zoom);

var_dump($test2);

?>
