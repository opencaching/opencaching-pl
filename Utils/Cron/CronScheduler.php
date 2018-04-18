<?php

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

final class CronScheduler
{
    const TIME_FIELDS = [
        "tm_min" => CronCommons::MINUTE,
        "tm_hour" => CronCommons::HOUR,
        "tm_mday" => CronCommons::DAY,
        "tm_mon" => [ CronCommons::MONTH, 1 ],
        "tm_wday" => CronCommons::WEEKDAY
    ];
    
    private $maxHistory;
    private $ttl;
    private $includeMissed;
    private $allowConcurrent;
    
    private $selfTask;
    private $tasks;
    private $executionMode;
    
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
            "CronScheduler special task",
            $this->ttl,
            $this->maxHistory
        );
        
        $this->tasks = CronConfigurator::configureTasks(
            $config,
            $this->ttl,
            $this->maxHistory
        );
    }

    private function lock() {
        $lockHandle = Lock::tryLock($this, Lock::EXCLUSIVE);
        if (! $lockHandle) {
            throw new \RuntimeException("Cannot obtain lock");
        }
        return $lockHandle;
    }
    
    private function unlock($lockHandle) {
        Lock::unlock($lockHandle);
    }
    
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
            foreach($previousTasks as $taskUuid => $scheduledTask) {
                if (
                    $scheduledTask instanceof CronScheduledTask
                    && !empty($scheduledTask->getStartTime())
                ) {
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
                    
                }
            }
            $startTime = ($minStartTime > 0 ? $minStartTime : $now);
            
            if (!$this->includeMissed && $startTime < $now) {
                $startTime = $now;
            }
            if ($this->allowConcurrent && $startTime > $now) {
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
                if (isset($taskResult) && $taskResult !== null) {
                    $scheduledTask->setResult(boolval($taskResult) ? 1 : 0);
                } else {
                    $scheduledTask->setResult(2);
                }
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
    
    private function clearStatusTable($now, &$statusTable, $cronHistory)
    {
        $specials = $this->clearStatusTableSection(
            $now,
            $statusTable,
            $cronHistory,
            CronCommons::SECTION_SPECIALS,
            true
        );
        $statusTable[CronCommons::SECTION_SPECIALS] = $specials;
        
        $tasks = $this->clearStatusTableSection(
            $now,
            $statusTable,
            $cronHistory,
            CronCommons::SECTION_TASKS,
            false
        );
        $statusTable[CronCommons::SECTION_TASKS] = $tasks;
    }
    
    private function clearStatusTableSection(
        $now,
        $statusTable,
        $cronHistory,
        $sectionName,
        $isSpecial
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
                    $isSpecial
                );
                $section[$entryPoint] = $updatedEntryPointTasks;
            }
        }
        return $section;
    }
    
    private function clearStatusTableEntryPoint(
        $now,
        $cronHistory,
        $sectionName,
        $entryPoint,
        $entryPointTasks,
        $isSpecial
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
                        "Timeouted, exceeded TTL seconds = "
                        . $scheduledTask->getTtl()
                    );
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