<?php
namespace Controllers\Admin;

use Controllers\BaseController;
use lib\Objects\OcConfig\OcConfig;
use Utils\Uri\Uri;
use lib\Objects\Cron\CronCommons;
use Utils\Cron\CronConfigurator;
use lib\Objects\Cron\CronTask;
use lib\Objects\Cron\CronScheduledTask;
use lib\Objects\Cron\CronStatus;
use lib\Objects\Cron\CronHistory;

class CronStatusController extends BaseController
{
    private $maxHistory;
    private $ttl;
    private $dateTimeFormat;
    private $refreshInterval;
    private $displayHistory;
    
    private $cronHistory;
    
    private $configTasks;
    
    const TIME_FIELDS = [
        "tm_min" => CronCommons::MINUTE,
        "tm_hour" => CronCommons::HOUR,
        "tm_mday" => CronCommons::DAY,
        "tm_mon" => [ CronCommons::MONTH, 1 ],
        "tm_wday" => CronCommons::WEEKDAY
    ];
    
    public function __construct()
    {
        parent::__construct();
        
        $config = OcConfig::instance()->getCronConfig();
        if ($config == null) {
            throw new \RuntimeException("Cron config not found");
        }
        
        foreach(CronConfigurator::configureCommons($config) as $key=>$value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        
        $this->configTasks = CronConfigurator::configureTasks(
            $config,
            $this->ttl,
            $this->maxHistory
        );
        
        $this->cronHistory = new CronHistory();
    }

    public function isCallableFromRouter($actionName)
    {
        return $this->loggedUser->isAdmin();
    }
    
    public function index($userId = null)
    {
        $this->view->addLocalCss(Uri::getLinkWithModificationTime(
            '/tpl/stdstyle/admin/cron/cron.css'
        ));
        $this->view->setVar("dateTimeFormat", $this->dateTimeFormat);
        $this->view->setVar("refreshInterval", $this->refreshInterval);
        $this->view->loadJQuery();
        $this->view->loadVueJs();
        $this->view->addLocalJs('/tpl/stdstyle/js/moment-with-locales.min.js');
        $this->view->setTemplate('admin/cron/cron');
        $this->view->buildView();
    }
    
    public function getStatus()
    {
        $result = [ "sections" => [] ];
        foreach (
            [
                CronCommons::SECTION_SPECIALS,
                CronCommons::SECTION_TASKS
            ]
            as $sectionName
        ) {
            $this->updateSectionEntryPoints(
                $result,
                $sectionName,
                CronStatus::getSection($sectionName, $statusTable, false),
                true
            );
            $historyTasks = $this->cronHistory->getTasks(
                    $sectionName,
                    $this->displayHistory
            );
            $this->updateSectionEntryPoints(
                $result,
                $sectionName,
                (
                    isset($historyTasks[$sectionName])
                    ? $historyTasks[$sectionName]
                    : []
                ),
                false
            );
        }
        $result["summaries"] = $this->cronHistory->getSummary();
        header('Content-Type: application/json');
        print json_encode($result);
        exit();
    }
    
    private function updateSectionEntryPoints(
        &$result,
        $sectionName,
        $section,
        $isCurrent
    ) {
        if (!empty($section) && is_array($section)) {
            $sectionEntryPoints =
                isset($result["sections"][$sectionName])
                ? $result["sections"][$sectionName]
                : []
            ;
            foreach ($section as $entryPoint => $entryPointTasks) {
                if (empty($sectionEntryPoints[$entryPoint]["info"])) {
                    $sectionEntryPoints[$entryPoint]["info"] = 
                        $this->getEntryPointInfo($entryPoint);
                }
                $tasks = [];
                foreach ($entryPointTasks as $uuid => $scheduledTask) {
                    $task = [
                        'scheduled_time' => $scheduledTask->getScheduledTime(),
                        'start_time' => $scheduledTask->getStartTime(),
                        'end_time' => $scheduledTask->getEndTime()
                    ];
                    if (!$isCurrent) {
                        $task['ttl'] = $scheduledTask->getTtl();
                        $task['result'] = $scheduledTask->getResult();
                        $task['failed'] = $scheduledTask->getFailed();
                        $task['output'] = $scheduledTask->getOutput();
                        $task['error_msg'] = $scheduledTask->getErrorMsg();
                    }
                    $tasks[$uuid] = $task;
                }
                $sectionEntryPoints[$entryPoint][
                    $isCurrent ? "current" : "history"
                ] = $tasks;
            }
            $result["sections"][$sectionName] = $sectionEntryPoints;
        }
    }
    
    private function getEntryPointInfo($entryPoint)
    {
        $result =[];
        if ($entryPoint == CronCommons::SCHEDULER_ENTRYPOINT_VALUE) {
            $result["description"] = CronCommons::SCHEDULER_ENTRYPOINT_VALUE;
            $result["maxHistory"] = $this->maxHistory;
            $result["ttl"] = $this->ttl;
        } elseif (!empty($this->configTasks[$entryPoint])) {
            $configTask = $this->configTasks[$entryPoint];
            $result["cronString"] = $configTask->getCronString();
            $result["description"] = $configTask->getDescription();
            $result["maxHistory"] = $configTask->getMaxHistory();
            $result["ttl"] = $configTask->getTtl();
        }
        return $result;
    }
}