<?php

namespace Utils\Cron;

use lib\Objects\Cron\CronCommons;
use lib\Objects\Cron\CronTask;

final class CronConfigurator
{
    const CRON_STRING_FIELDS = [
        [ CronCommons::MINUTE, 0, 59 ],
        [ CronCommons::HOUR, 0, 59 ],
        [ CronCommons::DAY, 1, 31 ],
        [ CronCommons::MONTH, 1, 12 ],
        [ CronCommons::WEEKDAY, 0, 6 ]
    ];
    
    public static function configureCommons($config)
    {
        $result = [];
        $result["ttl"] = self::getTtl($config);
        $result["maxHistory"] = self::getMaxHistory($config);
        $result["includeMissed"] = self::getIncludeMissed($config);
        $result["executionMode"] = self::getExecutionMode($config);
        $result["dateTimeFormat"] = self::getDisplayDateTimeFormat($config);
        $result["refreshInterval"] = self::getDisplayRefreshInterval($config);
        $result["displayHistory"] = self::getDisplayHistory($config);
        return $result;
    }
    
    public static function getMaxHistory($config)
    {
        return !empty($config["max_history"])
            ? $config["max_history"]
            : CronCommons::DEFAULT_MAX_HISTORY;
    }
    
    public static function getTtl($config)
    {
        return
            !empty($config["ttl"])
            ? $config["ttl"]
            : CronCommons::DEFAULT_TTL;
    }
    
    public static function getIncludeMissed($config)
    {
        return isset($config["include_missed"])
            ? boolval($config["include_missed"])
            : CronCommons::DEFAULT_INCLUDE_MISSED;
    }
    
    public static function getAllowConcurrent($config)
    {
        return isset($config["allow_concurrent"])
            ? boolval($config["allow_concurrent"])
            : CronCommons::DEFAULT_ALLOW_CONCURRENT;
    }
    
    public static function getExecutionMode($config)
    {
        $result = CronCommons::DEFAULT_EXECUTION_MODE;
        if (
            isset($config["execution_mode"])
            && in_array($config["execution_mode"], [
                CronCommons::EXECUTION_MODE_DIRECT,
                CronCommons::EXECUTION_MODE_EXTERNAL
            ])
        ) {
            $result = $config["execution_mode"];
        }
        return $result;
    }
    
    public static function getDisplayDateTimeFormat($config)
    {
        return isset($config["display_datetime_format"])
            ? $config["display_datetime_format"]
            : CronCommons::DEFAULT_DISPLAY_DATETIME_FORMAT;
    }
    
    public static function getDisplayRefreshInterval($config)
    {
        return isset($config["display_refresh_interval"])
            ? intval($config["display_refresh_interval"])
            : CronCommons::DEFAULT_DISPLAY_REFRESH_INTERVAL;
    }
    
    public static function getDisplayHistory($config)
    {
        return isset($config["display_history"])
            ? intval($config["display_history"])
            : CronCommons::DEFAULT_DISPLAY_HISTORY;
    }
    
    public static function configureTasks($config, $ttl, $maxHistory)
    {
        $result = [];       
        if (!empty($config["tasks"]) && is_array($config["tasks"])) {
            foreach($config["tasks"] as $entryPoint => $parameters) {
                if (!empty($entryPoint)
                    && !isset($result[$entryPoint])
                    && is_array($parameters)
                    && !empty($parameters[0])
                ) {
                    $cronTask = self::getCronTaskFromParameters(
                        $entryPoint,
                        $parameters,
                        $ttl,
                        $maxHistory
                    );
                    if ($cronTask !== null) {
                        $result[$entryPoint] = $cronTask;
                    }
                }
            }
        }
        return $result;
    }
    
    public static function getCronTask($entryPoint, $config, $ttl, $maxHistory)
    {
        $result = null;
        if (
            !empty($entryPoint)
            && !empty($config["tasks"])
            && is_array($config["tasks"])
            && !empty($config["tasks"][$entryPoint])
        ) {
            $result = self::getCronTaskFromParameters(
                $entryPoint,
                $parameters,
                $ttl,
                $maxHistory
            );
        }
        return $result;
    }
    
    private static function getCronTaskFromParameters(
        $entryPoint,
        $parameters,
        $ttl,
        $maxHistory
    ) {
        $result = null;
        if (is_array($parameters) && !empty($parameters[0])) {
            $result = new CronTask(
                $parameters[0],
                !empty($parameters[1]) ? $parameters[1] : null,
                !empty($parameters[2]) ? $parameters[2] : $ttl,
                !empty($parameters[3]) ? $parameters[3] : $maxHistory
            );
            self::parseCronString($result);
        }
        return $result;
    }
    
    private static function parseCronString($cronTask) {
        $fn = 0;
        foreach(explode(' ',$cronTask->getCronString()) as $cronValue) {
            $cronField = self::CRON_STRING_FIELDS[$fn++];
            $numberPattern = '';
            $numberMax = $cronField[2];
            while ($numberMax > 0) {
                if (strlen($numberPattern) > 0) {
                    $numberPattern = '?' . $numberPattern;
                }
                $numberPattern = '[0-'
                    . ($numberMax > 10 ? 9 : $numberMax % 10)
                    . ']'
                    . $numberPattern;
                $numberMax = intdiv($numberMax, 10);
            }
            foreach(explode(',', $cronValue) as $v) {
                if (preg_match(
                        '/^(?:(\*)|('
                        . $numberPattern
                        . '\-'
                        . $numberPattern
                        . ')|('
                        . $numberPattern
                        . '))(?:\/('
                        . $numberPattern
                        . '))?/',
                        $v,
                        $matches)
                ) {
                    $minVal = 0;
                    $maxVal = -1;
                    
                    if (!empty($matches[1])) {
                        $minVal = $cronField[1];
                        $maxVal = $cronField[2];
                    } elseif (
                        !empty($matches[2])
                        && preg_match(
                            '/^('
                            . $numberPattern
                            . ')\-('
                            . $numberPattern
                            .')/',
                            $matches[2],
                            $rangeMatches)
                        && intval($rangeMatches[1]) <= intval($rangeMatches[2])
                    ) {
                        $minVal =
                            intval($rangeMatches[1]) > $cronField[1] ?
                                intval($rangeMatches[1]) : $cronField[1];
                        $maxVal =
                            intval($rangeMatches[2]) < $cronField[2] ?
                                intval($rangeMatches[2]) : $cronField[2];
                    } elseif (!empty($matches[3])) {
                        $minVal =
                            intval($matches[3]) > $cronField[1]
                            ? intval($matches[3])
                            : $cronField[1];
                        $maxVal =
                            intval($matches[3]) < $cronField[2]
                            ? intval($matches[3])
                            : $cronField[2];
                    }
                    
                    $interval = 1;
                    
                    if (!empty($matches[4])) {
                        $interval = intval($matches[4]);
                    }
                    
                    for ($t = $minVal; $t <= $maxVal; $t += $interval) {
                        $cronTask->addCronValue($cronField[0], $t);
                    }
                }
            }
        }
    }
}