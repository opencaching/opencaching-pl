<?php

use src\Controllers\PictureController;

require_once (__DIR__.'/lib/common.inc.php');


if (isset($_REQUEST['uuid'])) {
    $showSpoiler = isset($_REQUEST['showspoiler']);

    $ctrl = new PictureController();
    $ctrl->thumbSizeMedium($_REQUEST['uuid'], $showSpoiler);
}
