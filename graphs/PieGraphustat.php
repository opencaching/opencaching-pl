<?php

use src\Libs\JpGraph\JpGraphLoader;
use src\Utils\Database\XDb;
use src\Utils\I18n\I18n;
use src\Models\GeoCache\GeoCache;

require(__DIR__ . '/../lib/common.inc.php');

// jpgraph package doesn't contains fonts
define('TTF_DIR', __DIR__ . '/../resources/fonts/');

JpGraphLoader::load();
JpGraphLoader::module('pie');
JpGraphLoader::module('pie3d');

$year = '';
// check for old-style parameters
if (isset($_REQUEST['userid']) && isset($_REQUEST['t'])) {
    $user_id = $_REQUEST['userid'];
    $tit = $_REQUEST['t'];
}

$y = array();
$x = array();

if ($tit == "cc") {
    $rsCreateCachesYear = XDb::xSql(
        "SELECT COUNT(`caches`.`type`) `count`, `caches`.`type`
        FROM `caches`
        WHERE `user_id`= ? AND status <> 4 AND status <>5
        GROUP BY `caches`.`type`
        ORDER BY `count` DESC", $user_id);

    if ($rsCreateCachesYear !== false) {
        while ($ry = XDb::xFetchArray($rsCreateCachesYear)) {
            $y[] = $ry['count'];
            $x[] = tr(GeoCache::CacheTypeTranslationKey($ry['type']));
        }
    }
    XDb::xFreeResults($rsCreateCachesYear);
}

if ($tit == "cf") {
    $rsCachesFindYear = XDb::xSql(
        "SELECT COUNT(`caches`.`type`) `count`, `caches`.`type`
        FROM `cache_logs`, caches
        WHERE cache_logs.`deleted`=0 AND cache_logs.user_id=? AND cache_logs.`type`='1' AND cache_logs.`cache_id` = caches.cache_id
        GROUP BY `caches`.`type`
        ORDER BY `count` DESC", $user_id);

    if ($rsCachesFindYear !== false) {
        while ($rfy = XDb::xFetchArray($rsCachesFindYear)) {
            $y[] = $rfy['count'];
            $x[] = tr(GeoCache::CacheTypeTranslationKey($rfy['type']));
        }
    }
    XDb::xFreeResults($rsCachesFindYear);
}

// A new pie graph
$graph = new PieGraph(500, 300, "auto");
$graph->SetScale('textint');
$type = tr("by_cachetype");

// Title setup
$graph->title->Set($type);
$graph->title->SetFont(FF_ARIAL, FS_NORMAL);

// Setup the pie plot
$p1 = new PiePlot($y);
$p1->SetTheme("earth");
$p1->value->SetFormat("%d");
$p1->SetLabelType(PIE_VALUE_ABS);

// Adjust size and position of plot
$p1->SetSize(0.35);
$p1->SetCenter(0.25, 0.52);

// Setup slice labels and move them into the plot
$p1->value->SetFont(FF_FONT1, FS_BOLD);
$p1->value->SetColor("darkred");
$p1->SetLabelPos(0.65);
$p1->SetLegends($x);

// Finally add the plot
$graph->Add($p1);

$graph->SetShadow();

// ... and stroke it
$graph->Stroke();
