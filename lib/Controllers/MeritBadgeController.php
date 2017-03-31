<?php
namespace lib\Controllers;

use lib\Objects\MeritBadge\MeritBadge;
use lib\Objects\MeritBadge\LevelMeritBadge;
use lib\Objects\MeritBadge\CategoryMeritBadge;

use Utils\Database\OcDb;


class MeritBadgeController{
        
    private $db;
    
    public function __construct(){
        $this->db = OcDb::instance();
    }
    
    
    
    //one user merit badge
    public function buildUserBadge( $user_id, $badge_id ){

        $condition = " WHERE badge_user.user_id=:1 and badges.id=:2 ";
        
        $stm = $this->db->multiVariableQuery( $this->getUserBadgeQuery($condition), $user_id, $badge_id );
        $rec = $this->db->dbResultFetch($stm);
        return $this->prepareUserBadgeObj( $rec );
        
    }

    //list of badges for user (user_id) and catgegory (category_id)
    public function buildArrayUserMeritBadgesInCategory( $user_id, $category_id ){
        $condition = " WHERE badge_user.user_id=:1 and badge_categories.id =:2 ";
        $stm = $this->db->multiVariableQuery( $this->getUserBadgeQuery($condition), $user_id, $category_id );
        
        $retArray = new \ArrayObject();
        
        for( $i = 0; $i < $this->db->rowCount($stm); $i++ ){
            $rec = $this->db->dbResultFetch($stm);
            $retArray->append( $this->prepareUserBadgeObj( $rec ) );
        }
        
        return $retArray;
    }
    
    
    
    //levels list of the badge (badge_id)
    public function buildArrayLevels( $badge_id ){
        $condition = " WHERE badge_levels.badge_id=:1 ";        
        $stm = $this->db->multiVariableQuery( $this->getLevelsQuery($condition), $badge_id );
        return $this->buildArray( '\lib\Objects\MeritBadge\LevelMeritBadge', $stm );
        
    }

    
    public function buildBadgeLevel( $badge_id, $level ){
        $condition = " WHERE badge_levels.badge_id=:1 and level = :2 ";
        $stm = $this->db->multiVariableQuery( $this->getLevelsQuery($condition), $badge_id, $level );
        $rec = $this->db->dbResultFetch($stm);
        
        $badgeLevel = new \lib\Objects\MeritBadge\LevelMeritBadge();
        $badgeLevel->setFromRow($rec);
        
        return $badgeLevel;
    }
    
    
    //list of users who gained the badge (badge_id)
    public function buildArrayUsers( $badge_id ){ 
        $stm = $this->db->multiVariableQuery( $this->getArrayUserQuery(), $badge_id );
        return $this->buildArray( '\lib\Objects\MeritBadge\UserMeritBadge', $stm );
    }
    

    //categories list of the user (user_id)
    public function buildArrayUserCategories( $user_id ){
        $stm = $this->db->multiVariableQuery( $this->getArrayUserCategoriesQuery(), $user_id );
        return $this->buildArray( '\lib\Objects\MeritBadge\CategoryMeritBadge', $stm );
    }
    
    public function buildArrayMeritBadgesTriggerLogs(){
        $condition = " WHERE trigger_type=0 "; //0 -log, 1 - cron
        $stm = $this->db->simpleQuery( $this->getMeritBadgeQuery( $condition ) );
        return $this->buildArray( '\lib\Objects\MeritBadge\MeritBadge', $stm );
    }


    public function buildMeritBadge( $badge_id ){
        $condition = " WHERE badges.id=:1 ";
        $stm = $this->db->multiVariableQuery( $this->getMeritBadgeQuery( $condition ), $badge_id );
        return $this->buildArray( '\lib\Objects\MeritBadge\MeritBadge', $stm );
    }
    
    
    
