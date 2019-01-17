<?php

namespace lib\Objects;

use lib\Objects\User\User;
use lib\Objects\OcConfig\OcConfig;
use Utils\Database\OcDb;

final class ApplicationContainer
{
    /** @var ApplicationContainer */
    private static $applicationContainer = null;

    /** ocNode identifier loaded form local site-settings: pl|ro|nl|uk|... */
    private $ocNode = null;

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
     * @return \lib\Objects\User\User
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


    public static function GetOcNode()
    {
        return self::Instance()->ocNode;
    }

    public static function SetOcNode($ocNode)
    {
        self::Instance()->ocNode = $ocNode;
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

