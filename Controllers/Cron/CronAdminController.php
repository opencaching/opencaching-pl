<?php
namespace Controllers\Cron;

use Controllers\BaseController;
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
        if (!$this->isUserLogged() || !$this->loggedUser->hasSysAdminRole()) {
            $this->view->redirect('/');
            exit();
        }
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'run') {
                if (isset($_GET['job'])) {
                    $this->runJob($_GET['job']);
                }
            }
        }
        $this->showCronAdmin();
    }

    private function showCronAdmin()
    {
        $cronJobs = new CronJobsController();
        $this->view->setVar('jobs', $cronJobs->getScheduleStatus());

        $currentUriExploded = explode('?', Uri::getCurrentUri());
        $this->view->setVar('runJobUri', $currentUriExploded[0] . '?action=run&job=');        

        $this->view->setTemplate('cron/cronAdmin');
        $this->view->buildView();
    }

    private function runJob($jobName)
    {
        $cronJobs = new CronJobsController($jobName);
        $cronJobs->index();
    }
}
