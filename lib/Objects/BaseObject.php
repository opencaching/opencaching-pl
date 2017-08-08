<?php
namespace lib\Objects;

use Utils\Database\OcDb;

abstract class BaseObject
{

    protected $db;
    protected $dataLoaded = false; //are data loaded to this object


    public function __construct(){
        $this->db = self::db();
    }

    public function isDataLoaded() //this method will be removed!
    {
        return $this->dataLoaded;
    }

    public static function db()
    {
        return OcDb::instance();
    }

    public static function getCurrentUser(){
        return ApplicationContainer::Instance()->getLoggedUser();
    }

}
