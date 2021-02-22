<?php

use src\Models\MeritBadge\MeritBadge;
use src\Controllers\MeritBadgeController;
use src\Models\ApplicationContainer;

require_once (__DIR__.'/lib/common.inc.php');
require_once (__DIR__.'/src/Views/user_badges.inc.php');

global $content_table;

$loggedUser = ApplicationContainer::GetAuthorizedUser();
if (!$loggedUser) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}

$tplname = 'user_badges';

if (isset($_REQUEST['user_id'])) {
    $userid = $_REQUEST['user_id'];
} else {
    $userid = $loggedUser->getUserId();
}

$meritBadgeCtrl = new \src\Controllers\MeritBadgeController;
$userCategories = $meritBadgeCtrl->buildArrayUserCategories($userid);


$content = '';
foreach($userCategories as $oneCategory){

    $category_table=mb_ereg_replace('{category}', $oneCategory->getName(), $content_table);
    $badgesInCategory = $meritBadgeCtrl->buildArrayUserMeritBadgesInCategory( $userid, $oneCategory->getId() );

    $content_badge = '';

    foreach($badgesInCategory as $oneBadge){

        $element=$content_element;
        $element=mb_ereg_replace('{name}', $oneBadge->getOBadge()->getName(), $element);

        $element=mb_ereg_replace('{short_desc}', MeritBadge::prepareShortDescription(   $oneBadge->getOBadge()->getShortDescription(),
                                                                                        $oneBadge->getNextVal(),
                                                                                        $oneBadge->getCurrVal()),
                                                                                        $element );

        $element=mb_ereg_replace('{picture}', $oneBadge->getPicture(), $element );
        $element=mb_ereg_replace('{level_name}', $oneBadge->getOLevel()->getLevelName(), $element );
        $element=mb_ereg_replace('{badge_id}', $oneBadge->getBadgeId(), $element );
        $element=mb_ereg_replace('{user_id}', $userid, $element );
        $element=mb_ereg_replace('{curr_val}', $oneBadge->getCurrVal(), $element );
        $element=mb_ereg_replace('{progresbar_curr_val}', MeritBadge::getProgressBarCurrValue($oneBadge->getOLevel()->getPrevThreshold(), $oneBadge->getCurrVal(), $oneBadge->getNextVal()), $element );
        $element=mb_ereg_replace('{progresbar_next_val}', MeritBadge::getProgressBarValueMax($oneBadge->getOLevel()->getPrevThreshold(), $oneBadge->getNextVal()), $element );
        $element=mb_ereg_replace('{next_val}', MeritBadge::prepareTextThreshold($oneBadge->getNextVal()), $element );
        $element=mb_ereg_replace('{progresbar_size}', MeritBadge::getBarSize( $oneBadge->getLevelId(), $oneBadge->getOBadge()->getLevelsNumber() ), $element );
        $element=mb_ereg_replace('{progresbar_color}', MeritBadge::getColor( $oneBadge->getLevelId(), $oneBadge->getOBadge()->getLevelsNumber() ), $element );

        $content_badge.= $element;
    }
    $content.=mb_ereg_replace('{content_badge_img}', $content_badge, $category_table);
}

$content.="<a class='links'  href='user_badges.php?user_id=999999'>[".tr('merit_badge_show_list')."]</a><br><br>";

tpl_set_var( 'content', $content );

tpl_BuildTemplate();
