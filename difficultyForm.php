<?php

use src\Utils\Uri\Uri;

require_once(__DIR__ . '/lib/common.inc.php');

$view = tpl_getView();
$view->setSubtitle('Geocache Difficulty Rating System - ');
$view->addLocalCss(Uri::getLinkWithModificationTime('/views/cacheEdit/difficultyForm.css'));

if (isset($_POST["Rating"]) && $_POST["Rating"] == "TRUE") {
    // print results

    $view->setTemplate('cacheEdit/difficultyFormResult');

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
    } else {
        $terrain = 1;
    }

    $view->setVar('diffResult', $Difficulty);
    $view->setVar('terrainResult', $terrain);

} else {
    // print form
    $view->setTemplate('cacheEdit/difficultyForm');
}

$view->buildView();
