<?php

namespace Controllers;

require_once('./lib/common.inc.php');

abstract class BaseController
{

    protected $view = null;

    private $applicationContainer = null;
    protected $loggedUser = null;
    protected $ocConfig = null;

    protected function __construct()
    {
        $this->view = tpl_getView();

        $this->applicationContainer = \lib\Objects\ApplicationContainer::Instance();
        $this->loggedUser = $this->applicationContainer->getLoggedUser();
        $this->ocConfig = $applicationContainer->getOcConfig();

        // there is no DB access init - DB operations should be performed in models/objects
    }

    abstract public function index(); //every Controller should have index method whoch should be call to handle requests





}