<?php
require_once __DIR__.'/../lib/common.inc.php';
if(!isset($_SESSION['user_id'])){
    print 'no hacking please!';
    exit;
}
$q = '
    UPDATE `PowerTrail_comments`
    SET `commentText`=:1,
        `logDateTime`=:2
    WHERE
        `id` =:3 AND
        `PowerTrailId` = :4 AND
        `userId` =:5
';
$text = htmlspecialchars($_REQUEST['text']);
$db = new dataBase(false);
$db->multiVariableQuery(
    $q,
    $text,  # :1
    $_REQUEST['dateTime'],      # :2
    $_REQUEST['commentId'],     # :3
    $_REQUEST['ptId'],          # :4
    $_REQUEST['callingUser']    # :5
);

sendEmail::emailOwners($_REQUEST['ptId'], '', $_REQUEST['dateTime'], $text, 'editComment');

?>