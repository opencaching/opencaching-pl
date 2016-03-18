<?php

namespace lib\Objects;

use lib\Objects\User\User;
use lib\Objects\OcConfig\OcConfig;

final class ApplicationContainer
{

    private static $applicationContainer = null;

    private $loggedUser = false;
    private $ocConfig;

    /**
     *
     * @var \dataBase $db
     */
    public $db;

    private function __construct()
    {
        $this->ocConfig = OcConfig::instance();
        $this->db = \lib\Database\DataBaseSingleton::Instance();
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

