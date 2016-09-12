<?php

namespace lib\Objects;

use lib\Objects\User\User;
use lib\Objects\OcConfig\OcConfig;
use Utils\Database\OcDb;

final class ApplicationContainer
{

    private static $applicationContainer = null;

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

    public function setLoggedUser(User $loggedUser)
    {
        $this->loggedUser = $loggedUser;
        return $this;
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

