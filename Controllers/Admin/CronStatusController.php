<?php
/**
 * Contains \Controllers\Admin\CronStatusController class definition
 */
namespace Controllers\Admin;

use Controllers\BaseController;
use lib\Objects\OcConfig\OcConfig;
use lib\Objects\ApplicationContainer;
use Utils\Uri\Uri;
use lib\Objects\Cron\CronCommons;
use Utils\Cron\CronConfigurator;
use lib\Objects\Cron\CronTask;
use lib\Objects\Cron\CronScheduledTask;
use lib\Objects\Cron\CronStatus;
use lib\Objects\Cron\CronHistory;

/**
 * Used for displaying status of tasks run periodically by scheduler.
 */
class CronStatusController extends BaseController
{
    /**
     * @var boolean true if another task of given entry point can start while
     *      while the previous one has not finished work; used only for tasks
     *      configuration retrieving.
     */
    private $allowConcurrent;
    /** 
     * @var integer Maximum historical entries stored in database;
     */
    private $maxHistory;
    /** 
     * @var integer Time in seconds the task is treated as finished/timeouted;
     */
    private $ttl;
    /** 
     * @var string Date and time format compliant with moment.js; used for
     *      displaying the precise timing of tasks start and end
     */
    private $dateTimeFormat;
    /** 
     * @var integer the number of seconds the display is automatically refreshed
     *      after
     */
    private $refreshInterval;
    /** 
     * @var integer the number of most recent tasks per entry point retrieved
     *      from database and displayed in output
     */
    private $displayHistory;
    /** @var CronHistory DAO instance to retrieve the history from */
    private $cronHistory;
    /** @var array the tasks being defined in configuration */
    private $configTasks;
    /** @var string scheduler entrypoint translated to current language */
    private $schedulerEPTranslated;
    /** @var array translations in current language used in display */
    private $cronDisplayTr;
    
    /** prefix of translation keys needed for display */
    const D_PREFIX = 'cron_display_';
    
