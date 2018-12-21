<?php
namespace Controllers\Cron;

use Controllers\BaseController;
use Utils\Uri\SimpleRouter;
use Utils\Uri\Uri;

class CronAdminController extends BaseController
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
        $this->redirectNotLoggedUsers();
        $this->showCronAdmin();
    }

    public function run($job = null)
    {
        if ($job && $this->allowRun()) {
            $this->runJob($job);
        }
        $this->view->redirect(SimpleRouter::getLink('Cron.CronAdmin'));
    }

    private function showCronAdmin()
    {
        $cronJobs = new CronJobsController();
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

    private function allowRun()
    {
        return $this->isUserLogged() && $this->loggedUser->hasSysAdminRole();
    }
}
