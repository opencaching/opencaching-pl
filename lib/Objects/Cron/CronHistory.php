<?php

namespace lib\Objects\Cron;

use lib\Objects\BaseObject;
use Utils\Database\OcDb;

class CronHistory extends BaseObject
{
    /**
     * Calls parent constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->db->setAttribute(OcDb::ATTR_EMULATE_PREPARES, false);
    }
    
    public function storeCronTaskInDb($section, $entryPoint, $scheduledTask)
    {
        $failed = $scheduledTask->getFailed();
        $this->db->multiVariableQuery(
            'INSERT IGNORE INTO `cron_history` (
                `section`, `entrypoint`, `uuid`,`ttl`,
                `scheduled_time`, `start_time`, `end_time`,
                `result`, `output`, `failed`, `error_msg`,
                `last_modified`
             ) VALUES (
                :1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11, NOW()
             )',
            $section,
            $entryPoint,
            $scheduledTask->getUuid(),
            $scheduledTask->getTtl(),
            $scheduledTask->getScheduledTime(),
            $scheduledTask->getStartTime(),
            $scheduledTask->getEndTime(),
            $scheduledTask->getResult(),
            $scheduledTask->getOutput(),
            isset($failed) ? boolval($failed) : false,
            $scheduledTask->getErrorMsg()
        );
        
        if ($scheduledTask->getMaxHistory() > 0) {
            $this->db->multiVariableQuery(
                'DELETE FROM `cron_history`
                 WHERE `section` = :1 AND `entrypoint` = :2 AND `uuid` NOT IN (
                    SELECT * FROM (
                        SELECT `uuid` FROM `cron_history`
                        WHERE `section` = :3 AND `entrypoint` = :4
                        ORDER BY `last_modified`
                        LIMIT :5
                    ) ch2
                 )',
                $section,
                $entryPoint,
                $section,
                $entryPoint,
                $scheduledTask->getMaxHistory()
            );
        }
    }
    
    public function getTasks(
        $sectionName = null,
        $limitPerEntryPoint = 0,
        $recentThan = null)
    {
        $result = [];
        
        foreach ($this->getSectionEntryPoints($sectionName) as $row) {
            list($section, $entryPoint) = $row;
            $params = [ $section, $entryPoint ];
            if (!empty($recentThan)) {
                $params[] = $recentThan;
            }
            if ($limitPerEntryPoint > 0) {
                $params[] = $limitPerEntryPoint;
            }
            $pn = 1;
            $epStmt = $this->db->multiVariableQuery(
                'SELECT * FROM `cron_history` WHERE '
                . '`section` = :' . ($pn++) . ' AND `entrypoint` = :' . ($pn++)
                . (
                    !empty($recentThan)
                    ? ' AND `last_modified` > :' . ($pn++)
                    : ''
                )
                . ' ORDER BY last_modified DESC'
                . ($limitPerEntryPoint > 0 ? ' LIMIT :' . ($pn++) : ''),
                $params
            );
            foreach(
                $this->db->dbResultFetchAll($epStmt, OcDb::FETCH_ASSOC) as $epRow
            ) {
                $scheduledTask = new CronScheduledTask();
                $scheduledTask->setUuid($epRow['uuid']);
                $scheduledTask->setScheduledTime($epRow['scheduled_time']);
                $scheduledTask->setStartTime($epRow['start_time']);
                $scheduledTask->setEndTime($epRow['end_time']);
                $scheduledTask->setTtl($epRow['ttl']);
                $scheduledTask->setResult($epRow['result']);
                $scheduledTask->setOutput($epRow['output']);
                $scheduledTask->setFailed($epRow['failed']);
                $scheduledTask->setErrorMsg($epRow['error_msg']);
                $result[$section][$entryPoint][$epRow['uuid']] = $scheduledTask;
            }
        }
        
        return $result;
    }
    
    public function getSummary($sectionName = null)
    {
        $result = [];
        
        foreach ($this->fetchSummaryRows($sectionName) as $row) {
            $result[$row['section']]['summary'] = $this->getSummaryFields($row);
        }
        foreach ($this->fetchSummaryRows($sectionName, true) as $row) {
            $result[$row['section']]['entrypoints'][$row['entrypoint']] = 
                $this->getSummaryFields($row);
        }
        
        return $result;
    }
    
    private function getSectionEntryPoints($sectionName)
    {
        $params = (!empty($sectionName) ? [ $sectionName ]: []);
        $stmt = $this->db->multiVariableQuery(
            'SELECT section, entrypoint FROM `cron_history`'
            . (!empty($sectionName) ? ' WHERE `section` = :1' : '')
            . ' GROUP BY section, entrypoint ORDER BY last_modified DESC',
            $params
        );
        return $this->db->dbResultFetchAll($stmt, OcDb::FETCH_NUM);
    }
    
    private function fetchSummaryRows($sectionName, $entryPointsIncluded = false)
    {
        $result = [];
        
        $stmt = $this->db->multiVariableQuery(
            'SELECT
                section,'
            . ( !empty($entryPointsIncluded) ? ' entrypoint,' : '' )
            . ' COUNT(*) tasks,
                COUNT(CASE result WHEN 1 THEN 1 ELSE NULL END) successes,
                COUNT(CASE result WHEN 0 THEN 1 ELSE NULL END) failures,
                COUNT(CASE result WHEN 0 THEN NULL WHEN 1 THEN NULL ELSE 1 END)
                    unknowns,
                COUNT(CASE failed WHEN 1 THEN 1 ELSE NULL END) errors,
                MIN(ABS(end_time-start_time)) duration_min,
                MAX(ABS(end_time-start_time)) duration_max,
                AVG(ABS(end_time-start_time)) duration_avg
             FROM cron_history'
            . ( !empty($sectionName) ? ' WHERE section = :1' : '' )
            . ' GROUP BY section'
            . ( !empty($entryPointsIncluded) ? ', entrypoint' : '' ),
            (!empty($sectionName) ? [ $sectionName ]: [])
        );
        return $this->db->dbResultFetchAll($stmt, OcDb::FETCH_ASSOC);
    }
    
    private function getSummaryFields($row)
    {
        $result = [];
        foreach (
            [ 
                'tasks', 'successes', 'failures', 'unknowns', 'errors',
                'duration_min', 'duration_max', 'duration_avg'
            ]
            as $field
        ) {
            $result[$field] = $row[$field];
        }
        return $result;
    }
}