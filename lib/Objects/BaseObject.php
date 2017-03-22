<?php
namespace lib\Objects;

use Utils\Database\OcDb;

abstract class BaseObject
{

    protected $db;
    protected $dataLoaded = false; //are data loaded to this object


    public function __construct(){
        $this->db = OcDb::instance();
    }

    protected static function Db()
    {
        return OcDb::instance();
    }

    public function isDataLoaded()
    {
        return $this->dataLoaded;
    }
}
