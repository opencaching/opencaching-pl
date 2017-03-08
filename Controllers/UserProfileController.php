<?php
namespace Controllers;


use lib\Objects\User\User;

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


    }

    private function loadRequestedUser()
    {
        if (isset($_REQUEST['userid'])) {
            return User::fromUserIdFactory($_REQUEST['userid']);
        }
        return null;
    }

}


