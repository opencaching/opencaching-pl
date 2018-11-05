<?php
// File for backward compatibility with OKAPI
// KEEP IT or modify OKAPI settings!

use Utils\Uri\SimpleRouter;

require_once (__DIR__.'/lib/common.inc.php');

$url = SimpleRouter::getLink('UserRegistration');
header('Location: '. $url);
exit();
