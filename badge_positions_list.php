<?php


use lib\Controllers\MeritBadgeController;
use Controllers\ViewBadgeHeadController;

require_once('./lib/common.inc.php');


global $content_table, $config, $dateFormat;

if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}

$usrid = -1;
if (isset($_REQUEST['user_id'])) {
    $userid = $_REQUEST['user_id'];
} else {
    $userid = $usr['userid'];
}

$badge_id = $_REQUEST['badge_id'];

$meritBadgeCtrl = new \lib\Controllers\MeritBadgeController;
$head= (new ViewBadgeHeadController())->index();

$tplname = 'badge_positions_list';
   

$content = "";

$positionsMeritBadge = $meritBadgeCtrl->buildArrayGainedPositions($userid, $badge_id);
$cacheTypesIcons = cache::getCacheIconsSet();

foreach( $positionsMeritBadge as $onePositionBadge ){

    $cacheName = str_replace("'", "-", $onePositionBadge->getName());
    $cacheName = str_replace("\"", " ", $cacheName);
    
    $cacheNameRef = '<a href="viewcache.php?cacheid={cacheId}">{cacheName}<a>';
    $cacheNameRef = str_replace('{cacheId}', $onePositionBadge->getId(), $cacheNameRef );
    $cacheNameRef = str_replace('{cacheName}', $cacheName, $cacheNameRef );
    
    $ownId = $onePositionBadge->getOwnerId();
    
    $userName = str_replace("'", "-", $onePositionBadge->getOwnerName());
    $userName = str_replace("\"", " ", $userName);
    
    $userNameRef = '<a href="viewprofile.php?userid={userId}">{userName}<a>';
    $userNameRef = str_replace('{userId}', $ownId, $userNameRef );
    $userNameRef = str_replace('{userName}', $userName, $userNameRef );
    
    $typeIcon ='<img src="{src}" />';
    $typeIcon = str_replace( "{src}", 
            $cacheTypesIcons[$onePositionBadge->getType()]['iconSet'][1]['iconSmallFound'], 
            $typeIcon );
    
    $date = date($dateFormat, strtotime($onePositionBadge->getGainDate()));
    $dateSort = date("y.m.d", strtotime($onePositionBadge->getGainDate()));
    
    $content .=  "
    gct.addEmptyRow();
    gct.addToLastRow( 0, '$typeIcon' );
    gct.addToLastRow( 1, '$cacheNameRef' );
    gct.addToLastRow( 2, '$userNameRef' );
    gct.addToLastRow( 3, '<span $dateSort/> $date' );
    ";
}

tpl_set_var( 'head', $head );
tpl_set_var( 'content', $content );


tpl_BuildTemplate();


