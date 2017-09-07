<?php

namespace Controllers;

use lib\Controllers\MeritBadgeController;

class ViewBadgeShowPositionsController extends BaseController{
    
    private $sCode = "";
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        if( $this->loggedUser->getUserId()== null ){
            $target = urlencode(tpl_get_current_page());
            tpl_redirect('login.php?target=' . $target);
            exit;
        }
        
        $usrid = -1;
        if (isset($_REQUEST['user_id'])) {
            $userid = $_REQUEST['user_id'];
        } else {
            $userid = $this->loggedUser->getUserId();
        }
        
        $badge_id = $_REQUEST['badge_id'];
        
        $ctrlMeritBadge = new MeritBadgeController;
        $meritBadge = $ctrlMeritBadge->buildMeritBadge( $badge_id );
        if ( $meritBadge->getCfgShowPositions() == "" )
            return "";
        
        $this->prepareCode();
        
        $this->setVar( 'user_id', $userid);
        $this->setVar( 'badge_id', $badge_id);
        
        return $this->sCode;
    }
        
    private function prepareCode(){
        global $stylepath;
        $this->sCode = file_get_contents($stylepath . '/badge_show_positions.tpl.php');
        $this->sCode = tpl_do_translate($this->sCode);
    }
    
    private function setVar($name, $value){
        $this->sCode= mb_ereg_replace('{' . $name . '}', $value, $this->sCode);
    }
    
}