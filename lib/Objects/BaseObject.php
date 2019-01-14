<?php
namespace lib\Objects;

use Utils\Database\OcDb;
use okapi\Facade;
use lib\Objects\OcConfig\OcConfig;

abstract class BaseObject
{
    /** @var OcDb */
    protected $db;

    protected $dataLoaded = false; //are data loaded to this object


    public function __construct(){
        $this->db = self::db();
    }

    public function isDataLoaded() //this method will be removed!
    {
        return $this->dataLoaded;
    }

    /**
     * @return \Utils\Database\OcDb
     */
    protected static function db()
    {
        //TODO: if PDO error!
        return OcDb::instance();
    }

    protected static function OcConfig()
    {
        return OcConfig::instance();
    }

    protected static function getCurrentUser(){
        return ApplicationContainer::Instance()->getLoggedUser();
    }

    protected static function callOkapi($service, $params){

        /** @var \lib\Objects\User\User */
        $user = self::getCurrentUser();

        // IMPORTANT: Only the logged-in user's ID may be passed to Facade,
        // not any other user's ID!

        $userId = is_null($user) ? null : $user->getUserId();

        $okapiResp = Facade::service_call(
            $service, $userId, $params);

        return $okapiResp;
    }

    public function prepareForSerialization()
    {
        $this->db = null;
    }

    public function restoreAfterSerialization()
    {
        $this->db = self::db();
    }

}
