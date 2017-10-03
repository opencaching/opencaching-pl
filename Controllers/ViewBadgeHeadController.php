<?php

namespace Controllers;

use lib\Objects\MeritBadge\MeritBadge; //for static functions
use lib\Controllers\MeritBadgeController;


class ViewBadgeHeadController extends BaseController{

    private $sCode = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        if( $this->loggedUser->getUserId()== null ){
              self::redirectToLoginPage();
              exit;
        }

        $usrid = -1;
        if (isset($_REQUEST['user_id'])) {
            $userid = $_REQUEST['user_id'];
        } else {
            $userid = $this->loggedUser->getUserId();
        }

        $badge_id = $_REQUEST['badge_id'];

        $meritBadgeCtrl = new \lib\Controllers\MeritBadgeController;
        $userMeritBadge = $meritBadgeCtrl->buildUserBadge($userid, $badge_id);

        $currUserLevel = $userMeritBadge->getOlevel()->getLevel();
        $currUserLevelName = $userMeritBadge->getOlevel()->getLevelName();
        $currUserCurrVal = $userMeritBadge->getCurrVal();
        $currUserThreshold = $userMeritBadge->getNextVal();
        $currUserPrevThreshold = $userMeritBadge->getOlevel()->getPrevThreshold();

        $cfg_period_threshold = $userMeritBadge->getOBadge()->getCfgPeriodThreshold();
        $noLevels = $userMeritBadge->getOBadge()->getLevelsNumber();

        $description = $userMeritBadge->getOBadge()->getDescription() . $userMeritBadge->getDescription();

        $whoPrepared = $userMeritBadge->getOBadge()->whoPrepared();

        $this->preapareCode();

        $this->setVar( 'picture', $userMeritBadge->getPicture() );
        $this->setVar( 'progresbar_curr_val', $currUserCurrVal-$currUserPrevThreshold);
        $this->setVar( 'progresbar_next_val', MeritBadge::getProgressBarValueMax($currUserPrevThreshold, $currUserThreshold) );
        $this->setVar( 'progresbar_color', MeritBadge::getColor($currUserLevel, $noLevels) );
        $this->setVar( 'progresbar_size', MeritBadge::getBarSize($currUserLevel, $noLevels) );
        $this->setVar( 'badge_name', $userMeritBadge->getOBadge()->getName() );
        $this->setVar( 'badge_short_desc', MeritBadge::prepareShortDescription( $userMeritBadge->getOBadge()->getShortDescription(), $currUserThreshold, $currUserCurrVal) );
        $this->setVar( 'desc_cont', MeritBadge::sqlTextTransform($description) );
        $this->setVar( 'who_prepared', $whoPrepared);

        $this->setVar( 'userLevel', $currUserLevel );

        $this->setVar( 'userLevelName', $currUserLevelName );
        $this->setVar( 'userCurrValue', $currUserCurrVal );
        $this->setVar( 'userThreshold', MeritBadge::prepareTextThreshold($currUserThreshold) );

        return $this->sCode;
    }

    private function setVar($name, $value){
        $this->sCode= mb_ereg_replace('{' . $name . '}', $value, $this->sCode);
    }

    private function preapareCode(){
        global $stylepath;
        $this->sCode = file_get_contents($stylepath . '/badge_head.tpl.php');
        $this->sCode = tpl_do_translate($this->sCode);
    }

}