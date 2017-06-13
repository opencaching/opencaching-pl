<?php

namespace lib\Objects\MeritBadge;

class CategoryMeritBadge{

    private $id;
    private $name;


    //////////////////////////////////////////////////////////////////////
    // getField functions
    //////////////////////////////////////////////////////////////////////

    public function getId(){
        return $this->id;
    }


    public function getName(){
        return $this->name;
    }


    //////////////////////////////////////////////////////////////////////
    // public functions
    //////////////////////////////////////////////////////////////////////

    public function setFromRow( $rec ){

        if (isset($rec['badge_categories_id']))
            $this->id = $rec['badge_categories_id'];

        if (isset($rec['badge_categories_name']))
            $this->name = $rec['badge_categories_name'];
    }

}


?>