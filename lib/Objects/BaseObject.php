<?php
namespace lib\Objects;

use Utils\Database\OcDb;

abstract class BaseObject
{

    protected $db;
    protected $dataLoaded = false; //are data loaded to this object


    public function __construct(){
        $this->db = self::Db();
    }

    public function isDataLoaded()
    {
        return $this->dataLoaded;
    }

    protected static function Db()
    {
        return OcDb::instance();
    }
}
