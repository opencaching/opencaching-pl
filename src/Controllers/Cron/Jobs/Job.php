<?php

namespace src\Controllers\Cron\Jobs;

use okapi\Facade;
use src\Models\OcConfig\OcConfig;
use src\Utils\Database\OcDb;

// Base class for all cron jobs. To add a new job:
//
//   - derive a new ...Job class
//   - implement the run() method
//   - add default schedule to Config/cronjobs.default.php

abstract class Job
{
    protected OcConfig $ocConfig;

    protected OcDb $db;

    public function __construct()
    {
        $this->ocConfig = OcConfig::instance();
        $this->db = OcDb::instance();
    }

    /**
     *  Returns nothing (null) on success, error message string on error.
     */
    abstract public function run();

    /**
     * Returns true, if it is safe to run the job in multiple parallel instances
     * AND it makes sense to do so - see for example the cache altitude updates,
     * which most of the time are waiting for slow external resources.
     */
    public function isReentrant(): bool
    {
        return false;
    }

    /**
     * Returns true, if it is safe to run the job at any date and time.
     *
     * false will disable the "run now" action in admin menu. The job then may
     * still be run manually for debugging via util.sec/cron/run_cron.php
     * (job name as argument).
     */
    public function mayRunNow(): bool
    {
        return true;
    }

    /**
     * Returns true if $this job should run now.
     */
    public function isDue()
    {
        $jobName = get_class($this);
        $schedule = $this->ocConfig->getCronjobSchedule($jobName);

        if ($schedule == 'disabled') {
            return false;
        }
        $lastRun = $this->getLastRun();

        if ($lastRun === null) {
            $lastRun = 'xxxx-xx-xx xx:xx:xx';
        }

        // run every x minutes
        if (preg_match('/^every (\\d+) minutes$/', $schedule, $matches)) {
            $this->validateMinutes($matches[1]);

            // If this is the first run, strototime() will return FALSE, which
            // translates to 0 and runs the job.
            // By subtracting 3 minutes (and running the controller every 5 minutes),
            // we enforce an interval of at least 2 minutes since the last run.

            return (time() - strtotime($lastRun)) >= 60 * ($matches[1] - 3);

            // run once per hour
        }

        if (preg_match('/^hourly at :(\\d{2})$/', $schedule, $matches)) {
            $this->validateMinutes($matches[1]);

            return
                date('Y-m-d H') != substr($lastRun, 0, 13)
                && date('i') >= $matches[1];

            // run once per day
        }

        if (preg_match('/^daily at (\\d{1,2}):(\\d{2})$/', $schedule, $matches)) {
            $this->validateMinutes($matches[2]);

            return
                date('Y-m-d') != substr($lastRun, 0, 10)
                && date('H:i') >= sprintf('%02d:%02d', $matches[1], $matches[2]);

            // run once per week
        }

        if (preg_match('/^weekly on ([A-Za-z]+)day at (\\d{1,2}):(\\d{2})$/', $schedule, $matches)) {
            $this->validateMinutes($matches[3]);
            $dow = array_search(
                strtolower($matches[1]),
                ['mon', 'tues', 'wednes', 'thurs', 'fri', 'satur', 'sun']
            );

            if ($dow === false) {
                exit('Invalid day of week (' . $matches[1] . 'day) for ' . $jobName . "\n");
            }

            return
                date('N') == $dow + 1
                && date('Y-m-d') != substr($lastRun, 0, 10)
                && date('H:i') >= sprintf('%02d:%02d', $matches[2], $matches[3]);

            // run once a month
        }

        if (preg_match('/^monthly on day (\\d+) at (\\d{1,2}):(\\d{2})$/', $schedule, $matches)) {
            $this->validateMinutes($matches[3]);

            if ($matches[1] > 28) {
                exit(
                    'Invalid day of month (' . $matches[1] . ') for ' . $jobName
                    . "; must range between 1 and 28.\n"
                );
            }

            return
                date('d') == $matches[1]
                && date('Y-m') != substr($lastRun, 0, 7)
                && date('H:i') >= sprintf('%02d:%02d', $matches[2], $matches[3]);
        }

        exit("Invalid schedule '" . $schedule . "' for " . $jobName);
    }

    private function validateMinutes($minutes)
    {
        if ($minutes % 5 != 0) {
            exit(
                'Invalid minutes setting (' . $minutes . ') for ' . get_class($this)
                . "; must be a multiple of 5.\n"
            );
        }
    }

    final public function getLastRun()
    {
        return Facade::cache_get('ocpl/cronJobRun#' . get_class($this));
    }

    final public function setLastRun()
    {
        Facade::cache_set(
            'ocpl/cronJobRun#' . get_class($this),
            date('Y-m-d H:i:s'),
            366 * 24 * 3600
        );
    }
}
