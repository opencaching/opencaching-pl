<?php
namespace Controllers;

use Utils\Log\Log;
use Utils\Text\UserInputFilter;
use Utils\Uri\Uri;
use lib\Objects\Neighbourhood\Neighbourhood;
use lib\Objects\Notify\Notify;
use lib\Objects\User\U2UEmailSender;
use lib\Objects\User\User;
use lib\Objects\User\UserNotify;
use lib\Objects\User\UserPreferences\UserPreferences;
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

    /**
     * Displays page to change notification settings
     */
    public function notifySettings()
    {
        $this->redirectNotLoggedUsers();

        $neighbourhoods = Neighbourhood::getAdditionalNeighbourhoodsList($this->loggedUser);

        $settings = $this->loggedUser->getCacheWatchEmailSettings();
        // check settings and reset to defaults if necessary
        $watchmailMode = $settings['watchmail_mode'];
        $watchmailHour = $settings['watchmail_hour'];
        $watchmailDay = $settings['watchmail_day'];

        if (! $this->areEmailSettingsInScope($watchmailMode, $watchmailHour, $watchmailDay)) {

            // email settings are wrong - reset to defaults
            // by default send notification: hourly
            $watchmailMode = UserNotify::SEND_NOTIFICATION_HOURLY;
            $watchmailHour = 0; // default at midnight
            $watchmailDay = 7; // default sunday

            $this->loggedUser->updateCacheWatchEmailSettings($watchmailMode, $watchmailHour, $watchmailDay);
        }

        $this->view->setVar('intervalSelected', $watchmailMode);
        $this->view->setVar('weekDaySelected', $watchmailDay);
        $this->view->setVar('hourSelected', $watchmailHour);
        $this->view->setVar('notifyCaches', $this->loggedUser->getNotifyCaches());
        $this->view->setVar('notifyLogs', $this->loggedUser->getNotifyLogs());
        $this->view->setVar('neighbourhoods', $neighbourhoods);
        $this->view->setTemplate('userProfile/notifySettings');
        $this->view->loadJQuery();
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/userProfile/userProfile.css'));
        $this->view->buildView();
    }

    /**
     * Checks if given params are right settings for notification period
     *
     * @param int $watchmailMode
     * @param int $watchmailHour
     * @param int $watchmailDay
     * @return boolean
     */
    private function areEmailSettingsInScope($watchmailMode, $watchmailHour, $watchmailDay)
    {
        return (is_numeric($watchmailMode) && in_array($watchmailMode, [
            UserNotify::SEND_NOTIFICATION_DAILY,
            UserNotify::SEND_NOTIFICATION_HOURLY,
            UserNotify::SEND_NOTIFICATION_WEEKLY
        ]) && is_numeric($watchmailHour) && $watchmailHour >= 0 && $watchmailHour <= 23 && is_numeric($watchmailDay) && $watchmailDay >= 1 && $watchmailDay <= 7);
    }

    /**
     * Sets user.notify_caches for logged user (via AJAX)
     *
     * @param number $state
     */
    public function ajaxSetNotifyCaches($state = 0)
    {
        $this->checkUserLoggedAjax();

        if (UserNotify::setUserCachesNotify($this->loggedUser, $state)) {
            $this->ajaxSuccessResponse();
        } else {
            $this->ajaxErrorResponse();
        }
    }

    /**
     * Sets user.notify_logs for logged user (via AJAX)
     *
     * @param number $state
     */
    public function ajaxSetNotifyLogs($state = 0)
    {
        $this->checkUserLoggedAjax();

        if (UserNotify::setUserLogsNotify($this->loggedUser, $state)) {
            $this->ajaxSuccessResponse();
        } else {
            $this->ajaxErrorResponse();
        }
    }

    /**
     * Sets notify flag for logged user Neighbourhood (via AJAX)
     *
     * $_POST['nbh'] - number (seq) of user's Nbh
     * $_POST['state'] - new state of notify flag
     */
    public function ajaxSetNeighbourhoodNotify()
    {
        $this->checkUserLoggedAjax();

        if (isset($_POST['nbh']) && isset($_POST['state'])) {
            if (Neighbourhood::setNeighbourhoodNotify($this->loggedUser, (int) $_POST['nbh'], $_POST['state'])) {
                $this->ajaxSuccessResponse();
            }
        }
        $this->ajaxErrorResponse();
    }

    /**
     * Sets user's notifications period (via AJAX)
     *
     * $_POST['watchmail_mode'] - one of UserNotify::SEND_NOTIFICATION_*
     * $_POST['watchmail_hour'] & $_POST['watchmail_day'] - notyfication period settings
     */
    public function ajaxSetNotifySettings()
    {
        $this->checkUserLoggedAjax();

        $watchmailMode = isset($_POST['watchmail_mode']) ? $_POST['watchmail_mode'] : '';
        $watchmailHour = isset($_POST['watchmail_hour']) ? $_POST['watchmail_hour'] : '';
        $watchmailDay = isset($_POST['watchmail_day']) ? $_POST['watchmail_day'] : '';

        if ($this->areEmailSettingsInScope($watchmailMode, $watchmailHour, $watchmailDay)) {
            $this->loggedUser->updateCacheWatchEmailSettings($watchmailMode, $watchmailHour, $watchmailDay);
            $this->ajaxSuccessResponse();
        }
        $this->ajaxErrorResponse();
    }

    /**
     * Supports U2U mails
     *
     * @param int $userId
     * @param string $subject
     */
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