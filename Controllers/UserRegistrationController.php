<?php
namespace Controllers;

use Utils\Uri\Uri;
use Utils\Uri\SimpleRouter;

/**
 * This controller is used in new user registration process
 */
class UserRegistrationController extends BaseController
{
    public function __construct(){
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return TRUE;
    }

    public function index()
    {
        if($this->isUserLogged()){
            return $this->alreadyRegistered();
        }

        $this->view->loadJQuery();
        $this->view->setTemplate('userRegistration/userRegistration');
        // local css
        $this->view->addLocalCss( Uri::getLinkWithModificationTime(
            '/tpl/stdstyle/userRegistration/userRegistration.css'));


        $this->view->buildView();
    }


    private function alreadyRegistered()
    {
        $this->view->setTemplate('userRegistration/alreadyRegistered');


        $this->view->buildView();
    }

}

