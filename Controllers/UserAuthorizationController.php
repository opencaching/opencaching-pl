<?php
namespace Controllers;

use lib\Objects\User\UserAuthorization;
use Utils\Uri\Uri;
use Utils\Uri\SimpleRouter;

class UserAuthorizationController extends BaseController
{

    const DEFAULT_TARGET = '/index.php';

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return TRUE;
    }

    public function index()
    {
        $this->displayLoginPage();
    }

    public function login()
    {
        if ($this->isUserLogged()) {
            // alredy logged in...
            $this->redirectToAuthCookieVerify();
            return;
        }

        list($userEmail, $userPassword) = $this->getCredentialsData();

        if ($userEmail && $userPassword) {

            switch (UserAuthorization::checkCredentials($userEmail, $userPassword)) {
                case UserAuthorization::LOGIN_OK:
                    $this->redirectToAuthCookieVerify();
                    return;

                case UserAuthorization::LOGIN_TOOMUCHLOGINS:
                    $error = tr('loginForm_tooManyTries');
                    break;
                case UserAuthorization::LOGIN_USERNOTACTIVE:
                    $error = tr('loginForm_userNotActive');
                    break;
                case UserAuthorization::LOGIN_BADUSERPW:
                default:
                    $error = tr('loginForm_badCredentials');
            }

        } else {
            $error = tr('loginForm_badCredentials');
        }

        $this->displayLoginPage($error);
    }

    private function displayLoginPage($error=null)
    {
        if ($this->isUserLogged()) {
            // alredy logged in...
            $this->redirectToAuthCookieVerify();
            return;
        }

        $this->view->setTemplate('userAuth/loginPage');
        $this->view->loadJQuery();
        $this->view->addLocalCss(
            Uri::getLinkWithModificationTime('/tpl/stdstyle/userAuth/userAuth.css'));
        $this->view->setVar('prevEmail', (isset($_POST['email']) ? $_POST['email'] : ''));
        $this->view->setVar('prevPassword', (isset($_POST['password']) ? $_POST['password'] : ''));
        $this->view->setVar('target', $this->getRedirectTarget());
        $this->view->setVar('errorMsg', $error);

        $this->view->buildView();
    }


    private function getCredentialsData()
    {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            return [$_POST['email'], $_POST['password']];
        } else {
            return [null, null];
        }
    }

    public function verifyAuthCookie()
    {

        if (UserAuthorization::isAuthCookiePresent()) {
            // cookie OK, redirect to target...
            $this->view->redirect( urldecode($this->getRedirectTarget()) );
            exit;
        } else {
            // display message if cookie can't be set in browser
            $this->displayLoginPage(tr('loginForm_cantSetCookie'));
        }
    }

    public function logout()
    {
        UserAuthorization::logout();

        $target = urldecode($this->getRedirectTarget());
        $this->view->redirect($target);
        exit;

    }

    private function getRedirectTarget()
    {
        if (isset($_REQUEST['target'])) {
            return $_REQUEST['target'];
        } else {
            return urlencode(self::DEFAULT_TARGET);
        }
    }

    private function redirectToAuthCookieVerify()
    {
        $uri = SimpleRouter::getLink('UserAuthorization', 'verifyAuthCookie');
        $uri = Uri::setOrReplaceParamValue('target', $this->getRedirectTarget(), $uri);
        $this->view->redirect($uri);
        $this->view->buildView();
        exit;
    }

}