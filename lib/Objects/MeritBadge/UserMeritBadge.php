<?php

namespace lib\Objects\MeritBadge;


use lib\Objects\MeritBadge\MeritBadge;
use lib\Objects\MeritBadge\LevelMeritBadge;
use lib\Objects\MeritBadge\CategoryMeritBadge;


class UserMeritBadge{

    private $id;
    private $user_id;
    private $user_name;
    private $badge_id;
    private $level_id;
    private $curr_val;
    private $next_val;
    private $level_date;
    private $level_date_ts;
    private $description;

    private $oBadge;
    private $oLevel;
    private $oCategory;


    //////////////////////////////////////////////////////////////////////
    // build functions
    //////////////////////////////////////////////////////////////////////

    public function buildOBadge(){
        $this->oBadge = new \lib\Objects\MeritBadge\MeritBadge();
    }

    public function buildOLevel(){
        $this->oLevel = new \lib\Objects\MeritBadge\LevelMeritBadge();
    }

    public function buildOCategory(){
        $this->oCategory = new \lib\Objects\MeritBadge\CategoryMeritBadge();
    }



    //////////////////////////////////////////////////////////////////////
    // getField functions
    //////////////////////////////////////////////////////////////////////

    public function getId(){
        return $this->id;
    }

    public function getUserId(){
        return $this->user_id;
    }

    public function getUserName(){
        return $this->user_name;
    }

    public function getBadgeId(){
        return $this->badge_id;
    }

    public function getLevelId(){
        return $this->level_id;
    }

    public function getCurrVal(){
        return $this->curr_val;
    }

    public function getNextVal(){
        return $this->next_val;
    }

    public function getLevelDate(){
        return $this->level_date;
    }

    //timestamp
    public function getLevelDateTS(){
        return $this->level_date_ts;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getPicture(){
        if ( $this->getOLevel()->getPicture() != "" )
            return $this->getOLevel()->getPicture();

        return $this->getOBadge()->getPicture();
    }




    //////////////////////////////////////////////////////////////////////
    // getObject functions
    //////////////////////////////////////////////////////////////////////
    public function getOBadge(){
        return $this->oBadge;
    }

    public function getOLevel(){
        return $this->oLevel;
    }

    public function getOCategory(){
        return $this->oCategory;
    }



    //////////////////////////////////////////////////////////////////////
    // public functions
    //////////////////////////////////////////////////////////////////////

    public function setFromRow( $rec ){

        if (isset($rec['badge_user_id']))
            $this->id = $rec['badge_user_id'];

        if (isset($rec['badge_user_badge_id']))
            $this->badge_id = $rec['badge_user_badge_id'];

        if (isset($rec['badge_user_user_id']))
            $this->user_id = $rec['badge_user_user_id'];

        if (isset($rec['badge_user_user_name']))
            $this->user_name = $rec['badge_user_user_name'];

        if (isset($rec['badge_user_level_date']))
            $this->level_date = $rec['badge_user_level_date'];

        if (isset($rec['badge_user_level_date_ts']))
            $this->level_date_ts = $rec['badge_user_level_date_ts'];

        if (isset($rec['badge_user_level_id']))
            $this->level_id = $rec['badge_user_level_id'];

        if (isset($rec['badge_user_curr_val']))
            $this->curr_val = $rec['badge_user_curr_val'];

        if (isset($rec['badge_user_next_val']))
            $this->next_val = $rec['badge_user_next_val'];

        if (isset($rec['badge_user_description']))
            $this->description = $rec['badge_user_description'];
    }

}

