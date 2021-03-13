<?php

namespace src\Models;

use src\Models\User\User;

final class ApplicationContainer
{
    /** @var User */
    private $loggedUser = null;

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
     * Return authorized user object or null if user is not authorized
     *
     * @return \src\Models\User\User
     */
    public static function GetAuthorizedUser(): ?User
    {
        return self::Instance()->loggedUser;
    }

    public static function SetAuthorizedUser(User $loggedUser=null)
    {
        self::Instance()->loggedUser = $loggedUser;
    }
}