    ///////////////////////////////////////////////////////////////////////
    //Update Merit Badges
    //
    // Check, which merit badges we need to change
    // Next: set curr_val, and proper level
    //
    // Return: The array of badge ids, which have changed the level
    ///////////////////////////////////////////////////////////////////////
    public function updateCurrValMeritBadges( $cache_id, $user_id ){
        $meritBadges = $this->buildArrayMeritBadgesTriggerLogs();
        $changedLevelBadgesIds = "";
        $first = true; 
       
        foreach( $meritBadges as $oneMeritBadge ){
                        
            if ( !$this->isToUpdate( $cache_id, $oneMeritBadge ) )
                continue;
                                
             $currValue = $this->calcCurrValForUserBadge($user_id, $oneMeritBadge);
             
            if (!$currValue)
                continue; 
                
             if ( !$this->isExistUserBadge( $user_id, $oneMeritBadge->getId() ) ){
                 $this->insertUserBadge($currValue, $user_id, $oneMeritBadge->getId() );
             }
             else{
                 $this->updateCurrValInUserBadge($currValue, $user_id, $oneMeritBadge->getId() );
             }
            
             if ( $this->setProperValueUserBadge( $user_id, $oneMeritBadge->getId() ) ){                 
                 if (!$first)
                     $changedLevelBadgesIds .= ",";
                 
                 $changedLevelBadgesIds .= $oneMeritBadge->getId(); //the level was changed
                 $first = false;
             }
        }
        
        
        return $changedLevelBadgesIds;
    }
    
    
    public function prepareHtmlChangeLevelMeritBadges( $arrBadgesNextLevel, $user_id ){
        $html = "";
        $firstEl = true;
        
        foreach ($arrBadgesNextLevel as $badge_id){
                        
            $element = $this->patternHtmlChangeLevelMeritBadges( $firstEl );
            
            $userMeritBadge = $this->buildUserBadge( $user_id, $badge_id );
                    
            $short_desc = MeritBadge::prepareShortDescription( $userMeritBadge->getOBadge()->getShortDescription(), $userMeritBadge->getNextVal() );
            
            $element=mb_ereg_replace('{name}', $userMeritBadge->getOBadge()->getName(), $element);
            $element=mb_ereg_replace('{short_desc}', $short_desc , $element);
            $element=mb_ereg_replace('{picture}', $userMeritBadge->getPicture(), $element );
            $element=mb_ereg_replace('{level_name}', $userMeritBadge->getOLevel()->getLevelName(), $element );
            $element=mb_ereg_replace('{curr_val}', $userMeritBadge->getCurrVal(), $element );
            $element=mb_ereg_replace('{next_val}', MeritBadge::prepareTextThreshold($userMeritBadge->getNextVal()), $element );
            
            $html.= $element;
            $firstEl = false;
        }
        
        return $html;
    }
    
    
    
    //////////////////////////////////////////////////////////////////////
    // private functions
    //////////////////////////////////////////////////////////////////////

    private function patternHtmlChangeLevelMeritBadges($firstEl){
        $header = "<p style='font-size:12px; font-weight:bold; color:green; text-decoration: underline;'> 
                Osiagnąłes nowy poziom dla sprawnosci: </p><br>" ;
        $htmlNewElement = "<hr><br>";
        
        $pattern = "<div style ='width:500px;'><img src='{picture}' style= 'float: left;margin-right:20px;' />
            <p style='font-size:20px; font-weight:bold;'> {name} <br>
            <span style='font-size:13px; font-weight:normal; font-style:italic;'> {short_desc} </span></p>
            <p style='font-size:13px;font-weight:normal;'>"
            .tr('merit_badge_level_name').": <b>{level_name}</b><br>"
            .tr('merit_badge_number').": <b>{curr_val}</b><br>"
            .tr('merit_badge_next_level_threshold').": <b>{next_val}</b><br>
            </p></div><br>";        
        
            if ( !$firstEl )
                return $htmlNewElement . $pattern;
            
        return $pattern;
    }
    
