<?php
namespace Controllers;

use Utils\Text\UserInputFilter;
use Utils\Text\Validator;
use Utils\Uri\Uri;
use lib\Objects\User\User;
use lib\Objects\User\UserEmailSender;

class UserRegistrationController extends BaseController
{
    public function __construct(){
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    /**
     * Displays register page
     */
    public function index()
    {
        if ($this->isUserLogged()) {
            $this->view->redirect('/');
            exit();
        }
        $this->showRegisterForm();
    }

    /**
     * Receives user input from register page. Checks data and creates new user
     * (or redisplays register page on error)
     */
    public function registerSubmit()
    {
        if ($this->isUserLogged()) {
            $this->view->redirect('/');
            exit();
        }

        $username = (isset($_POST['username'])) ? $_POST['username'] : '';
        $username = UserInputFilter::purifyHtmlString($username);
        $email = (isset($_POST['email'])) ? $_POST['email'] : '';
        $email = UserInputFilter::purifyHtmlString($email);

        // Check username
        if (! Validator::isValidUsername($username)) {
            $this->showRegisterForm($username, $email, tr('error_username_not_ok'));
        }
        if ($usertmp = User::fromUsernameFactory($username) !== null) {
            unset($usertmp);
            $this->showRegisterForm($username, $email, tr('error_username_exists'));
        }
        unset($usertmp);

        // Check e-mail
        if (! Validator::isValidEmail($email)) {
            $this->showRegisterForm($username, $email, tr('error_email_not_ok'));
        }
        if ($usertmp = User::fromEmailFactory($email) !== null) {
            unset($usertmp);
            $this->showRegisterForm($username, $email, tr('error_email_exists'));
        }
        unset($usertmp);

        // Check if user accept rules
        if (! isset($_POST['rules'])) {
            $this->showRegisterForm($username, $email, tr('error_tos'));
        }

        // Check if user accept rules
        if (! isset($_POST['age'])) {
            $this->showRegisterForm($username, $email, tr('error_age'));
        }

        // Check password
        $password = (isset($_POST['password'])) ? $_POST['password'] : '';
        if (! Validator::checkStrength($password)) {
            $this->showRegisterForm($username, $email, tr('password_weak'));
        }

        // GDPR Law check
        if (new \DateTime() > new \DateTime("2018-05-25 00:00:00")) {
            $rulesConf = true;
        } else {
            $rulesConf = false;
        }

        // Add user to DB and send activation message
        if (! User::addUser($username, $password, $email, $rulesConf)) {
            $this->showErrorMessage(tr('page_error'));
        }
        UserEmailSender::sendActivationMessage($username);

        $this->showSuccessMessage(tr('register_confirm'), tr('register_pageTitle'));
        exit();
    }

    private function showRegisterForm($username = '', $email = '', $errorMsg = null)
    {
        $this->view->setVar('username', $username);
        $this->view->setVar('email', $email);
        $this->view->setVar('errorMsg', $errorMsg);
        $this->view->setVar('minAge', $this->ocConfig->getMinumumAge());
        $this->view->setTemplate('userRegistration/register');
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/userAuth/userAuth.css'));
        $this->view->addLocalJs(Uri::getLinkWithModificationTime('/tpl/stdstyle/userAuth/newPassword.js'), true, true);
        $this->view->loadJQuery();
        $this->view->buildView();
        exit();
    }

    /**
     * Activate user. Page is called from user activation e-mail
     *
     * @param int $userId
     * @param string $activationCode
     */
    public function activate($userId = null, $activationCode = null)
    {
        $activationCode = urldecode($activationCode);

        // Check params
        if (empty($activationCode) || is_null($user = User::fromUserIdFactory($userId))) {
            $this->showErrorMessage(tr('security_error'), tr('activation_title'));
        }

        // Check if user already is activated
        if ($user->getIsActive()) {
            $this->showErrorMessage(tr('activation_error'), tr('activation_title'));
        }

        // Check correct of activation code
        if ($activationCode != $user->getActivationCode()) {
            $this->showErrorMessage(tr('security_error'), tr('activation_title'));
        }

        // Activate user
        User::activateUser($user->getUserId());
        UserEmailSender::sendPostActivationMessage($user);
        unset($user);
        $this->showSuccessMessage(tr('activation_success'), tr('activation_title'));
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

}