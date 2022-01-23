<?php
namespace src\Controllers\Cron;

use src\Controllers\BaseController;
use src\Utils\Uri\SimpleRouter;

class CronAdminController extends BaseController
{

    public function isCallableFromRouter($actionName): bool
    {
        // all public methods can be called by router
        return true;
    }

    public function index()
    {
        $this->redirectNotLoggedUsers();
        $this->showCronAdmin();
    }

    public function run($job = null)
    {
        if ($job && $this->allowRun()) {
            ob_start();
            $this->runJob($job);
            $this->showCronAdmin(ob_get_clean());
        } else {
            $this->view->redirect(SimpleRouter::getLink('Cron.CronAdmin'));
        }
    }

    private function showCronAdmin($message = '')
    {
        $cronJobs = new CronJobsController();
        $this->view->setVar('message', $message);
        $this->view->setVar('jobs', $cronJobs->getScheduleStatus());
        $this->view->setVar('allowRun', $this->allowRun());
        $this->view->setTemplate('sysAdmin/cronAdmin');
        $this->view->buildView();
    }

    private function runJob($jobName)
    {
        $cronJobs = new CronJobsController($jobName);
        $cronJobs->index();
    }

    private function allowRun(): bool
    {
        return $this->isUserLogged() && $this->loggedUser->hasSysAdminRole();
    }
}
