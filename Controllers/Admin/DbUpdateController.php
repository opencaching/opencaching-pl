<?php
namespace Controllers\Admin;

use Controllers\BaseController;
use Controllers\UpdateController;
use Utils\Database\DbUpdates;
use Utils\Uri\SimpleRouter;
use Utils\Uri\Uri;

class DbUpdateController extends BaseController
{
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
        $this->securityCheck(false);
        $this->showAdminView();
    }

    /**
     * @param $messages string
     */
    private function showAdminView($messages = '')
    {
        $updates = DbUpdates::getAll();
        $updates = array_reverse($updates, true);

        $updatesShouldRun = false;

        foreach ($updates as $update) {
            $update->adminActions = $this->getAvailableActions($update);
            $updatesShouldRun |= $update->shouldRun();
        }

        $this->view->setVar('updates', $updates);
        $this->view->setVar('updatesShouldRun', $updatesShouldRun);
        $this->view->setVar('messages', $messages);

        $this->buildTemplateView();
    }

    /**
     * @param $update DbUpdate
     */
    private function getAvailableActions($update)
    {
        $actions = [];  // dictionary of action => title

        $wasRun = ($update->wasRunAt() !== null);
        $specialPurpose = substr($update->getName(), 0, 3) < 100;

        if (!$this->ocConfig->inDebugMode()) {
            // production site

            if (!$wasRun) {
                $actions['run'] = 'run';
            } else {
                if ($update->getRuntype() == 'always' || $specialPurpose) {

                    # For tests an special-purpose updates, it can make sense to
                    # re-run them even on production sites, and to roll them back.

                    $actions['run'] = 'run again';

                    if ($update->hasRollback()) {
                        $actions['rollback'] = 'rollback';
                    }
                }
            }
        } else {
            // developer site

            if (!$wasRun) {
                $actions['run'] = 'run';

                if ($update->hasRollback()) {

                    # There may be cases when a developer wants to test
                    # the behaviour of a rollback method even without having
                    # run the update.

                    $actions['rollback'] = 'try rollback';
                }

            } else {
                # It can make sense to repeat an update, e.g. if there is no
                # rollback method and the developer did a manual rollback.

                $actions['run'] = 'run again';

                if ($update->hasRollback() && (!$update->isInGitMasterBranch() || $specialPurpose)) {

                    # The workflow for rolling back an already deployed
                    # update is to create a new rollback-update and run that.
                    # To enforce this workflow, we disable direct rollback
                    # of deployed updates.

                    $actions['rollback'] = 'rollback';
                }
            }

            if (!$update->isInGitMasterBranch()) {

                if (!($wasRun && $update->hasRollback())) {
                    $actions['askDelete'] = 'delete';
                } else {
                    # If there is a rollback method, we require developers
                    # to run that before they can delete the script. This
                    # helps to keep the database clean.
                }
            }

            $actions['askRename'] = 'rename';
        }

        return $actions;
    }

    /**
     * Get rid of the URI action, to avoid re-doing it on page reload.
     */
    private function reload($uriParams = [])
    {
        $this->view->redirect(
            Uri::addParamsToUri(SimpleRouter::getLink('Admin.DbUpdate'), $uriParams)
        );
    }

    public function viewScript($uuid)
    {
        $this->securityCheck(false);

        $update = $this->getUpdateFromUuid($uuid);
        $this->view->setVar('viewScript', $uuid);
        $this->view->setVar('scriptFilename', $update->getFileName());
        $this->view->setVar('scriptSource', $update->getScriptContents());
        $this->buildTemplateView();
    }

    public function run($uuid = null)
    {
        // This action is allowed to run on production sites (by sysAdmins only).
        $this->securityCheck(false);

        try {
            if (!$uuid) {
                $messages = UpdateController::runOcDatabaseUpdate();
            } else {
                $update = $this->getUpdateFromUuid($uuid);

                if (!isset($this->getAvailableActions($update)['run'])
                    && empty($_REQUEST['override'])
                ) {

                    # This can happen on page reload on a production site:
                    # Update was allowed to run, but must not re-run.

                    $messages = sprintf(tr('admin_dbupdate_norun'), $update->getName());
                } else {
                    $messages = $this->getUpdateFromUuid($uuid)->run();
                }
            }
        } catch (\Exception $e) {
            $messages = get_class($e).": " . $e->getMessage() . "\n\n" . $e->getTraceAsString();
        }

        $this->showAdminView($messages);

        # The update will be run again if the user reloads the page.
        # Alternatively, we could reload the page now without running update
        # and pass the message. But then if the user reloads again, the
        # message will confusingly be shown again. Probably it's the best
        # solution to re-run on reload.
    }

    public function rollback($uuid)
    {
        // This action is allowed to run on production sites for some special
        // updates like tests (by sysAdmins only).
        $this->securityCheck(false);

        $update = $this->getUpdateFromUuid($uuid);

        if (!isset($this->getAvailableActions($update)['rollback'])
            && empty($_REQUEST['override'])
        ) {

            # This can happen on page reload on a production site:
            # Update was allowed to roll back, but only once.

            $messages = sprintf(tr('admin_dbupdate_norollback'), $update->getName());
        } else {
            try {
                $messages = $this->getUpdateFromUuid($uuid)->rollback();
            } catch (\Exception $e) {
                $messages = get_class($e).": " . $e->getMessage() . "\n\n" . $e->getTraceAsString();
            }
        }

        $this->showAdminView($messages);

        # See comment in run() method.
    }

    public function askRename($uuid)
    {
        $this->securityCheck();
        $this->view->setVar('askRename', $uuid);
        $this->view->setVar('oldName', $this->getUpdateFromUuid($uuid)->getName());
        $this->buildTemplateView();
    }

    public function rename($uuid)
    {
        $this->securityCheck();
        if (isset($_REQUEST['newName'])) {

            // auto-convert some non-allowed spacers
            $newName = preg_replace('/[ \-]/', '_', $_REQUEST['newName']);

            $this->getUpdateFromUuid($uuid)->rename($newName);

            # This could be improved by returning error codes from rename(),
            # e.g. for "invalid characters" or "name too short", and
            # presenting an error message.
        }
        $this->reload();
    }

    public function askDelete($uuid)
    {
        $this->securityCheck();
        $this->view->setVar('askDelete', $uuid);
        $this->view->setVar('fileName', $this->getUpdateFromUuid($uuid)->getFileName());
        $this->buildTemplateView();
    }

    public function delete($uuid)
    {
        $this->securityCheck();
        DbUpdates::delete($uuid);
        $this->reload();
    }

    public function createNew()
    {
        $this->securityCheck();
        DbUpdates::create(
            $this->applicationContainer->getLoggedUser()->getUserName()
        );
        $this->reload();
    }

    public function deployed()
    {
        // This action is public. Developers may want to check if an update
        // was deployed to the site.

        $text = "";
        foreach (DbUpdates::getAll() as $update) {
            if ($r = $update->wasRunAt()) {
                $text .= $update->getUuid() . " " . $r . "\n";
            }
        }
        $this->view->showPlainText($text);
    }

    private function getUpdateFromUuid($uuid)
    {
        $update = DbUpdates::get($uuid);
        if ($update) {
            return $update;
        }
        $this->showAdminView('Bad UUID');
    }

    private function buildTemplateView()
    {
        $this->view->setVar('developerMode', $this->ocConfig->inDebugMode());
        $this->view->setTemplate('sysAdmin/dbUpdate');
        $this->view->buildView();
        exit();
    }

    private function securityCheck($onlyDevelopers = true)
    {
        if (!$this->isUserLogged() || !$this->loggedUser->hasSysAdminRole() ||
            ($onlyDevelopers && !$this->ocConfig->inDebugMode())
        ) {
            $this->view->redirect('/');
            exit();
        }
    }
}
