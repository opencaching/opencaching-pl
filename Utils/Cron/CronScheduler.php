<?php
/**
 * Contains \Utils\Cron\CronScheduler class definition
 */
namespace Utils\Cron;

use lib\Objects\OcConfig\OcConfig;
use lib\Objects\Cron\CronCommons;
use lib\Objects\Cron\CronTask;
use lib\Objects\Cron\CronScheduledTask;
use lib\Objects\Cron\CronStatus;
use lib\Objects\Cron\CronHistory;
use lib\Objects\Cron\CronExternalWaitingRoom;
use Utils\Generators\Uuid;
use Utils\Lock\Lock;

/**
 * Runs the whole process of scheduling and executing periodic tasks according
 * to their settings and current system status
 */
final class CronScheduler
{
    /**
     * mapping of localtime function fields to used constants with optional
     * adjustment
     */
    const TIME_FIELDS = [
        "tm_min" => CronCommons::MINUTE,
        "tm_hour" => CronCommons::HOUR,
        "tm_mday" => CronCommons::DAY,
        "tm_mon" => [ CronCommons::MONTH, 1 ],
        "tm_wday" => CronCommons::WEEKDAY
    ];

    /**
     * @var integer Maximum historical entries stored in database; valid for
     *      scheduler task, default for other tasks
     */
    private $maxHistory;
    /**
     * @var integer Time in seconds the task is treated as finished/timeouted;
     *      initial for scheduler task, default for other tasks
     */
    private $ttl;
    /**
     * @var boolean true if tasks, which should be started between subsequent
     *      scheduler execution, are to be started now
     */
    private $includeMissed;
    /**
     * @var boolean true if another task of given entry point can start while
     *      while the previous one has not finished work - the default value
     */
    private $allowConcurrent;
    /**
     * @var CronTask The cron scheduler itself special task
     */
    private $selfTask;
    /**
     * @var array The tasks defined in configuration
     */
    private $tasks;
    /**
     * @var string The mode, direct or external, the scheduled tasks will be
     *      started with
     */
    private $executionMode;

