<?php

use Utils\Text\Formatter;
use lib\Objects\MeritBadge\MeritBadge; //for static functions
use lib\Controllers\MeritBadgeController;
use Controllers\ViewBadgeHeadController;
use Controllers\ViewBadgeShowPositionsController;

require_once(__DIR__.'/lib/common.inc.php');


global $content_table, $config;

if ($usr == false) {
    $target = urlencode(tpl_get_current_page());
    tpl_redirect('login.php?target=' . $target);
    exit;
}


$tplname = 'badge';
$usrid = -1;


if (isset($_REQUEST['user_id'])) {
    $userid = $_REQUEST['user_id'];
} else {
    $userid = $usr['userid'];
}



$badge_id = $_REQUEST['badge_id'];


$head= (new ViewBadgeHeadController())->index();
$meritBadgeCtrl = new \lib\Controllers\MeritBadgeController;
$userMeritBadge = $meritBadgeCtrl->buildUserBadge($userid, $badge_id);

$currUserLevel = $userMeritBadge->getOlevel()->getLevel();
$currUserThreshold = $userMeritBadge->getNextVal();
$cfg_period_threshold = $userMeritBadge->getOBadge()->getCfgPeriodThreshold();
$noLevels = $userMeritBadge->getOBadge()->getLevelsNumber();
$description = $userMeritBadge->getOBadge()->getDescription() . $userMeritBadge->getDescription();
$whoPrepared = $userMeritBadge->getOBadge()->whoPrepared();

$levelsMeritBadge = $meritBadgeCtrl->buildArrayLevels($badge_id);

$contentLvl = "";
$is_user_level = false;
$prevThreshold = 1;


foreach( $levelsMeritBadge as $oneLevel ){
    $is_user_level = false;


    $pure_level = $oneLevel->getLevel();

    if ( $currUserLevel == $pure_level )
        $is_user_level = true;

    if ($is_user_level){
        $threshold = MeritBadge::preparePeriodOrThreshold($prevThreshold, $currUserThreshold, $cfg_period_threshold );
        $prevThreshold = $currUserThreshold;
    }
    else{
        $threshold = MeritBadge::preparePeriodOrThreshold($prevThreshold,  $oneLevel->getThreshold(), $cfg_period_threshold );
        $prevThreshold = $oneLevel->getThreshold();
    }


    $color = MeritBadge::getColor($pure_level, $noLevels );
    $level = "<b style=\'color:$color\'> ".intval($pure_level+1)."</b>";

    $name = $oneLevel->getLevelName();
    $name = "<b style=\'color:$color\'>$name</b>";

    $gain = $oneLevel->getGainCounter();
    $max_date = $oneLevel->getGainLastDate() ? Formatter::date($oneLevel->getGainLastDate()) : "";


    if ($is_user_level){
        $threshold = setAsSelectedBold($threshold);
        $gain = setAsSelectedBold($gain);
        $max_date = setAsSelectedBold($max_date);
    }

    $contentLvl .= "
        gct.addEmptyRow();
        gct.addToLastRow( 0, \"$pure_level\" );
        gct.addToLastRow( 1, \"$level\" );
        gct.addToLastRow( 2, \"$name\" );
        gct.addToLastRow( 3, \"$threshold\" );
        gct.addToLastRow( 4, \"$gain\" );
        gct.addToLastRow( 5, \"$max_date\" ); ";
}




$usersMeritBadge = $meritBadgeCtrl->buildArrayUsers($badge_id);

$contentUsr = "";
$level_id = "";

foreach( $usersMeritBadge as $oneUserBadge ){


    if ( $level_id != $oneUserBadge->getLevelId()){

        if ($level_id!= "" ) $contentUsr .= "}
                ";

        $level_id = $oneUserBadge->getLevelId();
        $contentUsr .= "if (level== $level_id){";
    }

    $user_id = $oneUserBadge->getUserId();

    $pure_user_name = str_replace( '"' , '' ,$oneUserBadge->getUserName() );
    $user_name = $pure_user_name;

    $pure_curr_val = $oneUserBadge->getCurrVal();
    $curr_val = $pure_curr_val;

    $pure_ts = $oneUserBadge->getLevelDateTS();
    $curr_level_date = $oneUserBadge->getLevelDateTS() ? Formatter::date($oneUserBadge->getLevelDate()) : "";


    if ($user_id == $userid ){
        $user_name = setAsSelected( $user_name );
    }

    $pure_user_name = strtoupper($pure_user_name);
    $user_name = "<a $pure_user_name href='viewprofile.php?userid=$user_id'>$user_name</a>";
    $curr_level_date = "<spam $pure_ts>$curr_level_date</spam>";

    $contentUsr .= "
        gctU.addEmptyRow();
        gctU.addToLastRow( 0, \"$user_name\" );
        gctU.addToLastRow( 1, \"$curr_val\" );
        gctU.addToLastRow( 2, \"$curr_level_date\" );";
}

$contentUsr .= "}";

tpl_set_var( 'desc_cont', MeritBadge::sqlTextTransform($description) );
tpl_set_var( 'who_prepared', $whoPrepared);
tpl_set_var( 'userLevel', $currUserLevel );

tpl_set_var( 'user_id', $userid );
tpl_set_var( 'badge_id', $badge_id );


tpl_set_var( 'head', $head );
tpl_set_var( 'showPositions', (new ViewBadgeShowPositionsController())->index());

tpl_set_var( 'contentLvl', $contentLvl );
tpl_set_var( 'contentUsr', $contentUsr );

tpl_BuildTemplate();




function setAsSelectedBold( $value ){
    $value = "<b>" . $value . "</b>";
    return $value;
}

function setAsSelectedColor( $value ){
    $value = "<span class='GCT-color-darkred'>" . $value . "</span>";
    return $value;
}

function setAsSelected( $value ){
    return setAsSelectedColor( setAsSelectedBold( $value ) );
}
