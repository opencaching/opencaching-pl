<?php

namespace src\Models;

use src\Models\User\User;
use src\Models\OcConfig\OcConfig;
use src\Utils\Database\OcDb;

final class ApplicationContainer
{
    /** @var ApplicationContainer */
    private static $applicationContainer = null;

    /** @var User */
    private $loggedUser = null;

    private $ocConfig;

    public $db;

    private function __construct()
    {
        $this->ocConfig = OcConfig::instance();
        $this->db = OcDb::instance();
    }

    /**
     * @return ApplicationContainer
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new ApplicationContainer();
        }
        return $inst;
    }

    /**
     *
     * @return User
     */
    public function getLoggedUser()
    {
        return $this->loggedUser;
    }

    /**
     * Return authorized user object or null
     *
     * @return \src\Models\User\User
     */
    public static function GetAuthorizedUser(){
        return self::Instance()->getLoggedUser();
    }

    public function setLoggedUser(User $loggedUser)
    {
        $this->loggedUser = $loggedUser;
        return $this;
    }

    public static function SetAuthorizedUser(User $loggedUser=null)
    {
        self::Instance()->loggedUser = $loggedUser;
    }

    /**
     *
     * @return OcConfig
     */
    public function getOcConfig()
    {
        return $this->ocConfig;
    }

}
