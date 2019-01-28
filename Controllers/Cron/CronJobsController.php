<?php
namespace Controllers\Cron;

use Controllers\BaseController;
use Utils\Lock\Lock;
use okapi\Facade;

class CronJobsController extends BaseController
{
    private $jobToRun;
    private $jobs = [];

    public function __construct($jobToRun = null)
    {
        parent::__construct();
        if ($jobToRun !== null && !$this->ocConfig->getCronjobSchedule($jobToRun)) {
            die("unknown job: ".$jobToRun."\n");
        }
        $this->jobToRun = $jobToRun;
    }

    public function isCallableFromRouter($actionName)
    {
        // this controller is used by cron only - router shouldn't call it!
        return false;
    }

    public function index()
    {
        $this->processCronJobs();
    }

    private function processCronJobs()
    {
        $lockHandle = Lock::tryLock($this, Lock::EXCLUSIVE | Lock::NONBLOCKING);
        if (!$lockHandle) {
            $lastLockedRun = Facade::cache_get('ocpl/lastLockedCronRun');
            $minutesSinceLastRun = (time() - strtotime($lastLockedRun)) / 60;

            // We allow one run to take a maximum of 19 minutes, so that admins
            // are not spammed with error messages if something is slow.

            if ($minutesSinceLastRun > 19) {
                die("Another instance of CronJobsController is running for ".$minutesSinceLastRun.
                    " minutes, or problem with lock file.\n"
                );
            }
        } else {
            Facade::cache_set('ocpl/lastLockedCronRun', date('Y-m-d H:i:s'), 7200);
        }

        $this->prepareJobs();

        // First run non-reentrant jobs in 'locked' mode, then reentrant jobs.
        // See Jobs::isReentrant() for explanation of reentrant cronjobs.

        if ($lockHandle) {
            $this->runJobs(false);
            Lock::unlock($lockHandle);
        }
        $this->runJobs(true);
    }

    private function prepareJobs()
    {
        foreach ($this->ocConfig->getCronjobSchedule() as $jobName => $schedule) {
            if (!$this->jobToRun || $jobName == $this->jobToRun) {
                $jobPath = __DIR__."/Jobs/".$jobName.".php";
                if (!file_exists($jobPath)) {
                    echo "\nConfigured cronjob '".$jobName."' does not exist.\n";
                } else {
                    require_once $jobPath;
                    $this->jobs[] = new $jobName();
                }
            }
        }
    }

    private function runJobs($reentrant)
    {
        foreach ($this->jobs as $job) {
            if ($reentrant == $job->isReentrant() && ($this->jobToRun || $job->isDue())) {
                if ($job->mayRunNow()) {
                    if ($result = $job->run()) {
                        echo $result . "\n";
                    } else {
                        $job->setLastRun();
                    }
                }
            }
        }
    }

    public function getScheduleStatus()
    {
        $result = [];
        foreach ($this->ocConfig->getCronjobSchedule() as $jobName => $schedule) {
            $jobPath = __DIR__."/Jobs/".$jobName.".php";
            if (!file_exists($jobPath)) {
                $lastRun = '?';
            } else {
                require_once $jobPath;
                $job = new $jobName();
                $lastRun = $job->getLastRun();
            }
            $result[$jobName] = [
                'shortName' => substr($jobName, 0, strlen($jobName) - 3),
                'schedule' => $schedule,
                'lastRun' => $lastRun,  // is null if not run yet
                'jobFileMissing' => ($lastRun == '?'),
                'mayRunNow' => $job->mayRunNow(),
            ];
        }
        return $result;
    }
}
