<?php
/**
 * Contains \lib\Objects\Cron\CronHistory class definition
 */
namespace lib\Objects\Cron;

use lib\Objects\BaseObject;
use Utils\Database\OcDb;

/**
 * DAO for storing and retrieving historical (ended) cron tasks in database and
 * getting computed summaries
 */
class CronHistory extends BaseObject
{
    /**
     * Calls parent constructor
     */
    public function __construct()
    {
        parent::__construct();
        // needed for DELETE subquery
        $this->db->setAttribute(OcDb::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * Stores given CronScheduledTask in database and assures the number of
     * stored entries do not exceed task max history value, deleting the least
     * recent ones.
     *
     * @param string $scetion the name of section where the task belongs
     * @param string $entryPoint the name of entry point associated with task
     * @param CronScheduledTask $scheduledTask the task to store
     */
    public function storeCronTaskInDb($section, $entryPoint, $scheduledTask)
    {
        $failed = $scheduledTask->getFailed();
        $this->db->multiVariableQuery(
            'INSERT IGNORE INTO `cron_history` (
                `section`, `entrypoint`, `uuid`,`scheduler_uuid`,`ttl`,
                `scheduled_time`, `start_time`, `end_time`,
                `result`, `output`, `failed`, `error_msg`,
                `last_modified`
             ) VALUES (
                :1, :2, :3, :4, :5, :6, :7, :8, :9, :10, :11, :12, NOW()
             )',
            $section,
            $entryPoint,
            $scheduledTask->getUuid(),
            $scheduledTask->getSchedulerUuid(),
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
                        ORDER BY `last_modified` DESC
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

    /**
     * Retrieves from database tasks belonging to given section (or all),
     * limiting the retrievied entries to given number of most recent per
     * entry point.
     *
     * @param string $sectionName the name of section to retrieve the tasks from,
     *      if null (empty) the tasks from every section will be retrieved
     * @param integer $limitPerEntryPoint the maximum number of most recent tasks
     *      per entry point which will be retrieved, if 0 or below - no limit is
     *      applied
     * @param string $recentThan datetime in db compliant format, if set only
     *      entries with last_modified greater than the value will be retrieved
     *
     * @return array the CronScheduledTask instances created using the entries
     *      retrieved
     */
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
                $scheduledTask->setSchedulerUuid($epRow['scheduler_uuid']);
                $scheduledTask->setScheduledTime($epRow['scheduled_time']);
                $scheduledTask->setStartTime($epRow['start_time']);
                $scheduledTask->setEndTime($epRow['end_time']);
                $scheduledTask->setTtl($epRow['ttl']);
                $scheduledTask->setResult($epRow['result']);
                $scheduledTask->setOutput($epRow['output']);
                $scheduledTask->setTranslateOutput($epRow['translate_output']);
                $scheduledTask->setFailed($epRow['failed']);
                $scheduledTask->setErrorMsg($epRow['error_msg']);
                $scheduledTask->setTranslateErrorMsg(
                    $epRow['translate_error_msg']
                );
                $result[$section][$entryPoint][$epRow['uuid']] = $scheduledTask;
            }
        }

        return $result;
    }

    /**
     * Retrieves summaries for given section (or all) computed using database
     * aggregate functions;
     *
     * @param string $sectionName the name of section to retrieve summary for,
     *      if empty all sections will be included
     *
     * @return array the summaries, where for each section name there is
     *      'summary' part containing overall summary and 'entrypoints' part
     *       containing summaries for each entrypoint of the section
     */
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

    /**
     * Retrieves the entrypoints belonging to given section (or all)
     *
     * @param string $sectionName the name of section to retrieve entry points
     *       for, if empty every entry points in database will be included
     *
     * @return array the indexed array of pairs (section name, entry point)
     */
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

    /**
     * Retrieves from database the computed summary for given parameters
     *
     * @param string $sectionName the name of section to retrieve summary for
     *       if empty every section in database will be included
     * @param boolean $entryPointsIncluded if true, summaries will be computed
     *      per each entrypoint of section, otherwise the results will be
     *      computed for the whole section
     * @return array the associated array of summary items
     */
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

    /**
     * Writes selected summary items from the database row to result array
     *
     * @param array $row the database row
     *
     * @return array the result array containing only selected fields
     */
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