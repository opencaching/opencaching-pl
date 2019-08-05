<?php
namespace src\Controllers;

use Exception;
use RuntimeException;
use src\Models\User\PasswordManager;
use src\Models\User\User;
use src\Models\User\UserAuthorization;
use src\Utils\Text\Validator;
use src\Utils\Uri\SimpleRouter;
use src\Utils\Uri\Uri;

class UserAuthorizationController extends BaseController
{

    const DEFAULT_TARGET = '/';

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return true;
    }

    public function index()
    {
        $this->displayLoginPage();
    }

    public function login()
    {
        if ($this->isUserLogged()) {
            // already logged in...
            $this->redirectToAuthCookieVerify();
            return;
        }

        list ($userEmail, $userPassword) = $this->getCredentialsData();

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
        $this->view->loadJQuery();
        $this->displayLoginPage($error);
    }

    /**
     * Displays form to send code for password change
     * and supports form submit
     */
    public function newPassword()
    {
        $errorMsg = '';
        $this->view->setTemplate('userAuth/newPassword');
        if (isset($_POST['submitNewPw'])) {
            $errorMsg = $this->newPasswordStage2();
            if (is_null($errorMsg)) {
                $this->showSuccessMessage(tr('newpw_info_send'), tr('newpw_title'));
            }
        }
        $username = ($this->isUserLogged()) ? $this->loggedUser->getUserName() : '';
        $this->view->setVar('username', $username);
        $this->view->setVar('errorMsg', $errorMsg);
        $this->view->loadJQuery();
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/userAuth/userAuth.css'));
        $this->view->buildView();
    }

    /**
     * Check $_POST param and send new password code to $_POST['userName']
     * $_POST['userName'] may be username or e-mail
     * Returns translated error string or null on success
     *
     * @return string|NULL
     */
    private function newPasswordStage2()
    {
        if (! isset($_POST['userName'])) { // Check POST params
            return tr('page_error');
        }
        $username = strip_tags(trim($_POST['userName']));
        if (($user = User::fromUsernameFactory($username, User::AUTH_COLUMNS)) || ($user = User::fromEmailFactory($username, User::AUTH_COLUMNS))) {
            if (! $user->isActive()) {
                return tr('newpw_err_notact');
            }
            UserAuthorization::sendPwCode($user);
            return null;
        } else {
            return tr('newpw_err_notusr');
        }
    }

    /**
     * Displays form to change password and supports pwd change
     *
     * @param string $usr
     *            - urlencoded username
     * @param string $code
     *            - new password code (send by e-mail)
     * @throws Exception
     */
    public function newPasswordInput($usr = null, $code = null)
    {
        $errorMsg = '';

        $this->view->setRedirectToMainPageAfterLogin(true);

        try {
            $user = self::checkUserAndCode($usr, $code);
        } catch (RuntimeException $e){
            $this->showErrorMessage(tr("userAuth_codeCheckError", [$e->getMessage()]));
            return;
        }

        if (isset($_POST['submitNewPw'])) {
            if (! isset($_POST['password'])) {
                $this->showErrorMessage(tr('security_error'));
            }
            $password = trim($_POST['password']);
            if (Validator::checkStrength($password)) {
                $pm = new PasswordManager($user->getUserId());
                $pm->change($password);
                UserAuthorization::removePwCode($user);
                $this->showSuccessMessage(tr('newpw_info_changed'), tr('newpw_title'));
            } else {
                $errorMsg = tr('password_weak'); // It should never happen, because JS script doesn't allow to send weak password
            }
        }
        $this->view->setTemplate('userAuth/newPasswordInput');
        $this->view->setVar('returnUrl', SimpleRouter::getLink('UserAuthorization', 'newPasswordInput', [
            $usr,
            $code
        ]));
        $this->view->setVar('errorMsg', $errorMsg);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/userAuth/userAuth.css'));
        $this->view->addLocalJs(Uri::getLinkWithModificationTime('/views/userAuth/newPassword.js'), true, true);
        $this->view->loadJQuery();
        $this->view->buildView();
    }

    /**
     * Security check of parameters given.
     * This method checks if:
     * - params are not null
     * - $usr is real username
     * - user is active
     * - $code is valid and not expired
     * If all above are true - returns User object. Null is returned otherwise.
     * If code is not valid or expired - removes code from DB for security
     * reason (anti brute-force strategy)
     *
     * @param string $usr
     *            - urlencoded username
     * @param string $code
     *            - new password code
     * @return NULL|User
     */
    private function checkUserAndCode($usr, $code)
    {
        if (is_null($usr) || is_null($code)) {
            throw new RuntimeException("Wrong parameters");
        }
        $usr = urldecode($usr);
        if (is_null($user = User::fromUsernameFactory($usr))) {
            throw new RuntimeException("There is no such user!");
        }
        if (! $user->isActive()) {
            throw new RuntimeException("Inactive user account!");

        }
        if (! UserAuthorization::checkPwCode($user, $code)) {
            UserAuthorization::removePwCode($user);
            throw new RuntimeException("Wrong authorization code!");
        }
        return $user;
    }

    /**
     * Shows simple security alert based on callout
     *
     * @param string $message
     * @param string $header
     */
    private function showErrorMessage($message, $header = null)
    {
        $this->view->setTemplate('userAuth/simpleInfoError');
        $this->view->setVar('notLogged', is_null($this->loggedUser));
        $this->view->setVar('message', $message);
        $this->view->setVar('header', (is_null($header)) ? tr('errtpl04') : $header);
        $this->view->buildView();
        exit();
    }

    /**
     * Shows simple success message based on callout
     *
     * @param string $message
     * @param string $header
     */
    private function showSuccessMessage($message, $header)
    {
        $this->view->setTemplate('userAuth/simpleInfoOk');
        $this->view->setVar('notLogged', is_null($this->loggedUser));
        $this->view->setVar('message', $message);
        $this->view->setVar('header', $header);
        $this->view->buildView();
        exit();
    }

    private function displayLoginPage($error = null)
    {
        if ($this->isUserLogged()) {
            // alredy logged in...
            $this->redirectToAuthCookieVerify();
            return;
        }

        $this->view->setTemplate('userAuth/loginPage');
        $this->view->loadJQuery();
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/userAuth/userAuth.css'));
        $this->view->setVar('prevEmail', (isset($_POST['email']) ? $_POST['email'] : ''));
        $this->view->setVar('target', $this->getRedirectTarget());
        $this->view->setVar('errorMsg', $error);

        $this->view->buildView();
    }

    private function getCredentialsData()
    {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            return [
                $_POST['email'],
                $_POST['password']
            ];
        } else {
            return [
                null,
                null
            ];
        }
    }

    public function verifyAuthCookie()
    {
        if (UserAuthorization::isAuthCookiePresent()) {
            // cookie OK, redirect to target...
            $this->view->redirect(urldecode($this->getRedirectTarget()));
            exit();
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
        exit();
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
        exit();
    }
}
