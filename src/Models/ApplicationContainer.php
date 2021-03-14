<?php

namespace src\Models;

use src\Models\User\User;
use src\Utils\Debug\ErrorHandler;

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
     * This function should be called right after class loader
     */
    public static function ocBaseInit()
    {
        $rootPath = __DIR__.'/../../';

        // Install error handlers
        ErrorHandler::install();

        session_start();
        ob_start();

        // reset server encondig - to be sure we use UTF-8
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');
        mb_language('uni');
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
