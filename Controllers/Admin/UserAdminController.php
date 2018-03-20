<?php
namespace Controllers\Admin;

use Controllers\BaseController;
use Utils\Uri\Uri;
use lib\Objects\User\User;
use lib\Objects\User\AdminNote;
use Utils\Text\UserInputFilter;
use lib\Controllers\Php7Handler;
use lib\Objects\User\UserAdmin;
use lib\Objects\User\UserAuthorization;
use Utils\Uri\SimpleRouter;
use lib\Objects\User\UserNotify;

class UserAdminController extends BaseController
{

    private $infoMsg = null;
    private $errorMsg = null;

    /**
     * User who is administered
     *
     * @var User
     */
    private $viewedUser = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index($userId = null)
    {
        $this->checkSecurityAndPrepare($userId);

        $this->view->setVar('user', $this->viewedUser);
        $this->view->setVar('infoMsg', $this->infoMsg);
        $this->view->setVar('errorMsg', $this->errorMsg);
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/tpl/stdstyle/admin/admin.css'));
        $this->view->loadJQuery();
        $this->view->setTemplate('admin/user_admin');
        $this->view->buildView();
    }

    /**
     * Adds Admin note to $userId and redirects to main user admin page
     *
     * @param int $userId
     */
    public function addNote($userId = null)
    {
        $this->checkSecurityAndPrepare($userId);

        if (isset($_POST['note_content']) && ! empty($_POST['note_content'])) {
            $note = UserInputFilter::purifyHtmlString($_POST['note_content']);
            AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), false, $note);
            $this->infoMsg = tr('admin_user_noteok');
        }
        unset($this->viewedUser);
        $this->index($userId);
    }

    public function userBan($userId = null, $state = null)
    {
        $this->checkSecurityAndPrepare($userId);

        if (! is_null($state)) {
            $state = Php7Handler::Boolval($state);
            UserAdmin::setBanStatus($this->viewedUser, $state);

            if ($state) { // ban
                UserAuthorization::removeUserSessions($this->viewedUser);
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), TRUE, AdminNote::BAN);
            } else { // unban
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), TRUE, AdminNote::UNBAN);
            }
        }
        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin','index', $userId));
    }

    public function statBan($userId = null, $state = null)
    {
        $this->checkSecurityAndPrepare($userId);

        if (! is_null($state)) {
            $state = Php7Handler::Boolval($state);
            UserAdmin::setStatBanStatus($this->viewedUser, $state);

            if ($state) { // ban stats
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), TRUE, AdminNote::BAN_STATS);
            } else { // unban stats
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), TRUE, AdminNote::UNBAN_STATS);
            }
        }
        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin','index', $userId));
    }

    public function verifyAll($userId = null, $state = null)
    {
        $this->checkSecurityAndPrepare($userId);

        if (! is_null($state)) {
            $state = Php7Handler::Boolval($state);
            UserAdmin::setVerifyAllStatus($this->viewedUser, $state);

            if ($state) { // verify all
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), TRUE, AdminNote::VERIFY_ALL);
            } else { // remove verify all
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), TRUE, AdminNote::NO_VERIFY_ALL);
            }
        }
        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin','index', $userId));
    }

    public function createNoLimit($userId = null, $state = null)
    {
        $this->checkSecurityAndPrepare($userId);

        if (! is_null($state)) {
            $state = Php7Handler::Boolval($state);
            UserAdmin::setCreateWithoutLimitStatus($this->viewedUser, $state);

            if ($state) { // user can create
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), TRUE, AdminNote::IGNORE_FOUND_LIMIT);
            } else { // user cannot create
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), TRUE, AdminNote::IGNORE_FOUND_LIMIT_RM);
            }
        }
        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin','index', $userId));
    }

    public function notifyCaches($userId = null, $state = null)
    {
        $this->checkSecurityAndPrepare($userId);

        if (! is_null($state)) {
            $state = Php7Handler::Boolval($state);
            UserNotify::setUserCachesNotify($this->viewedUser, $state);

            if ($state) { // turn on caches notify
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), TRUE, AdminNote::NOTIFY_CACHES_ON);
            } else { // turn off caches notify
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), TRUE, AdminNote::NOTIFY_CACHES_OFF);
            }
        }
        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin','index', $userId));
    }

    public function notifyLogs($userId = null, $state = null)
    {
        $this->checkSecurityAndPrepare($userId);

        if (! is_null($state)) {
            $state = Php7Handler::Boolval($state);
            UserNotify::setUserLogsNotify($this->viewedUser, $state);

            if ($state) { // turn on logs notify
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), TRUE, AdminNote::NOTIFY_LOGS_ON);
            } else { // turn off logs notify
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), TRUE, AdminNote::NOTIFY_LOGS_OFF);
            }
        }
        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin','index', $userId));
    }

    private function checkSecurityAndPrepare($userId)
    {
        // Check if user is logged and is admin
        $this->redirectNotLoggedUsers();
        if (! $this->loggedUser->isAdmin() || is_null($this->viewedUser = User::fromUserIdFactory($userId))) {
            $this->view->redirect('/');
        }
    }

}