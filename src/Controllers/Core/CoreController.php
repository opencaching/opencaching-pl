<?php

namespace src\Controllers\Core;

use src\Models\ApplicationContainer;
use src\Models\OcConfig\OcConfig;
use src\Models\User\User;

/**
 * Base controller class - root of all controllers
 */
abstract class CoreController
{
    protected ?User $loggedUser;

    protected OcConfig $ocConfig;

    public function __construct()
    {
        $this->loggedUser = ApplicationContainer::GetAuthorizedUser();
        $this->ocConfig = OcConfig::instance();

        // there is no DB access init - DB operations should be performed in models/objects
    }

    protected function isUserLogged(): bool
    {
        return ! is_null($this->loggedUser);
    }

    /**
     * Every ctrl should have index method
     * which is called by router as a default action
     */
    abstract public function index();

    /**
     * This method is called by router to be sure that given action is allowed
     * to be called by router (it is possible that ctrl has public method which
     * shouldn't be accessible on request).
     *
     * @param string $actionName
     *                           - method which router will call
     * @return bool - TRUE if given method can be call from router
     */
    abstract public function isCallableFromRouter(string $actionName): bool;
}
