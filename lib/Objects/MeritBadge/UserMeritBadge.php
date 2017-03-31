<?php

namespace lib\Objects\MeritBadge;


use lib\Objects\MeritBadge\MeritBadge;
use lib\Objects\MeritBadge\LevelMeritBadge;
use lib\Objects\MeritBadge\CategoryMeritBadge;
use lib\Objects\BaseObject;


class UserMeritBadge extends BaseObject{

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

    public function __construct()
    {
        parent::__construct();
    }

    private static function FromDataArrayFactory($rec)
    {
        $userBadge = new self();
        $userBadge->setFromRow($rec);

        $userBadge->oBadge = new MeritBadge();
        $userBadge->oLevel = new LevelMeritBadge();
        $userBadge->oCategory = new CategoryMeritBadge();

        $userBadge->getOBadge()->setFromRow($rec);
        $userBadge->getOLevel()->setFromRow($rec);
        $userBadge->getOCategory()->setFromRow($rec);

        return $userBadge;
    }

    public static function FromUserAndBadgeIdFactory($user_id, $badge_id)
    {
        $condition = " WHERE badge_user.user_id=:1 and badges.id=:2 ";
        foreach(self::GetCommonQueryResults($condition, $user_id, $badge_id) as $userBadge){
            return $userBadge; //return the first arg
        }
        return null;
    }

    public static function GetUserMeritBadgesInCategory($user_id, $category_id)
    {
        $condition = " WHERE badge_user.user_id=:1 and badge_categories.id =:2 ";
        return self::GetCommonQueryResults($condition, $user_id, $category_id);
    }

    private static function GetCommonQueryResults($condition, $args) //TODO: there is lack of arg!
    {
        $db = self::Db();

        $stm = $db->multiVariableQuery(
            "SELECT
            badges.name                 badges_name,
            badges.short_description    badges_short_description,
            badges.description          badges_description,
            lvl.amount                  badges_levels,
            badges.picture              badges_picture,
            badges.cfg_period_threshold badges_cfg_period_threshold,
            badges.graphic_author       badges_graphic_author,
            badges.description_author   badges_description_author,
            badges.attendant            badges_attendant,

            badge_levels.picture        badge_levels_picture,
            badge_levels.level          badge_levels_level,
            badge_levels.name           badge_levels_name,

            badge_user.id               badge_user_id,
            badge_user.badge_id         badge_user_badge_id,
            badge_user.level_id         badge_user_level_id,
            badge_user.curr_val         badge_user_curr_val,
            badge_user.next_val         badge_user_next_val,
            badge_user.description      badge_user_description,

            badge_categories.name       badge_categories_name

            FROM
            badge_user
            JOIN badge_levels ON badge_user.badge_id = badge_levels.badge_id AND badge_user.level_id = badge_levels.level
            JOIN badges ON badge_user.badge_id = badges.id
            JOIN badge_categories ON badges.category_id = badge_categories.id
            JOIN
            (
                SELECT badge_id, COUNT(*) amount FROM badge_levels GROUP BY badge_id
            ) AS lvl ON lvl.badge_id = badges.id
            $condition
            ORDER BY badges.sequence", $args);

        $result = [];
        while($rec = $db->dbResultFetch($stm)){
            $result[] = self::FromDataArrayFactory($rec);
        }
        return $result;
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

    public function getShortDesc()
    {
        return MeritBadge::prepareShortDescription( $this->getOBadge()->getShortDescription(), $this->getNextVal() );
    }

    public function getTextTreshold()
    {
        return MeritBadge::prepareTextThreshold($this->getNextVal());
    }

    public function getLevelName()
    {
        return $this->getOLevel()->getLevelName();
    }


}