    /**
     * Calls parent constructor, sets common attributes from config as well as
     * tasks, instatiates CronHistory and translations
     */
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
            $this->maxHistory,
            $this->allowConcurrent
        );
        
        $this->cronHistory = new CronHistory();
        
        $this->schedulerEPTranslated = tr('cron_scheduler_entrypoint');
        
        global $language;
        $this->cronDisplayTr =
            array_filter(
                $language[(ApplicationContainer::Instance())->getLang()],
                function ($key) {
                    return (substr($key, 0, strlen(self::D_PREFIX))
                                === self::D_PREFIX);
                },
                ARRAY_FILTER_USE_KEY
            );
    }

    /**
     * Available only for logged in users
     *
     * @return true if the user is logged in
     */
    public function isCallableFromRouter($actionName)
    {
        return $this->loggedUser != null;
    }
    
    /**
     * Sets appropriate template and view variables and calls buildView
     */
    public function index($userId = null)
    {
        $this->view->addLocalCss(Uri::getLinkWithModificationTime(
            '/tpl/stdstyle/admin/cron/cron.css'
        ));
        $this->view->setVar("dateTimeFormat", $this->dateTimeFormat);
        $this->view->setVar("refreshInterval", $this->refreshInterval);
        $this->view->setVar("trPrefixLen", strlen(self::D_PREFIX));
        $this->view->setVar("trs", $this->cronDisplayTr);
        $this->view->loadJQuery();
        $this->view->loadVueJs();
        $this->view->addLocalJs('/tpl/stdstyle/js/moment-with-locales.min.js');
        $this->view->setTemplate('admin/cron/cron');
        $this->view->buildView();
    }
    
    /**
     * Called dynamically from javascript. Loads current status, history data
     * and summaries and prints them as json with main entries of "sections"
     * and "summaries"
     */
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
        $summaries = $this->cronHistory->getSummary();
        if (
            !empty(
                $summaries[CronCommons::SECTION_SPECIALS]['entrypoints']
                    [CronCommons::SCHEDULER_ENTRYPOINT_VALUE]
            )
        ) {
            $summaries[CronCommons::SECTION_SPECIALS]['entrypoints']
                [$this->schedulerEPTranslated] = 
            $summaries[CronCommons::SECTION_SPECIALS]['entrypoints']
                [CronCommons::SCHEDULER_ENTRYPOINT_VALUE];
            unset(
                $summaries[CronCommons::SECTION_SPECIALS]['entrypoints']
                    [CronCommons::SCHEDULER_ENTRYPOINT_VALUE]
            );
        }
        $result["summaries"] = $summaries;
        
        header('Content-Type: application/json');
        print json_encode($result);
        exit();
    }
    
    /**
     * Updates resulting array adding entry points to given section
     *
     * @param array $result the resulting array to update
     * @param string $sectionName the name of section to update
     * @param array $section the section data containing entry points
     * @param boolean $isCurrent true if the section contains current status data
     */
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
                $newEntryPoint = $entryPoint;
                if (
                    $sectionName == CronCommons::SECTION_SPECIALS
                    && $entryPoint == CronCommons::SCHEDULER_ENTRYPOINT_VALUE
                ) {
                    $newEntryPoint = $this->schedulerEPTranslated;
                }
                if (empty($sectionEntryPoints[$newEntryPoint]["info"])) {
                    $sectionEntryPoints[$newEntryPoint]["info"] = 
                        $this->getEntryPointInfo($entryPoint);
                }
                $displayName = (
                    !empty(
                        $sectionEntryPoints[$newEntryPoint]["info"]
                                        ["displayName"]
                    )
                    ? $sectionEntryPoints[$newEntryPoint]["info"]["displayName"]
                    : ''
                );
                $tasks = [];
                foreach ($entryPointTasks as $uuid => $scheduledTask) {
                    $task = [
                        'display_name' => $displayName,    
                        'scheduled_time' => $scheduledTask->getScheduledTime(),
                        'start_time' => $scheduledTask->getStartTime(),
                        'end_time' => $scheduledTask->getEndTime()
                    ];
                    if (!$isCurrent) {
                        $task['ttl'] = $scheduledTask->getTtl();
                        $task['result'] = $scheduledTask->getResult();
                        $task['failed'] = $scheduledTask->getFailed();
                        $task['output'] = 
                            $scheduledTask->getTranslateOutput()
                            ? $this->getTranslated($scheduledTask->getOutput())
                            : $scheduledTask->getOutput()
                        ;
                        $task['error_msg'] = 
                            $scheduledTask->getTranslateErrorMsg()
                            ? $this->getTranslated($scheduledTask->getErrorMsg())
                            : $scheduledTask->getErrorMsg()
                        ;
                    }
                    $tasks[$uuid] = $task;
                }
                $sectionEntryPoints[$newEntryPoint][
                    $isCurrent ? "current" : "history"
                ] = $tasks;
            }
            $result["sections"][$sectionName] = $sectionEntryPoints;
        }
    }
    
    /**
     * Translates input text changing double braced keys to corresponding
     * translations
     *
     * @param string $input the string to translate
     *
     * @return string the translated input
     */
    private function getTranslated($input)
    {
        $result = $input;
        if (preg_match_all("/{{[^}]*}}/u", $result, $matches)) {
            foreach ($matches[0] as $trExpr) {
                $trKey = substr($trExpr, 2, -2);
                $result = preg_replace("/$trExpr/u", tr($trKey), $result);
            }
        }
        return $result;
    }
    
    /**
     * Fills up the entry point information with data from configuration
     *
     * @param string $entryPoint the entry point to fill the infomation up
     *
     * @return array the array containing info fields
     */
    private function getEntryPointInfo($entryPoint)
    {
        $result =[];
        if ($entryPoint == CronCommons::SCHEDULER_ENTRYPOINT_VALUE) {
            $result["description"] = tr('cron_scheduler_description');
            $result["maxHistory"] = $this->maxHistory;
            $result["ttl"] = $this->ttl;
        } elseif (!empty($this->configTasks[$entryPoint])) {
            $configTask = $this->configTasks[$entryPoint];
            $result["cronString"] = $configTask->getCronString();
            $result["displayName"] = $configTask->getDisplayName();
            $result["description"] = $configTask->getDescription();
            $result["maxHistory"] = $configTask->getMaxHistory();
            $result["ttl"] = $configTask->getTtl();
        }
        return $result;
    }
}