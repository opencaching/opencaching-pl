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

    /**
     * Check whether a user is logged in.
     *
     * @return bool
     */
    protected function isUserLogged(): bool
    {
        return ! is_null($this->loggedUser);
    }

    /**
     * Read an integer query parameter from $_GET, with a default fallback.
     *
     * @param string $name
     * @param int $default
     * @return int
     */
    protected function getIntegerQueryParameter(string $name, int $default): int
    {
        if (! isset($_GET[$name]) || ! is_scalar($_GET[$name])) {
            return $default;
        }

        $value = filter_var((string) $_GET[$name], FILTER_VALIDATE_INT);

        return $value === false ? $default : max(0, $value);
    }
}