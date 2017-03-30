<?php

namespace lib\Objects\MeritBadge;

use Utils\Database\OcDb;

class MeritBadge
{
    const COLOR_NUMBER = 10;
    const PROGRESIVE_BAR_SIZE = 16;
    const THE_HIGHEST_LEVEL = 999999;
    
    static private $_colors = array("#89af2b",//0
                                    "#af992b",
                                    "#af5e2b",
                                    "#af2b2b",
                                    "#af2b7e",
                                    "#962baf",
                                    "#2b5caf",
                                    "#8E6343", /*"#2b90af"*/
                                    "#A5A5A5",  /*"#2baf99"*/
                                    "#FFCC00" /*"#3daf2c"*/);//9
    
    private $id;
    private $name;
    private $short_description;
    private $description;
    private $levels; //amount of levles - ToDo - to remove ?
    private $cfg_period_threshold;
    private $picture;
    
    private $graphic_author;
    private $description_author;
    private $attendant;
    
    //queries
    private $is_to_update_query;
    private $curr_val_query;
    
    
    //////////////////////////////////////////////////////////////////////
    // getField functions
    //////////////////////////////////////////////////////////////////////

    public function getId(){
        return $this->id;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getShortDescription(){
        return $this->short_description;
    }
    
    public function getDescription(){
        return $this->description;
    }
    
    public function getLevels(){ //ToDo - zmienic na count ?
        return $this->levels;
    }
    
    public function getCfgPeriodThreshold(){
        return $this->cfg_period_threshold;
    }
    
    public function getPicture(){
        return $this->picture;
    }
    
    public function getGraphicAuthor(){
        return $this->graphic_author;
    }
    
    public function getDescriptionAuthor(){
        return $this->description_author;
    }
    
    public function getAttendant(){
        return $this->attendant;
    }
    
    public function getIsToUpdateQuery(){
        return $this->is_to_update_query;
    }
    
    public function getCurrValQuery(){
        return $this->curr_val_query;
    }
    
    public function whoPrepared(){
        
        $text = 
        tr('merit_badge_graphic_author')." <b>". $this->getGraphicAuthor() . "</b>, ".
        tr('merit_badge_description_author'). " <b>". $this->getDescriptionAuthor() . "</b>, ".
        tr('merit_badge_attendant'). " <b>". $this->getAttendant() . "</b>";
        
        return $text;
    }
    
    
    
    public function setFromRow( $rec ){
        
        if ( isset($rec['badges_id']) )
            $this->id = $rec[ 'badges_id' ];
        
        if ( isset($rec['badges_name']) )
            $this->name = $rec[ 'badges_name' ];
        
        if ( isset($rec['badges_short_description']) )
            $this->short_description = $rec[ 'badges_short_description' ];
        
        if ( isset($rec['badges_description']) )
            $this->description = $rec[ 'badges_description' ];
        
        if ( isset($rec['badges_levels']) )
            $this->levels = $rec[ 'badges_levels' ];
        
        if ( isset($rec['badges_cfg_period_threshold']) )
            $this->cfg_period_threshold = $rec[ 'badges_cfg_period_threshold' ];
        
        if ( isset($rec['badges_picture']) )
            $this->picture = $rec[ 'badges_picture' ];
        
        if ( isset($rec['badges_is_to_update_query']) )
            $this->is_to_update_query = $rec[ 'badges_is_to_update_query' ];
        
        if ( isset($rec['badges_curr_val_query']) )
            $this->curr_val_query = $rec[ 'badges_curr_val_query' ];
        
        if ( isset($rec['badges_graphic_author']) )
            $this->graphic_author= $rec[ 'badges_graphic_author' ];
                
        if ( isset($rec['badges_description_author']) )
            $this->description_author= $rec[ 'badges_description_author' ];
                
        if ( isset($rec['badges_attendant']) )
            $this->attendant= $rec[ 'badges_attendant' ];
    }
    
    //////////////////////////////////////////////////////////////////////
    // Static functions
    //////////////////////////////////////////////////////////////////////
    
    public static function getBarSize( $level, $maxLevel){
        $size = round(($level*self::PROGRESIVE_BAR_SIZE)/$maxLevel);
        
        if($size == 0 ) $size = 1;
        if($size > self::PROGRESIVE_BAR_SIZE ) $size = self::PROGRESIVE_BAR_SIZE;
        
        return $size;
    }
    
    
    public static function getColor( $level, $maxLevel){
        $idx = round((($level+1)*self::COLOR_NUMBER)/($maxLevel));
        if($idx > (self::COLOR_NUMBER) ) $idx = self::COLOR_NUMBER;
        return self::$_colors[ $idx-1 ];
    }
    
    
    public static function getProgressBarValueMax($value){
        if ($value != self::THE_HIGHEST_LEVEL)
            return $value;
        
        return 1;
    }
    
    public static function prepareShortDescription($desc, $threshold ){
        if ( $threshold == self::THE_HIGHEST_LEVEL)
            return "<span style='font-weight:bold;color:". self::getColor( self::COLOR_NUMBER, self::COLOR_NUMBER) .";'>".tr('merit_badge_gain_max_level' )."</span>";
    
            $desc = str_replace( "{userThreshold}", $threshold, $desc);
            return $desc;
    }
    
    //it is used functions in badge.php to prepare the table with levels 
    public static function preparePeriodOrThreshold($first, $second, $PeriodOrThreshold){
        $out =  $first;
        
        if ($PeriodOrThreshold == "P"){
            if ($second != self::THE_HIGHEST_LEVEL)
                $out .= " - " . ($second-1);
            else
                $out .= "";
        }
        
        return $out;
    }
    
    public static function prepareTextThreshold($threshold){
        if ( $threshold != self::THE_HIGHEST_LEVEL)
            return $threshold;
        
        return "...";
    }
    
    
    //# select () as value from ...  # - one value
    //@ select () as cache_id, () as cache_name from ...  @ - a list of caches
    public static function sqlTextTransform($text){
    
        global $config;
        
        $db = OcDb::instance();
    
        preg_match_all("/#(.*)#/", $text, $sqlOut); //one value
        foreach( $sqlOut[1] as $sql ){
            $stmt = $db->simpleQuery($sql);
            $rec = $db->dbResultFetch($stmt);
            $text = str_replace('#'.$sql.'#', number_format( $rec["value"], 0, $config['numberFormatDecPoint'], $config['numberFormatThousandsSep']), $text);
    
        }
    
        preg_match_all("/@(.*)@/", $text, $sqlOut); //a list of caches
        foreach( $sqlOut[1] as $sql ){
            $cache_list = "<ul>";
    
            $stmt = $db->simpleQuery($sql);
            for($i = 0; $i < $db->rowCount($stmt); $i++ ){
                $pattern = "<li> <a href='viewcache.php?cacheid={cache_id}'>{cache_name}</a></li>";
                $rec = $db->dbResultFetch($stmt);
                $pattern = str_replace( "{cache_name}", $rec["cache_name"], $pattern);
                $cache_list .= str_replace( "{cache_id}", $rec["cache_id"], $pattern);
                 
            }
            
            $cache_list .= "</ul>";
                    
            $text = str_replace('@'.$sql.'@', $cache_list, $text);
        }
    
        return $text;
    }
    
}
