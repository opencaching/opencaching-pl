<?php
// File for backward compatibility with old register.php system
// Will be deleted in future

use Utils\Uri\SimpleRouter;
use Utils\Database\OcDb;

require_once('./lib/common.inc.php');

$uuid = (isset($_REQUEST['user'])) ? $_REQUEST['user'] : null;
$code = (isset($_REQUEST['code'])) ? $_REQUEST['code'] : null;

if (! empty($uuid) && ! empty($code)) {
    $db = OcDb::instance();
    $userId = $db->multiVariableQueryValue('SELECT `user_id` FROM `user` WHERE `uuid` = :1 LIMIT 1', null, $uuid);
    $url = SimpleRouter::getLink('userAuthorization', 'activate', [$userId, $code]);
} else {
    $url = '/';
}
header('Location: '. $url);
exit();