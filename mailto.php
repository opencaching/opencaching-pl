<?php

use Controllers\UserProfileController;


require_once('./lib/common.inc.php');

$ctrl = new UserProfileController();
$ctrl->mailTo();
exit();