    /**
     * Using CronConfigurator initiates attribute values from config parameter,
     * reading it from OcConfig if null; creates selfTask;
     *
     * @param array $config the cron configuration
     */
    public function __construct($config = null)
    {
        if ($config == null) {
            $config = OcConfig::instance()->getCronConfig();
            if ($config == null) {
                throw new \RuntimeException("Cron config not found");
            }
        }

        foreach(CronConfigurator::configureCommons($config) as $key=>$value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->selfTask = new CronTask(
            "* * * * *",
            null,
            "CronScheduler special task",
            $this->ttl,
            $this->maxHistory,
            $this->allowConcurrent
        );

        $this->tasks = CronConfigurator::configureTasks(
            $config,
            $this->ttl,
            $this->maxHistory,
            $this->allowConcurrent
        );
    }

    /**
     * Locks access in exclusive mode
     *
     * @return resource the lock handle
     */
    private function lock() {
        $lockHandle = Lock::tryLock($this, Lock::EXCLUSIVE);
        if (! $lockHandle) {
            throw new \RuntimeException("Cannot obtain lock");
        }
        return $lockHandle;
    }

    /**
     * Unlocks previously locked access
     *
     * @param resource $lockHandle the handle to unlock
     */
    private function unlock($lockHandle) {
        Lock::unlock($lockHandle);
    }

    /**
     * The main processing method of scheduler. Creates special
     * CronScheduledTask based on selfTask, then in exclusive mode walk through
     * configured tasks list and for each entry point:
     * - browses current working/finished tasks of the same entrypoint, adjusting
     *   time loop start value according to current status and settings
     * - checks if task is allowed to start in any minute between loop start
     *   value and now
     * - if true creates new CronScheduledTask based on the task configuration
     *   and adds it to the list.
     * The method then adjust the special task ttl according to summed up time
     * of all scheduled tasks and saves it in current status exiting exclusive
     * mode.
     * The scheduled tasks are sorted according to scheduled time and entrypoint
     * and passed to execution.
     * When the execution of all tasks finishes, the special task, if it still
     * exists, is updated with end time and result, saved in current status
     * and stored in database.
     * Finnaly the method clearing current status is called.
     */
    public function scheduleAndPerform()
    {
        $now = time();

        $uuid = Uuid::create();

        $lockHandle = $this->lock();

        $statusTable = CronStatus::getStatusTable();

        $scheduledSelfTask = new CronScheduledTask($this->selfTask);
        $scheduledSelfTask->setUuid($uuid);
        $scheduledSelfTask->setScheduledTime($now);
        $scheduledSelfTask->setStartTime($now);

        $tasksToPerform = [];
        $totalTaskTtls = 0;
        foreach ($this->tasks as $entryPoint=>$cronTask) {
            //$startTime = $now;

            $previousTasks = CronStatus::getEntryPoint(
                CronCommons::SECTION_TASKS,
                $entryPoint,
                $statusTable
            );

            $minStartTime = 0;
            $isPreviousScheduled = false;
            foreach($previousTasks as $taskUuid => $scheduledTask) {
                if ($scheduledTask instanceof CronScheduledTask) {
                    if (!empty($scheduledTask->getStartTime())) {
                        if (!empty($scheduledTask->getEndTime())) {
                            $taskEnd = $scheduledTask->getEndTime();
                        } elseif (
                            !empty($scheduledTask->getTtl())
                            && $scheduledTask->getTtl() > 0
                        ) {
                            $taskEnd =
                                $scheduledTask->getStartTime()
                                + $scheduledTask->getTtl();
                        } else {
                            $taskEnd = $now + 2 * 60;
                        }

                        if (
                            ($taskEnd - $scheduledTask->getStartTime() > 60)
                            && $minStartTime < $taskEnd
                        ) {
                            $minStartTime = $taskEnd;
                        } elseif (
                            $minStartTime < (
                                $scheduledTask->getStartTime() + 60
                            )
                        ) {
                            $minStartTime =
                                $scheduledTask->getStartTime() + 60;
                        }
                    } else {
                        $isPreviousScheduled =
                            $isPreviousScheduled
                            || !empty($scheduledTask->getScheduledTime());
                    }
                }
            }
            $startTime = ($minStartTime > 0 ? $minStartTime : $now);

            if ($this->includeMissed && $isPreviousScheduled) {
                $startTime = $now + $cronTask->getTtl();
            }
            if (!$this->includeMissed && $startTime < $now) {
                $startTime = $now;
            }
            if ($scheduledTask->getAllowConcurrent() && $startTime > $now) {
                $startTime = $now;
            }

            $canSchedule = true;
            $startTime = floor($startTime/60) * 60;
            for ($t = $startTime; $t <= $now; $t += 60) {
                $currentTimeFields = localtime($t, true);
                foreach(self::TIME_FIELDS as $field => $mapping) {
                    if (is_array($mapping)) {
                        $name = $mapping[0];
                        $adjust = $mapping[1];
                    } else {
                        $name = $mapping;
                        $adjust = 0;
                    }
                    $canSchedule = $cronTask->hasCronValue(
                        $name,
                        $currentTimeFields[$field] + $adjust
                    );
                    if (!$canSchedule) {
                        break;
                    }
                }
                if ($canSchedule) {
                    $scheduledTask = new CronScheduledTask($cronTask);
                    $scheduledTask->setUuid(Uuid::create());
                    $scheduledTask->setSchedulerUuid($uuid);
                    $scheduledTask->setScheduledTime($t);
                    if ($scheduledTask->getTtl() > 0) {
                        $totalTaskTtls += $scheduledTask->getTtl();
                    }
                    $tasksToPerform[] = [ $entryPoint, $scheduledTask ];
                    break;
                }
            }

        }
        if ($totalTaskTtls > $scheduledSelfTask->getTtl()) {
            $scheduledSelfTask->setTtl($totalTaskTtls);
        }
        CronStatus::addScheduledTask(
            CronCommons::SECTION_SPECIALS,
            CronCommons::SCHEDULER_ENTRYPOINT_VALUE,
            $scheduledSelfTask,
            $statusTable
        );

        CronStatus::setStatusTable($statusTable);

        $this->unlock($lockHandle);

        usort($tasksToPerform, function($a, $b) {
            $result = ($a[1])->getScheduledTime() - ($b[1])->getScheduledTime();
            if ($result === 0) {
                $result = strcmp($a[0], $b[0]);
            }
            return $result;
        });

        $this->performTasksSequential($tasksToPerform);

        $lockHandle = $this->lock();

        $now = time();

        $cronHistory = new CronHistory();

        $statusTable = CronStatus::getStatusTable();
        $scheduledSelfTask = CronStatus::getScheduledTask(
            CronCommons::SECTION_SPECIALS,
            CronCommons::SCHEDULER_ENTRYPOINT_VALUE,
            $uuid,
            $statusTable,
            false
        );
        if (
            $scheduledSelfTask != null
            && $scheduledSelfTask instanceof CronScheduledTask
        ) {
            $scheduledSelfTask->setEndTime(time());
            $scheduledSelfTask->setResult(true);

            CronStatus::replaceScheduledTask(
                CronCommons::SECTION_SPECIALS,
                CronCommons::SCHEDULER_ENTRYPOINT_VALUE,
                $scheduledSelfTask,
                $statusTable
            );

            $cronHistory->storeCronTaskInDb(
                CronCommons::SECTION_SPECIALS,
                CronCommons::SCHEDULER_ENTRYPOINT_VALUE,
                $scheduledSelfTask
            );
        }

        $this->clearStatusTable($now, $statusTable, $cronHistory);

        CronStatus::setStatusTable($statusTable);

        $this->unlock($lockHandle);
    }

    /**
     * Performs the scheduled tasks given in parameter one by one. The next task
     * starts when the previous one finishes. Each task has startTime set, then
     * is added to current status and executed in direct or external mode,
     * according to configuration. On execution end the scheduled task, if it
     * still exists in current status, is fulfilled with end time, result, error
     * output and error message, updated in current status and stored in db.
     *
     * @param array $tasksToPerform the ordered tasks to execute
     */
    private function performTasksSequential(array $tasksToPerform)
    {
        foreach ($tasksToPerform as $task)
        {
            list($entryPoint, $scheduledTask) = $task;
            $taskUuid = $scheduledTask->getUuid();

            $lockHandle = $this->lock();

            $statusTable = CronStatus::getStatusTable();

            $scheduledTask->setStartTime(time());
            CronStatus::addScheduledTask(
                CronCommons::SECTION_TASKS,
                $entryPoint,
                $scheduledTask,
                $statusTable
            );
            CronStatus::setStatusTable($statusTable);

            $this->unlock($lockHandle);

            $performResult = null;
            switch($this->executionMode) {
                case CronCommons::EXECUTION_MODE_DIRECT:
                    $performResult = $this->performTaskDirect($entryPoint);
                    break;
                case CronCommons::EXECUTION_MODE_EXTERNAL:
                    $performResult = $this->performTaskExternal(
                        $entryPoint,
                        $taskUuid,
                        $scheduledTask->getTtl()
                    );
                    break;
            }
            list($taskResult, $taskOutput, $taskError, $errorMsg)
                = (
                    ( $performResult !== null )
                    ? $performResult
                    : [ null, null, true, "Perform task failed" ]
                  );

            $lockHandle = $this->lock();

            $statusTable = CronStatus::getStatusTable();
            $scheduledTask = CronStatus::getScheduledTask(
                CronCommons::SECTION_TASKS,
                $entryPoint,
                $taskUuid,
                $statusTable,
                false
            );
            if (
                $scheduledTask != null
                && $scheduledTask instanceof CronScheduledTask
            ) {
                $scheduledTask->setEndTime(time());
                $scheduledTask->setResult($taskResult);
                if (!empty($taskOutput)) {
                    $scheduledTask->setOutput(
                        substr(
                            $taskOutput,
                            0,
                            CronCommons::OUTPUT_MAX_LEN
                        )
                    );
                }
                if ($taskError) {
                    $scheduledTask->setFailed(true);
                    if (!empty($errorMsg)) {
                        $scheduledTask->setErrorMsg($errorMsg);
                    }
                }
                CronStatus::replaceScheduledTask(
                    CronCommons::SECTION_TASKS,
                    $entryPoint,
                    $scheduledTask,
                    $statusTable
                );
                CronStatus::setStatusTable($statusTable);

                (new CronHistory())->storeCronTaskInDb(
                    CronCommons::SECTION_TASKS,
                    $entryPoint,
                    $scheduledTask
                );
            }

            $this->unlock($lockHandle);
        }
    }

    /**
     * Performs the direct execution of entry point. The entry point is called
     * in current process by eval, catching as many errors and exceptions as
     * possible.
     *
     * @param string $entryPoint the entry point to execute
     *
     * @return array the execution results where the first value is the task
     *      result, the second - the task output, the third - the task error
     *      flag and the fourth - the task error message
     */
    private function performTaskDirect($entryPoint)
    {
        $taskResult = null;
        $taskOutput = null;
        $taskError = false;
        $errorMsg = null;
        try {
            ob_start();
            $taskResult = eval('return '.$entryPoint.'();');
            $taskOutput = ob_get_clean();
        } catch (\Throwable $e) {
            /* PHP 7 */
            $taskResult = false;
            $errorMsg = substr(
                $e->getMessage(),
                0,
                CronCommons::ERROR_MSG_MAX_LEN
            );
            $taskError = true;
        } catch (\Exception $e) {
            /* PHP 5 only */
            $taskResult = false;
            $errorMsg = substr(
                $e->getMessage(),
                0,
                CronCommons::ERROR_MSG_MAX_LEN
            );
            $taskError = true;
        }

        return [ $taskResult, $taskOutput, $taskError, $errorMsg ];
    }

    /**
     * Performs the execution of entry point using external wrapper.
     * The (uuid, entry point) pair is put into CronExternalWaitingRoom and then
     * the external wrapper is requested using the server request protocol, host
     * and port. When returned, response headers and contents are read to set
     * adequate result values
     *
     * @param string $entryPoint the entry point to execute
     * @param string $uuid the uuid of executed task
     * @param integer $ttl the ttl of executed task, to set the correct request
     *      timeout
     *
     * @return array the execution results where the first value is the task
     *      result, the second - the task output, the third - the task error
     *      flag and the fourth - the task error message
     */
    private function performTaskExternal($entryPoint, $uuid, $ttl)
    {
        $taskResult = null;
        $taskOutput = null;
        $taskError = false;
        $errorMsg = null;

        try {
            ob_start();
            if (function_exists("stream_get_meta_data")) {
                $isHttps =
                    !(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off');
                $url = ( $isHttps ? "https://" : "http://" )
                    .(
                        !empty($_SERVER['HTTP_HOST'])
                        ? $_SERVER['HTTP_HOST']
                        : $_SERVER['SERVER_NAME']
                    )
                    .(
                        !empty($_SERVER['SERVER_PORT'])
                        ? ":" . $_SERVER['SERVER_PORT']
                        : ""
                    )
                    . CronCommons::WRAPPER_EXEC_URL
                    . "?uuid=" . $uuid
                ;
                CronExternalWaitingRoom::put($uuid, $entryPoint);

                $streamOptions = [
                    'http' => [
                        'method' => 'GET',
                    ]
                ];
                if (!empty($ttl) && $ttl > 0) {
                    $streamOptions['http']['timeout'] = $ttl;
                }
                if ($isHttps) {
                    $streamOptions['ssl'] = [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ];
                }
                $streamContext = stream_context_create($streamOptions);

                $trackErrorsValue = ini_get('track_errors');
                ini_set('track_errors', true);
                $stream = @fopen($url, 'r', false, $streamContext);
                ini_set('track_errors', $trackErrorsValue);
                if ($stream) {
                    $streamMetaData = stream_get_meta_data($stream);
                    if (!empty($streamMetaData["wrapper_data"])) {
                        $resultPreg = preg_quote(
                            CronCommons::WRAPPER_HEADER_RESULT
                        );
                        $errorPreg = preg_quote(
                            CronCommons::WRAPPER_HEADER_ERROR
                        );
                        foreach ($streamMetaData["wrapper_data"] as $line) {
                            if (
                                preg_match(
                                    '/^' . $resultPreg. ':\s*(\w+)/i',
                                    $line,
                                    $matches
                                )
                            ) {
                                switch($matches[1]) {
                                    case "true": $taskResult = true; break;
                                    case "false": $taskResult = false; break;
                                    default: $taskResult = null;
                                }
                            } elseif (
                                preg_match(
                                    '/^' . $errorPreg . ':\s*true/i',
                                    $line
                                )
                            ) {
                                $taskResult = false;
                                $taskError = true;
                            }
                        }
                    }
                    if ($taskError) {
                        $errorMsg = substr(
                            stream_get_contents($stream),
                            0,
                            CronCommons::ERROR_MSG_MAX_LEN
                        );
                    } else {
                        $taskOutput = stream_get_contents($stream);
                    }
                } else {
                    throw new \RuntimeException($php_errormsg);
                }
            }
            print ob_get_clean();
        } catch (\Throwable $e) {
            /* PHP 7 */
            $taskResult = false;
            $taskError = true;
            $errorMsg = substr(
                $e->getMessage(),
                0,
                CronCommons::ERROR_MSG_MAX_LEN
            );
        } catch (\Exception $e) {
            /* PHP 5 only */
            $taskResult = false;
            $taskError = true;
            $errorMsg = substr(
                $e->getMessage(),
                0,
                CronCommons::ERROR_MSG_MAX_LEN
            );
        }

        return [ $taskResult, $taskOutput, $taskError, $errorMsg ];
    }

    /**
     * Clears the current status passed as a parameter, finishing tasks which
     * exceeded their ttls and removing previous finished tasks, all but the
     * most recent per entry point.
     *
     * @param integer now current time in epoch seconds
     * @param array $statusTable the current status to clear
     * @param CronHistory $cronHistory the DAO instance to store the timeouted
     *      tasks to
     */
    private function clearStatusTable($now, &$statusTable, $cronHistory)
    {
        $specials = $this->clearStatusTableSection(
            $now,
            $statusTable,
            $cronHistory,
            CronCommons::SECTION_SPECIALS,
            true,
            null
        );
        $statusTable[CronCommons::SECTION_SPECIALS] = $specials;

        $schedulerUuids = [];
        if (isset($specials[CronCommons::SCHEDULER_ENTRYPOINT_VALUE])) {
            foreach (
                $specials[CronCommons::SCHEDULER_ENTRYPOINT_VALUE]
                as $uuid => $scheduledTask
            ) {
                $schedulerUuids[$uuid] = true;
            }
        }

        $tasks = $this->clearStatusTableSection(
            $now,
            $statusTable,
            $cronHistory,
            CronCommons::SECTION_TASKS,
            false,
            $schedulerUuids
        );
        $statusTable[CronCommons::SECTION_TASKS] = $tasks;
    }

    /**
     * Clears the section in current status passed as a parameter.
     *
     * @param integer now current time in epoch seconds
     * @param array $statusTable the current status to get section from
     * @param CronHistory $cronHistory the DAO instance to store the timeouted
     *      tasks to
     * @param string $sectionName the name of section to clear up
     * @param boolean $isSpecial true if the section is a special/system one
     * @param array $schedulerUuids the current running scheduler tasks uuids,
     *      variable not used in special section
     *
     * @return array the cleared up section
     */
    private function clearStatusTableSection(
        $now,
        $statusTable,
        $cronHistory,
        $sectionName,
        $isSpecial,
        $schedulerUuids
    ) {
        $section = CronStatus::getSection(
            $sectionName,
            $statusTable,
            false
        );
        if (!empty($section)) {
            foreach ($section as $entryPoint => $entryPointTasks) {
                $updatedEntryPointTasks = $this->clearStatusTableEntryPoint(
                    $now,
                    $cronHistory,
                    $sectionName,
                    $entryPoint,
                    $entryPointTasks,
                    $isSpecial,
                    $schedulerUuids
                );
                $section[$entryPoint] = $updatedEntryPointTasks;
            }
        }
        return $section;
    }

    /**
     * Clears the entrypoint in current status passed as a parameter.
     *
     * @param integer now current time in epoch seconds
     * @param CronHistory $cronHistory the DAO instance to store the timeouted
     *      tasks to
     * @param string $sectionName the name of section where the entry point
     *      belongs
     * @param string $entryPoint the name of entry point to clear up
     * @param array $entryPointTasks the tasks in current status belonging to
     *      given entry point
     * @param boolean $isSpecial true if the section is a special/system one
     * @param array $schedulerUuids the current running scheduler tasks uuids,
     *      variable not used in special section
     *
     * @return array the cleared up entry point tasks
     */
    private function clearStatusTableEntryPoint(
        $now,
        $cronHistory,
        $sectionName,
        $entryPoint,
        $entryPointTasks,
        $isSpecial,
        $schedulerUuids
    ) {
        $lastEnded = 0;
        foreach ($entryPointTasks as $uuid => $scheduledTask) {
            if ($scheduledTask instanceof CronScheduledTask) {
                if (
                    !empty($scheduledTask->getEndTime())
                    && $scheduledTask->getEndTime() > $lastEnded
                ) {
                    $lastEnded = $scheduledTask->getEndTime();
                }
            }
        }
        foreach ($entryPointTasks as $uuid => $scheduledTask) {
            if ($scheduledTask instanceof CronScheduledTask) {
                if (
                    empty($scheduledTask->getScheduledTime())
                    || (
                            !empty($scheduledTask->getEndTime())
                            && $scheduledTask->getEndTime() < $lastEnded
                       )
                    || (
                            empty($scheduledTask->getStartTime())
                            && !empty($schedulerUuids)
                            && !empty($scheduledTask->getSchedulerUuid())
                            && empty($schedulerUuids[
                                $scheduledTask->getSchedulerUuid()
                            ])
                       )
                ) {
                    unset($entryPointTasks[$uuid]);
                } elseif (
                    empty($scheduledTask->getEndTime())
                    && !empty($scheduledTask->getStartTime())
                    && !empty($scheduledTask->getTtl())
                    && $scheduledTask->getTtl() > 0
                    && (
                            $scheduledTask->getStartTime()
                            + $scheduledTask->getTtl()
                            < $now
                        )
                ) {
                    $scheduledTask->setResult(false);
                    $scheduledTask->setFailed(true);
                    $scheduledTask->setErrorMsg(
                        "{{cron_timeouted}} ("
                        . $scheduledTask->getTtl()
                        . ")"
                    );
                    $scheduledTask->setTranslateErrorMsg(true);
                    $cronHistory->storeCronTaskInDb(
                        $sectionName,
                        $entryPoint,
                        $scheduledTask,
                        $isSpecial
                    );
                    unset($entryPointTasks[$uuid]);
                }
            }
        }
        return $entryPointTasks;
    }
}