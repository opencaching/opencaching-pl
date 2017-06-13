<?php

use Utils\View\View;
use Utils\Uri\Uri;


require_once('./lib/common.inc.php');

set_tpl_subtitle('Geocache Difficulty Rating System');

$view = tpl_getView();
$view->addLocalCss(Uri::getLinkWithModificationTime('tpl/stdstyle/cacheEdit/difficultyForm.css'));

if ( isset($_POST["Rating"]) && $_POST["Rating"] == "TRUE") {
    // print results

    tpl_set_tplname('cacheEdit/difficultyFormResult');

    $Equipment = $_POST["Equipment"];
    $Night = $_POST["Night"];
    $Length = $_POST["Length"];
    $Trail = $_POST["Trail"];
    $Overgrowth = $_POST["Overgrowth"];
    $Elevation = $_POST["Elevation"];

    $Difficulty = $_POST["Difficulty"];


    $maximum = max($Equipment, $Night, $Length, $Trail, $Overgrowth, $Elevation);

    if ($maximum > 0) {
        $terrain = $maximum
            + 0.25 * ($Equipment == $maximum)
            + 0.25 * ($Night == $maximum)
            + 0.25 * ($Length == $maximum)
            + 0.25 * ($Trail == $maximum)
            + 0.25 * ($Overgrowth == $maximum)
            + 0.25 * ($Elevation == $maximum) - 0.25 + 1;
    }else{
        $terrain = 1;
    }

    $view->setVar('diffResult', $Difficulty);
    $view->setVar('terrainResult', $terrain);

} else {
    // print form
    tpl_set_tplname('cacheEdit/difficultyForm');
}


tpl_BuildTemplate();