    private function buildArray( $class, $stm ){
        $retArray = new \ArrayObject();
    
        for( $i = 0; $i < $this->db->rowCount($stm); $i++ ){
    
            $rec = $this->db->dbResultFetch($stm);
    
            $one = new $class;
            $one->setFromRow($rec);
            $retArray->append($one);
        }
    
        return $retArray;
    }
    
    
    private function prepareUserBadgeObj( $rec )
    {
        $userBadge = new \lib\Objects\MeritBadge\UserMeritBadge();
        $userBadge->buildOBadge();
        $userBadge->buildOLevel();
        $userBadge->buildOCategory();
    
    
        $userBadge->setFromRow($rec);
        $userBadge->getOBadge()->setFromRow($rec);
        $userBadge->getOLevel()->setFromRow($rec);
        $userBadge->getOCategory()->setFromRow($rec);
    
        return $userBadge;
    }
    
    
    
    private function getProperBadgeLevel($badge_id, $curr_val ){
        $query= "SELECT level 
                FROM badge_levels 
                WHERE badge_id = :1 AND threshold > :2
                ORDER BY level
                LIMIT 1 ";
        
        $stm = $this->db->multiVariableQuery( $query, $badge_id, $curr_val );
        $rec = $this->db->dbResultFetch($stm);
        $level = $rec[ "level"];
        
        return $this->buildBadgeLevel( $badge_id, $level );
    }
    
    private function insertUserBadge($curr_val, $user_id, $badge_id ){
        $meritBadge = $this->buildMeritBadge( $badge_id );
        
        $query= "INSERT INTO badge_user
                (user_id, badge_id, level_id, level_date, curr_val, next_val, description)
                VALUES (:1, :2, :3, :4, :5, :6, :7)";
        
        $this->db->multiVariableQuery( $query, 
                                        $user_id, 
                                        $badge_id, 
                                        0, //level_id 
                                        "1971-12-20",
                                        $curr_val,
                                        0, //threshold  
                                        "" //description 
                                    );
    }
    
    
    private function updateCurrValInUserBadge($curr_val, $user_id, $badge_id ){
        $query = "UPDATE badge_user SET curr_val= :1 WHERE user_id = :2 and badge_id=:3";
        $this->db->multiVariableQuery( $query, $curr_val, $user_id, $badge_id );
    }
    
    
    private function isExistUserBadge( $user_id, $badge_id ){
        $userMeritBadge = $this->buildUserBadge( $user_id, $badge_id );
        
        if ( !$userMeritBadge->getCurrVal() )
            return false;
        
        return true;
    }

    private function setProperValueUserBadge( $user_id, $badge_id ){

        $userMeritBadge = $this->buildUserBadge( $user_id, $badge_id );
        
        if ( $userMeritBadge->getCurrVal() < $userMeritBadge->getNextVal() )
            return false;
            
        //the badge has a wrong level  
            
        $badgeLevel = $this->getProperBadgeLevel($badge_id, $userMeritBadge->getCurrVal() );
          
        $query = "UPDATE badge_user 
                SET level_id = :1, level_date= :2, prev_val = next_val, next_val = :3  
                WHERE user_id = :4 and badge_id= :5";
        $this->db->multiVariableQuery( $query,  
                $badgeLevel->getLevel(), date("Y-m-d"), $badgeLevel->getThreshold(),
                $user_id, $badge_id );                            
        
        return true;
    }
    
    private function isToUpdate( $cache_id, $oneMeritBadge ){
        $query = $oneMeritBadge->getIsToUpdateQuery();
        if ($query == "")
            return false;
            
        $stm = $this->db->multiVariableQuery( $query, $cache_id );
        if( !$this->db->rowCount($stm) )
            return false;
        
        $rec = $this->db->dbResultFetch($stm);
        if ( !$rec )
            return false;
            
        $value = reset($rec);
        if (!$value)
            return false;
                
        return true;
    }
    
    
    private function calcCurrValForUserBadge($user_id, $oneMeritBadge){
        $query = $oneMeritBadge->getCurrValQuery();
        if ( $query == "" )
            return 0;

        $stm = $this->db->multiVariableQuery( $query, $user_id );
        if( !$this->db->rowCount($stm) )
            return false;
            
        $rec = $this->db->dbResultFetch($stm);
        if ( !$rec )
            return 0;
        
        $value = reset($rec);
        if (!$value)
            return 0;
        
        return $value;
    }
    
