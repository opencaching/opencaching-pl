<?php
namespace lib\Objects;

use Utils\Database\OcDb;
use okapi\Facade;

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
    public static function db()
    {
        return OcDb::instance();
    }

    public static function getCurrentUser(){
        return ApplicationContainer::Instance()->getLoggedUser();
    }

    protected static function callOkapi($service, $params){


        /** @var \lib\Objects\User\User */
        $user = self::getCurrentUser();

        $userId = is_null($user) ? null : $user->getUserId();

        Facade::disable_error_handling();

        $okapiResp = Facade::service_call(
            $service, $userId, $params);

        Facade::reenable_error_handling();

        return $okapiResp;
    }

}
