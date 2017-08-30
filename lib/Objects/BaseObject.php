<?php
namespace lib\Objects;

use Utils\Database\OcDb;

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

        require_once( __DIR__.'/../../okapi/facade.php');

        /** @var \lib\Objects\User\User */
        $user = self::getCurrentUser();

        $userId = is_null($user) ? null : $user->getUserId();

        \okapi\OkapiErrorHandler::disable();

        $okapiResp = \okapi\Facade::service_call(
            $service, $userId, $params);

        \okapi\OkapiErrorHandler::reenable();

        return $okapiResp;
    }

}
