<?php
// File for backward compatibility with OKAPI
// KEEP IT or modify OKAPI settings!

use Utils\Uri\SimpleRouter;

require_once('./lib/common.inc.php');

$url = SimpleRouter::getLink('userAuthorization', 'register');
header('Location: '. $url);
exit();