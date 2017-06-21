<?php

namespace lib\Objects\MeritBadge;

class PositionMeritBadge{
    
    private $id; //cache
    private $name; //cache
    private $type; //cache
    private $gain_date;
    private $owner_id;
    private $owner_name;
    
    //////////////////////////////////////////////////////////////////////
    // getField functions
    //////////////////////////////////////////////////////////////////////

    public function getId(){
        return $this->id;
    }
    
    public function getName(){
        return $this->name;
    }

    public function getType(){
        return $this->type;
    }
    
    
    public function getGainDate(){
        return $this->gain_date;
    }

    public function getOwnerId(){
        return $this->owner_id;
    }
    
    public function getOwnerName(){
        return $this->owner_name;
    }
    
    
    //////////////////////////////////////////////////////////////////////
    // public functions
    //////////////////////////////////////////////////////////////////////
    
    public function setFromRow( $rec ){
        
        if ( isset($rec['badge_position_id'])  )
            $this->id = $rec['badge_position_id'];
        
        if ( isset($rec['badge_position_name'])  )
            $this->name = $rec['badge_position_name'];

        if ( isset($rec['badge_position_type'])  )
            $this->type = $rec['badge_position_type'];
                            
        if ( isset($rec['badge_position_gain_date'])  )
            $this->gain_date = $rec['badge_position_gain_date'];
        
        if ( isset($rec['badge_position_owner_id'])  )
            $this->owner_id = $rec['badge_position_owner_id'];

        if ( isset($rec['badge_position_owner_name'])  )
            $this->owner_name = $rec['badge_position_owner_name'];
                
    }
        
}


?>