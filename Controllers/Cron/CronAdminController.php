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
        $this->securityCheck();

        $this->showCronAdmin();
    }

    public function run($job = null)
    {
        $this->securityCheck();

        if ($job) {
            $this->runJob($job);
        }
        $this->view->redirect(SimpleRouter::getLink('Cron.CronAdmin'));
    }

    private function showCronAdmin()
    {
        $cronJobs = new CronJobsController();
        $this->view->setVar('jobs', $cronJobs->getScheduleStatus());
        $this->view->setVar('runJobUri', Uri::getCurrentRequestUri() . '?action=run&job=');
        $this->view->setTemplate('cron/cronAdmin');
        $this->view->buildView();
    }

    private function runJob($jobName)
    {
        $cronJobs = new CronJobsController($jobName);
        $cronJobs->index();
    }

    private function securityCheck()
    {
        if (!$this->isUserLogged() || !$this->loggedUser->hasSysAdminRole()) {
            $this->view->redirect('/');
            exit();
        }
    }
}
