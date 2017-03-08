<?php

namespace Controllers;

use lib\Objects\ApplicationContainer;
use lib\Objects\User\User;
use lib\Objects\OcConfig\OcConfig;
use Utils\View\View;
use Utils\Uri\Uri;

require_once('./lib/common.inc.php');

abstract class BaseController
{
    /** @var View $view */
    protected $view = null;

    /** @var ApplicationContainer $applicationContainer */
    private $applicationContainer = null;

    /** @var User */
    protected $loggedUser = null;

    /** @var OcConfig $ocConfig */
    protected $ocConfig = null;

    protected function __construct()
    {
        $this->view = tpl_getView();

        $this->applicationContainer = \lib\Objects\ApplicationContainer::Instance();
        $this->loggedUser = $this->applicationContainer->getLoggedUser();
        $this->ocConfig = $this->applicationContainer->getOcConfig();

        // there is no DB access init - DB operations should be performed in models/objects
    }

    abstract public function index(); //every Controller should have index method whoch should be call to handle requests

    protected function redirectToLoginPage()
    {
        $this->view->redirect(Uri::setOrReplaceParamValue('target', Uri::getCurrentUri()), 'login.php');
    }

}