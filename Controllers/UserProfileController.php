<?php
namespace Controllers;

use Utils\Log\Log;
use Utils\Uri\Uri;
use lib\Objects\User\U2UEmailSender;
use lib\Objects\User\User;
use lib\Objects\User\UserPreferences\UserPreferences;
use Utils\Text\UserInputFilter;
use lib\Objects\User\UserPreferences\UserProfilePref;

class UserProfileController extends BaseController
{

    /** @var User $requestedUser */
    private $requestedUser;

    /** @var array */
    private $preferences;

    private $infoMsg = '';

    private $errorMsg = '';

    public function __construct()
    {
        parent::__construct();
        // $this->requestedUser = $this->loadRequestedUser();
    }

    public function isCallableFromRouter($actionName)
    {
        // all public methods can be called by router
        return true;
    }

    public function index()
    {
        // there is nothing here yet
    }

    public function mailTo($userId = null, $subject = '')
    {
        $this->redirectNotLoggedUsers();
        if (! $this->prepareUserData($userId)) {
            // Bad request - user not selected.
            $this->view->redirect('/');
        }
        $subject = UserInputFilter::purifyHtmlString(urldecode($subject));
        $content = '';
        if (isset($_POST['sendEmailAction'])) {
            $this->sendEmail($subject, $content);
        }
        $this->view->setVar('requestedUser', $this->requestedUser);
        $this->view->setVar('preferences', $this->preferences);
        $this->view->setVar('subject', $subject);
        $this->view->setVar('content', $content);
        $this->view->setVar('errorMsg', $this->errorMsg);
        $this->view->setVar('infoMsg', $this->infoMsg);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/userProfile/mailto.css'));
        $this->view->setTemplate('userProfile/mailto');
        $this->view->buildView();
    }

    /**
     * Sends U2U message.
     * Is called by mailTo() method after POST data detected
     * Params are needed to return given form data if sth is missing
     *
     * @param string $subject
     * @param string $content
     */
    private function sendEmail(&$subject, &$content)
    {
        // Check content
        if (! isset($_POST['content']) || (mb_strlen($content = strip_tags(trim($_POST['content']))) == 0)) {
            $this->errorMsg = tr('mailto_lackOfText');
        }
        // Check subject
        if (! isset($_POST['subject']) || (mb_strlen($subject = mb_substr(strip_tags(trim($_POST['subject'])), 0, 150)) == 0)) {
            $this->errorMsg = tr('mailto_lackOfSubject');
        }
        if (! empty($this->errorMsg)) {
            return;
        }
        // Save user preferences
        $this->preferences['email']['showMyEmail'] = isset($_POST['showMyEmail']);
        $this->preferences['email']['recieveCopy'] = isset($_POST['recieveCopy']);
        UserPreferences::savePreferencesJson(UserProfilePref::KEY, json_encode($this->preferences));
        // Send mail to recipient
        $result = U2UEmailSender::sendU2UMessage($this->loggedUser, $this->requestedUser, $subject, $content, $this->preferences['email']['showMyEmail']);
        if ($result && $this->preferences['email']['recieveCopy']) {
            // Send copy of email - to sender
            $result = U2UEmailSender::sendU2UCopy($this->loggedUser, $this->requestedUser, $subject, $content);
        }
        // Insert log entry into email_user
        Log::logEmail($this->loggedUser, $this->requestedUser, $subject, $this->preferences['email']['showMyEmail']);
        // Redirect to user profile page
        $uri = $this->requestedUser->getProfileUrl();
        if ($result) {
            $uri = Uri::setOrReplaceParamValue('infoMsg', tr('mailto_messageSent'), $uri);
        } else {
            $uri = Uri::setOrReplaceParamValue('errorMsg', tr('mailto_messageError'), $uri);
        }
        $this->view->redirect($uri);
    }

    private function prepareUserData($userId)
    {
        if (($this->requestedUser = User::fromUserIdFactory($userId)) == null) {
            return false;
        }
        $this->preferences = UserPreferences::getUserPrefsByKey(UserProfilePref::KEY)->getValues();
        return true;
    }

    private function loadRequestedUser()
    {
        if (isset($_REQUEST['userid'])) {
            return User::fromUserIdFactory($_REQUEST['userid']);
        }
        return null;
    }
}