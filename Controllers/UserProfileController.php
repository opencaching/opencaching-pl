<?php
namespace Controllers;


use lib\Objects\User\User;
use Utils\Uri\Uri;

class UserProfileController extends BaseController
{

    /** @var User $requestedUser */
    private $requestedUser;

    public function __construct()
    {
        parent::__construct();
        $this->requestedUser = $this->loadRequestedUser();
    }

    public function index()
    {
        // there is nothing here yet
    }

    public function mailTo()
    {
        if(!$this->loggedUser){
            // this view is only for authorized user
            $this->redirectToLoginPage();
        }

        tpl_set_tplname('userProfile/mailto');

        if(!$this->requestedUser){
            // send mail to unknow user ?!
            //TODO:
        }


        $this->view->setVar('requestedUser', $this->requestedUser);
        $this->view->setVar('mailto_css',
            Uri::getLinkWithModificationTime('tpl/stdstyle/userProfile/mailto.css'));


        $this->view->setVar('messagePresent', false);




        tpl_BuildTemplate();
    }

    private function loadRequestedUser()
    {
        if (isset($_REQUEST['userid'])) {
            return User::fromUserIdFactory($_REQUEST['userid']);
        }
        return null;
    }
}