    //////////////////////////////////////////////////////////////////////
    // private functions - query
    //////////////////////////////////////////////////////////////////////
    
    private function getMeritBadgeQuery( $condition ){
        $query="SELECT
                badges.id badges_id,
                badges.is_to_update_query badges_is_to_update_query,
                badges.curr_val_query badges_curr_val_query
                
                FROM badges ". $condition;
        
        return $query;
    }
    
    
    private function getUserBadgeQuery( $condition )
    {
        $query="SELECT
                badges.name badges_name,
                badges.short_description badges_short_description,
                badges.description badges_description,
                lvl.amount badges_levels,
                badges.picture badges_picture,
                badges.cfg_period_threshold badges_cfg_period_threshold,
                badges.graphic_author badges_graphic_author,
                badges.description_author badges_description_author,
                badges.attendant badges_attendant,
    
                badge_levels.picture badge_levels_picture,
                badge_levels.level badge_levels_level,
                badge_levels.name badge_levels_name,

                badge_user.id badge_user_id,
                badge_user.badge_id badge_user_badge_id,
                badge_user.level_id badge_user_level_id,
                badge_user.curr_val badge_user_curr_val,
                badge_user.next_val badge_user_next_val,
                badge_user.description badge_user_description,
                
                badge_categories.name badge_categories_name
    
                FROM
                badge_user
                join badge_levels on badge_user.badge_id = badge_levels.badge_id and badge_user.level_id = badge_levels.level
                join badges on badge_user.badge_id = badges.id
                join badge_categories on badges.category_id = badge_categories.id
                join
                (
                    SELECT badge_id, count(*) amount FROM badge_levels GROUP BY 1
                ) as lvl on lvl.badge_id = badges.id "
                .$condition.
                " ORDER BY badges.sequence";
    
        return $query;
    }
    
    
    private function getLevelsQuery( $condition ){
        
        $query= "SELECT 
                badge_levels.level badge_levels_level,
                badge_levels.name badge_levels_name,
                badge_levels.threshold badge_levels_threshold, 
                
                gain.counter badge_levels_gain_counter,
                gain.last_date badge_levels_gain_last_date
                
                FROM 
                badge_levels
                left outer join
                (
                    SELECT level_id, count(*) counter, max(level_date) last_date  
                    FROM badge_user 
                    WHERE badge_id=:1
                    GROUP BY 1
                ) as gain on gain.level_id = badge_levels.level "
                .$condition.                        
                " ORDER BY badge_levels.level";
        
        return $query;
    }
 
    private function getArrayUserQuery(){
        $query =" SELECT
                badge_user.level_id badge_user_level_id, 
                badge_user.user_id badge_user_user_id,
                badge_user.level_date badge_user_level_date,
                UNIX_TIMESTAMP(badge_user.level_date) badge_user_level_date_ts,
                badge_user.curr_val badge_user_curr_val,
                user.username badge_user_user_name
        
                FROM badge_user
                JOIN user on badge_user.user_id = user.user_id
                WHERE badge_id= :1
                ORDER BY badge_user.level_id, badge_user.curr_val desc";
        
        return $query;
    }

    
    private function getArrayUserCategoriesQuery(){
        $query = "SELECT distinct 
                badge_categories.id badge_categories_id, 
                badge_categories.name badge_categories_name
                
                FROM badge_user
                JOIN badges on badge_user.badge_id = badges.id
                JOIN badge_categories on badges.category_id = badge_categories.id
                
                WHERE user_id=:1
                ORDER BY badge_categories.sequence";
    
        return $query;
    }
    
}
?>
