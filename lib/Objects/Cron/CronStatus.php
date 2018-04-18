<?php

namespace lib\Objects\Cron;

use lib\Objects\Cron\CronCommons;
use lib\Controllers\Php7Handler;

final class CronStatus
{
    const APC_KEY = "cc3bdf0f1ee7a7579ac46661dce46970d606ebc40812f0f25d6e88a8";
    
    public static function getStatusTable($autoCreate = true)
    {
        $result = Php7Handler::apc_fetch(self::APC_KEY);
        if ($autoCreate && empty($result)) {
            $result = [];
        }
        return $result;
    }
    
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
    
    public static function removeScheduledTask(
        $sectionName,
        $entryPoint,
        $uuid,
        $scheduledTask,
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
    
    public static function setStatusTable($statusTable)
    {
        Php7Handler::apc_store(self::APC_KEY, $statusTable, 0);
    }
}