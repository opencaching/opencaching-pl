<?php

namespace lib\Objects\MeritBadge;

class LevelMeritBadge{
    
    private $name;
    private $level;
    private $picture;
    private $threshold;
    
    private $gain_counter;
    private $gain_last_date;
    
    //////////////////////////////////////////////////////////////////////
    // getField functions
    //////////////////////////////////////////////////////////////////////
    
    public function getName(){
        return $this->name;
    }
    
    public function getLevel(){
        return $this->level;
    }
    
    public function getPicture(){
        return $this->picture;
    }
 
    public function getThreshold(){
        return $this->threshold;
    }

    public function getGainCounter(){
        return $this->gain_counter;
    }

    public function getGainLastDate(){
        return $this->gain_last_date;
    }
    
    public function getLevelName(){
        if ( $this->getName() != "" )
            return $this->getName();
    
        return $this->getLevel();
    }
    
    
    //////////////////////////////////////////////////////////////////////
    // public functions
    //////////////////////////////////////////////////////////////////////
    
    public function setFromRow( $rec ){
        
        $this->name = $rec['badge_levels_name'];
        $this->level = $rec['badge_levels_level'];
        
        if ( isset($rec['badge_levels_picture']) )
            $this->picture = $rec['badge_levels_picture'];
        
        if ( isset($rec['badge_levels_threshold'])  )
            $this->threshold = $rec['badge_levels_threshold'];
        
        if ( isset($rec['badge_levels_gain_counter'])  )
            $this->gain_counter = $rec['badge_levels_gain_counter'];
            
        if ( isset($rec['badge_levels_gain_last_date'])  )
            $this->gain_last_date = $rec['badge_levels_gain_last_date'];
    }
        
}


?>