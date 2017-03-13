<?php

use Controllers\CacheAdoptionController;

/**
 * This script allow user to:
 * - create adoption offer
 * - accept or refuse offer from other users
 * - abort adoption offer created before
 *
 */

require_once('./lib/common.inc.php');

$ctrl = new CacheAdoptionController();
$ctrl->index();
exit();