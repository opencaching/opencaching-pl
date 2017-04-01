<?php

use lib\Objects\MeritBadge\MeritBadge;
use lib\Controllers\MeritBadgeController;

require_once('./tpl/stdstyle/user_badges.inc.php');
require_once('./lib/common.inc.php');

global $content_table;


if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}


$tplname = 'user_badges';

$usrid = -1;

if (isset($_REQUEST['user_id'])) {
    $userid = $_REQUEST['user_id'];
} else {
    $userid = $usr['userid'];
}

$meritBadgeCtrl = new \lib\Controllers\MeritBadgeController;
$userCategories = $meritBadgeCtrl->buildArrayUserCategories($userid);


$content = '';
foreach($userCategories as $oneCategory){

    $category_table=mb_ereg_replace('{category}', $oneCategory->getName(), $content_table);

    //$sB = $dbcLocCache->multiVariableQuery($queryBadge, $userid, $oneCategory->getId() );
    $badgesInCategory = $meritBadgeCtrl->buildArrayUserMeritBadgesInCategory( $userid, $oneCategory->getId() );

    $content_badge = '';

    foreach($badgesInCategory as $oneBadge){

        $element=$content_element;
        $element=mb_ereg_replace('{name}', $oneBadge->getOBadge()->getName(), $element);
        $element=mb_ereg_replace('{short_desc}', MeritBadge::prepareShortDescription( $oneBadge->getOBadge()->getShortDescription(), $oneBadge->getNextVal() ), $element );
        $element=mb_ereg_replace('{picture}', $oneBadge->getPicture(), $element );
        $element=mb_ereg_replace('{level_name}', $oneBadge->getOLevel()->getLevelName(), $element );
        $element=mb_ereg_replace('{badge_id}', $oneBadge->getBadgeId(), $element );
        $element=mb_ereg_replace('{user_id}', $userid, $element );
        $element=mb_ereg_replace('{progresbar_curr_val}', $oneBadge->getCurrVal(), $element );
        $element=mb_ereg_replace('{progresbar_next_val}', MeritBadge::getProgressBarValueMax($oneBadge->getNextVal()), $element );
        $element=mb_ereg_replace('{next_val}', MeritBadge::prepareTextThreshold($oneBadge->getNextVal()), $element );
        $element=mb_ereg_replace('{progresbar_size}', MeritBadge::getBarSize( $oneBadge->getLevelId(), $oneBadge->getOBadge()->getLevels() ), $element );
        $element=mb_ereg_replace('{progresbar_color}', MeritBadge::getColor( $oneBadge->getLevelId(), $oneBadge->getOBadge()->getLevels() ), $element );


        $content_badge.= $element;
    }
    $content.=mb_ereg_replace('{content_badge_img}', $content_badge, $category_table);
}



tpl_set_var( 'content', $content );

tpl_BuildTemplate();

?>
