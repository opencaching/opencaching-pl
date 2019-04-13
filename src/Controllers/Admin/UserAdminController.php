<?php
namespace src\Controllers\Admin;

use src\Controllers\BaseController;
use src\Utils\Text\UserInputFilter;
use src\Utils\Uri\SimpleRouter;
use src\Utils\Uri\Uri;
use src\Models\Admin\AdminNote;
use src\Models\Admin\AdminNoteSet;
use src\Models\User\User;
use src\Models\User\UserAdmin;
use src\Models\User\UserAuthorization;
use src\Models\User\UserEmailSender;
use src\Models\User\UserNotify;
use src\Models\User\MultiUserQueries;

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

        $this->redirectNotLoggedUsers();

        if (! $this->loggedUser->hasOcTeamRole()) {
            $this->view->redirect('/');
        }

    }

    public function isCallableFromRouter($actionName)
    {
        return true;
    }

    public function index($userId = null)
    {
        $this->initViewedUser($userId);

        $this->view->setVar('user', $this->viewedUser);
        $this->view->setVar('infoMsg', $this->infoMsg);
        $this->view->setVar('errorMsg', $this->errorMsg);
        $this->view->setVar('userNotes', AdminNoteSet::getNotesForUser($this->viewedUser, 10000));
        $this->view->addLocalCss(Uri::getLinkWithModificationTime('/views/admin/admin.css'));
        $this->view->loadJQuery();
        $this->view->setTemplate('admin/user_admin');
        $this->view->buildView();
    }

    /**
     * Admin search engine for users
     */
    public function search()
    {
        $usersTable = [];
        $userName = '';

        if (isset($_POST['username'])) { //There are submitted data
            $userName = trim(strip_tags($_POST['username']));

            // First try - submitted data is an e-mail of existing user
            if (
                strpos($userName, '@')
                && ! is_null($user = User::fromEmailFactory($userName))
                ) {

                $this->view->redirect(SimpleRouter::getLink(
                    'Admin.UserAdmin',
                    'index',
                    $user->getUserId())
                    );
            }

            // Second try - submitted data is full username of existing user
            if (! is_null($user = User::fromUsernameFactory($userName))) {
                    $this->view->redirect(SimpleRouter::getLink(
                        'Admin.UserAdmin',
                        'index',
                        $user->getUserId())
                        );
                }

            // Third try - submitted data is substring of existing username
            // so display list of users
            if (mb_strlen($userName) >= 3) {
                $usersTable = MultiUserQueries::searchUser($userName);
                // If there is exact one result - redirect for user admin
                if (sizeof($usersTable) == 1) {
                    $this->view->redirect(SimpleRouter::getLink(
                        'Admin.UserAdmin',
                        'index',
                        $usersTable[0]->getUserId())
                        );
                }
                // If there is no results - show message
                if (sizeof($usersTable) == 0) {
                    $this->errorMsg = tr('message_user_not_found');
                }
            }
        } // End of submitted data process

        $this->view->setVar('userName', $userName);
        $this->view->setVar('usersTable', $usersTable);
        $this->view->setVar('errorMsg', $this->errorMsg);
        $this->view->setTemplate('admin/user_search');
        $this->view->buildView();
    }

    /**
     * Adds Admin note to $userId and redirects to main user admin page
     *
     * @param int $userId
     */
    public function addNote($userId = null)
    {
        $this->initViewedUser($userId);

        if (isset($_POST['note_content']) && ! empty($_POST['note_content'])) {
            $note = UserInputFilter::purifyHtmlString($_POST['note_content']);
            AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), false, $note);
            $this->infoMsg = tr('admin_user_noteok');
        }
        unset($this->viewedUser);
        $this->index($userId);
    }

    /**
     * (un)bans user $userId (depends of $state)
     *
     * @param int $userId
     * @param boolean $state
     */
    public function userBan($userId = null, $state = null)
    {
        $this->initViewedUser($userId);

        if (! is_null($state)) {
            $state = boolval($state);
            UserAdmin::setBanStatus($this->viewedUser, $state);

            if ($state) { // ban
                UserAuthorization::removeUserSessions($this->viewedUser);
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::BAN);
            } else { // unban
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::UNBAN);
            }
        }
        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin', 'index', $userId));
    }

    /**
     * (un)bans stats for user $userId (depends of $state)
     *
     * @param int $userId
     * @param boolean $state
     */
    public function statBan($userId = null, $state = null)
    {
        $this->initViewedUser($userId);

        if (! is_null($state)) {
            $state = boolval($state);
            UserAdmin::setStatBanStatus($this->viewedUser, $state);

            if ($state) { // ban stats
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::BAN_STATS);
            } else { // unban stats
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::UNBAN_STATS);
            }
        }
        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin', 'index', $userId));
    }

    /**
     * Sets VerifyAll flag for $userId to $state
     *
     * @param int $userId
     * @param boolean $state
     */
    public function verifyAll($userId = null, $state = null)
    {
        $this->initViewedUser($userId);

        if (! is_null($state)) {
            $state = boolval($state);
            UserAdmin::setVerifyAllStatus($this->viewedUser, $state);

            if ($state) { // verify all
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::VERIFY_ALL);
            } else { // remove verify all
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::NO_VERIFY_ALL);
            }
        }
        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin', 'index', $userId));
    }

    /**
     * Sets Ignore_founds flag for $userId to $state
     *
     * @param int $userId
     * @param boolean $state
     */
    public function createNoLimit($userId = null, $state = null)
    {
        $this->initViewedUser($userId);

        if (! is_null($state)) {
            $state = boolval($state);
            UserAdmin::setCreateWithoutLimitStatus($this->viewedUser, $state);

            if ($state) { // user can create
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::IGNORE_FOUND_LIMIT);
            } else { // user cannot create
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::IGNORE_FOUND_LIMIT_RM);
            }
        }
        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin', 'index', $userId));
    }

    /**
     * Changes new caches notify state for $userId
     *
     * @param int $userId
     * @param boolean $state
     */
    public function notifyCaches($userId = null, $state = null)
    {
        $this->initViewedUser($userId);

        if (! is_null($state)) {
            $state = boolval($state);
            UserNotify::setUserCachesNotify($this->viewedUser, $state);

            if ($state) { // turn on caches notify
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::NOTIFY_CACHES_ON);
            } else { // turn off caches notify
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::NOTIFY_CACHES_OFF);
            }
        }
        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin', 'index', $userId));
    }

    /**
     * Changes new logs notify state for $userId
     *
     * @param int $userId
     * @param boolean $state
     */
    public function notifyLogs($userId = null, $state = null)
    {
        $this->initViewedUser($userId);

        if (! is_null($state)) {
            $state = boolval($state);
            UserNotify::setUserLogsNotify($this->viewedUser, $state);

            if ($state) { // turn on logs notify
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::NOTIFY_LOGS_ON);
            } else { // turn off logs notify
                AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::NOTIFY_LOGS_OFF);
            }
        }
        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin', 'index', $userId));
    }

    /**
     * Activate user
     *
     * @param int $userId
     */
    public function activateUser($userId = null)
    {
        $this->initViewedUser($userId);

        if (! $this->viewedUser->isUserActivated()) {
            User::activateUser($this->viewedUser->getUserId());
            UserEmailSender::sendPostActivationMessage($this->viewedUser);
            AdminNote::addAdminNote($this->loggedUser->getUserId(), $this->viewedUser->getUserId(), true, AdminNote::ACTIVATE);
        }

        unset($this->viewedUser);
        $this->view->redirect(SimpleRouter::getLink('Admin.UserAdmin', 'index', $userId));
    }

    private function initViewedUser($userId)
    {
        $this->viewedUser = User::fromUserIdFactory($userId);

        if (!$this->viewedUser) {
            $this->view->redirect('/');
        }
    }
}
