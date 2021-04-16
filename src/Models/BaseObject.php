<?php
namespace src\Models;

use src\Utils\Database\OcDb;
use okapi\Facade;
use src\Models\OcConfig\OcConfig;

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
     * @return \src\Utils\Database\OcDb
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
        return ApplicationContainer::GetAuthorizedUser();
    }

    protected static function callOkapi($service, $params){

        /** @var \src\Models\User\User */
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
