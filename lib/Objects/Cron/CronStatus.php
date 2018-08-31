<?php
/**
 * Contains \lib\Objects\Cron\CronStatus class definition
 */
namespace lib\Objects\Cron;

use lib\Objects\Cron\CronCommons;
use lib\Controllers\Php7Handler;

/**
 * Used for saving and retrieving current cron status or its elements from
 * apc(u) cache
 */
final class CronStatus
{
    /** Apc(u) key identifying status value  */
    const APC_KEY = "cc3bdf0f1ee7a7579ac46661dce46970d606ebc40812f0f25d6e88a8";

    /**
     * Gives the whole status stored in cache
     *
     * @param boolean $autoCreate the flag marking if new array should be
     *      created if not found
     *
     * @return array the status table stored in cache, if not found and
     *      $autoCreate, empty array is returned instead of null
     */
    public static function getStatusTable($autoCreate = true)
    {
        $result = Php7Handler::apc_fetch(self::APC_KEY);
        if ($autoCreate && empty($result)) {
            $result = [];
        }
        return $result;
    }

    /**
     * Gives the particular status section
     *
     * @param string $sectionName the name of section to retrieve
     * @param array $statusTable the status to retrieve the section from
     *      or create it within; if null, the status is retreived from cache
     * @param boolean $autoCreate the flag marking if new section should be
     *      created if not found
     *
     * @return array the section stored in cache, if not found and
     *      $autoCreate, empty array is returned instead of null and it is
     *      added to status array
     */
    public static function getSection(
        $sectionName,
        &$statusTable = null,
        $autoCreate = true
    ) {
        $result = null;
        if (!is_array($statusTable)) {
            $statusTable = self::getStatusTable($autoCreate);
        }
        if (is_array($statusTable)) {
            $result =
                isset($statusTable[$sectionName])
                ? $statusTable[$sectionName]
                : null;
            if ($autoCreate && !is_array($result)) {
                $result = [];
                $statusTable[$sectionName] = $result;
            }
        }
        return $result;
    }

    /**
     * Gives the particular status entry point
     *
     * @param string $sectionName the name of section to retrieve the entry point
     *      from
     * @param string $entryPoint the name of entry point to retrieve
     * @param array $statusTable the status to retrieve the entry point from
     *      or create it within; if null, the status is retreived from cache
     * @param boolean $autoCreate the flag marking if new entry point should be
     *      created if not found
     *
     * @return array the entry point stored in cache, if not found and
     *      $autoCreate, empty array is returned instead of null and it is
     *      added to status array under given section
     */
    public static function getEntryPoint(
        $sectionName,
        $entryPoint,
        &$statusTable = null,
        $autoCreate = true
    ) {
        $result = null;
        $section = self::getSection($sectionName, $statusTable, $autoCreate);
        if (is_array($section)) {
            $result =
                isset($section[$entryPoint])
                ? $section[$entryPoint]
                : null;
            ;
            if ($autoCreate && !is_array($result)) {
                $result = [];
                $statusTable[$sectionName][$entryPoint] = $result;
            }
        }
        return $result;
    }

    /**
     * Gives the particular scheduled task
     *
     * @param string $sectionName the name of section to retrieve the task from
     * @param string $entryPoint the name of entry point to retrieve the task from
     * @param string $uuid the uuid of secheduled task to retrieve
     * @param array $statusTable the status to retrieve the entry point from
     *      or create it within; if null, the status is retreived from cache
     * @param boolean $autoCreate the flag marking if new entry point should be
     *      created if not found
     *
     * @return array the entry point stored in cache, if not found and
     *      $autoCreate, empty array is returned instead of null and it is
     *      added to status array under given section
     */
    public static function getScheduledTask(
        $sectionName,
        $entryPoint,
        $uuid,
        &$statusTable = null,
        $autoCreate = true
    ) {
        $result = null;
        $entryPointTasks = self::getEntryPoint(
            $sectionName,
            $entryPoint,
            $statusTable,
            $autoCreate
        );
        if (is_array($entryPointTasks)) {
            $result =
                isset($entryPointTasks[$uuid])
                ? $entryPointTasks[$uuid]
                : null;
        }
        return $result;
    }

    /**
     * Adds a scheduled task to given section and entry point
     *
     * @param string $sectionName the name of section to add the task to
     * @param string $entryPoint the name of entry point to add the task to
     * @param CronScheduledTask $scheduledTask the task to add
     * @param array $statusTable the status to add the task to;
     *      if null, the status is retreived from cache
     * @param boolean $replace if true the task existing in cache should be
     *      replaced with a new one
     * @param boolean $autoCreate the flag marking if neccessary status elements
     *      should be created if not found
     *
     * @return true on success, false otherwise
     */
    public static function addScheduledTask(
        $sectionName,
        $entryPoint,
        $scheduledTask,
        &$statusTable = null,
        $replace = false,
        $autoCreate = true
    ) {
        $result = false;
        if (!empty($scheduledTask->getUuid())
            && (
                $replace
                || self::getScheduledTask(
                    $sectionName,
                    $entryPoint,
                    $scheduledTask->getUuid(),
                    $statusTable,
                    $autoCreate
                ) == null
            )
        ) {
            $entryPointTasks = self::getEntryPoint(
                $sectionName,
                $entryPoint,
                $statusTable,
                $autoCreate
            );
            if (is_array($entryPointTasks)) {
                $statusTable[$sectionName][$entryPoint]
                    [$scheduledTask->getUuid()] = $scheduledTask;
                $result = true;
            }
        }
        return $result;
    }

    /**
     * Replaces given task in status
     *
     * @param string $sectionName the name of section to replace the task in
     * @param string $entryPoint the name of entry point to replace the task in
     * @param CronScheduledTask $scheduledTask the task to replace
     * @param array $statusTable the status to replace the task in;
     *      if null, the status is retreived from cache
     * @param boolean $autoCreate the flag marking if neccessary status elements
     *      should be created if not found
     *
     * @return true on success, false otherwise
     */
    public static function replaceScheduledTask(
        $sectionName,
        $entryPoint,
        $scheduledTask,
        &$statusTable = null,
        $autoCreate = true
    ) {
        return self::addScheduledTask(
            $sectionName,
            $entryPoint,
            $scheduledTask,
            $statusTable,
            true,
            $autoCreate
        );
    }

    /**
     * Removes scheduled task from cache
     *
     * @param string $sectionName the name of section to remove the task from
     * @param string $entryPoint the name of entry point to remove the task from
     * @param string $uuid the UUID of task to remove
     * @param CronScheduledTask $scheduledTask the task to replace
     * @param array $statusTable the status to remove the task from;
     *      if null, the status is retreived from cache
     * @param boolean $autoCreate the flag marking if neccessary status elements
     *      should be created if not found
     *
     * @return true on success, false otherwise
     */
    public static function removeScheduledTask(
        $sectionName,
        $entryPoint,
        $uuid,
        &$statusTable = null,
        $autoCreate = true
    ) {
        $result = false;
        $entryPointTasks = self::getEntryPoint(
            $sectionName,
            $entryPoint,
            $statusTable,
            $autoCreate
        );
        if (is_array($entryPointTasks)) {
            unset($statusTable[$sectionName][$entryPoint][$uuid]);
            $result = true;
        }
        return $result;
    }

    /**
     * Saves the status in the cache itself
     *
     * @param array $statusTable the status to save
     */
    public static function setStatusTable($statusTable)
    {
        Php7Handler::apc_store(self::APC_KEY, $statusTable, 0);
    }
}