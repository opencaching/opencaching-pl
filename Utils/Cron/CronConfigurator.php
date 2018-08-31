<?php
/**
 * Contains \Utils\Cron\CronConfigurator class definition
 */
namespace Utils\Cron;

use lib\Objects\Cron\CronCommons;
use lib\Objects\Cron\CronTask;

/**
 * Used for retreiving real configuration from given config, including
 * default values and cron string parse results
 */
final class CronConfigurator
{
    /** config keys */
    const CONFIG_EXECUTION_MODE = "execution_mode";
    const CONFIG_INCLUDE_MISSED = "include_missed";
    const CONFIG_ALLOW_CONCURRENT = "allow_concurrent";
    const CONFIG_DATETIME_FORMAT = "display_datetime_format";
    const CONFIG_REFRESH_INTERVAL = "display_refresh_interval";
    const CONFIG_DISPLAY_HISTORY = "display_history";
    const CONFIG_TTL = "ttl";
    const CONFIG_MAX_HISTORY = "max_history";

    const CONFIG_TASK_DISPLAY_NAME = "display_name";
    const CONFIG_TASK_CRON = "cron_string";
    const CONFIG_TASK_DESCRIPTION = "description";

    /** elements available in cron string including min and max values */
    const CRON_STRING_FIELDS = [
        [ CronCommons::MINUTE, 0, 59 ],
        [ CronCommons::HOUR, 0, 59 ],
        [ CronCommons::DAY, 1, 31 ],
        [ CronCommons::MONTH, 1, 12 ],
        [ CronCommons::WEEKDAY, 0, 6 ]
    ];

    /**
     * Extracts base cron config elements using corresponding methods
     *
     * @param array $config the configuration to extract the settings from
     *
     * @return array inner configuration where keys matches CronScheduler or
     *      CronStatusController attributes
     */
    public static function configureCommons($config)
    {
        $result = [];
        $result["executionMode"] = self::getExecutionMode($config);
        $result["includeMissed"] = self::getIncludeMissed($config);
        $result["allowConcurrent"] = self::getAllowConcurrent($config);
        $result["ttl"] = self::getTtl($config);
        $result["maxHistory"] = self::getMaxHistory($config);
        $result["dateTimeFormat"] = self::getDisplayDateTimeFormat($config);
        $result["refreshInterval"] = self::getDisplayRefreshInterval($config);
        $result["displayHistory"] = self::getDisplayHistory($config);
        return $result;
    }

    /**
     * Extracts maxHistory from config
     *
     * @param array $config the configuration to extract the settings from
     *
     * @return integer maxHistory from config or default if not found
     */
    public static function getMaxHistory($config)
    {
        return !empty($config[self::CONFIG_MAX_HISTORY])
            ? $config[self::CONFIG_MAX_HISTORY]
            : CronCommons::DEFAULT_MAX_HISTORY;
    }

    /**
     * Extracts ttl from config
     *
     * @param array $config the configuration to extract the settings from
     *
     * @return integer ttl from config or default if not found
     */
    public static function getTtl($config)
    {
        return
            !empty($config[self::CONFIG_TTL])
            ? $config[self::CONFIG_TTL]
            : CronCommons::DEFAULT_TTL;
    }

    /**
     * Extracts includeMissed from config
     *
     * @param array $config the configuration to extract the settings from
     *
     * @return boolean includeMissed from config or default if not found
     */
    public static function getIncludeMissed($config)
    {
        return isset($config[self::CONFIG_INCLUDE_MISSED])
            ? boolval($config[self::CONFIG_INCLUDE_MISSED])
            : CronCommons::DEFAULT_INCLUDE_MISSED;
    }

    /**
     * Extracts allowConcurrent from config
     *
     * @param array $config the configuration to extract the settings from
     *
     * @return boolean allowConcurrent from config or default if not found
     */
    public static function getAllowConcurrent($config)
    {
        return isset($config[self::CONFIG_ALLOW_CONCURRENT])
            ? boolval($config[self::CONFIG_ALLOW_CONCURRENT])
            : CronCommons::DEFAULT_ALLOW_CONCURRENT;
    }

    /**
     * Extracts executionMode from config
     *
     * @param array $config the configuration to extract the settings from
     *
     * @return string executionMode from config or default if not found or invalid
     */
    public static function getExecutionMode($config)
    {
        $result = CronCommons::DEFAULT_EXECUTION_MODE;
        if (
            isset($config[self::CONFIG_EXECUTION_MODE])
            && in_array($config[self::CONFIG_EXECUTION_MODE], [
                CronCommons::EXECUTION_MODE_DIRECT,
                CronCommons::EXECUTION_MODE_EXTERNAL
            ])
        ) {
            $result = $config[self::CONFIG_EXECUTION_MODE];
        }
        return $result;
    }

    /**
     * Extracts dateTimeFormat from config
     *
     * @param array $config the configuration to extract the settings from
     *
     * @return string dateTimeFormat from config or default if not found
     */
    public static function getDisplayDateTimeFormat($config)
    {
        return isset($config[self::CONFIG_DATETIME_FORMAT])
            ? $config[self::CONFIG_DATETIME_FORMAT]
            : CronCommons::DEFAULT_DISPLAY_DATETIME_FORMAT;
    }

    /**
     * Extracts refreshInterval from config
     *
     * @param array $config the configuration to extract the settings from
     *
     * @return integer refreshInterval from config or default if not found
     */
    public static function getDisplayRefreshInterval($config)
    {
        return isset($config[self::CONFIG_REFRESH_INTERVAL])
            ? intval($config[self::CONFIG_REFRESH_INTERVAL])
            : CronCommons::DEFAULT_DISPLAY_REFRESH_INTERVAL;
    }

    /**
     * Extracts displayHistory from config
     *
     * @param array $config the configuration to extract the settings from
     *
     * @return integer displayHistory from config or default if not found
     */
    public static function getDisplayHistory($config)
    {
        return isset($config[self::CONFIG_DISPLAY_HISTORY])
            ? intval($config[self::CONFIG_DISPLAY_HISTORY])
            : CronCommons::DEFAULT_DISPLAY_HISTORY;
    }

    /**
     * Reads config task section and creates ConfigTask instances according
     * to the each entry point settings
     *
     * @param array $config the configuration to extract the settings from
     * @param int $ttl default ttl value
     * @param int $maxHistory default maximum history stored value
     * @param boolean $allowConcurrent default concurrent tasks flag value
     *
     * @return array CronTask instances
     */
    public static function configureTasks(
        $config,
        $ttl,
        $maxHistory,
        $allowConcurrent
    ) {
        $result = [];
        if (!empty($config["tasks"]) && is_array($config["tasks"])) {
            foreach($config["tasks"] as $entryPoint => $parameters) {
                if (!empty($entryPoint)
                    && !isset($result[$entryPoint])
                    && is_array($parameters)
                    && !empty($parameters[self::CONFIG_TASK_CRON])
                ) {
                    $cronTask = self::getCronTaskFromParameters(
                        $entryPoint,
                        $parameters,
                        $ttl,
                        $maxHistory,
                        $allowConcurrent
                    );
                    if ($cronTask !== null) {
                        $result[$entryPoint] = $cronTask;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Extracts single task from given configuration matching entry point
     *
     * @param string $entryPoint the entry point of task to get the settings
     * @param array $config the configuration to extract the settings from
     * @param int $ttl default ttl value
     * @param int $maxHistory default maximum history stored value
     * @param boolean $allowConcurrent default concurrent tasks flag value
     *
     * @return CronTask created instance
     */
    public static function getCronTask(
        $entryPoint,
        $config,
        $ttl,
        $maxHistory,
        $allowConcurrent
    ) {
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
                $maxHistory,
                $allowConcurrent
            );
        }
        return $result;
    }

    /**
     * Creates a CronTask instance basing on given parameters
     *
     * @param string $entryPoint the entry point of created task
     * @param array $parameters the configuration parameters to create
     *      the task from
     * @param int $ttl default ttl value
     * @param int $maxHistory default maximum history stored value
     * @param boolean $allowConcurrent default concurrent tasks flag value
     *
     * @return CronTask created instance
     */
    private static function getCronTaskFromParameters(
        $entryPoint,
        $parameters,
        $ttl,
        $maxHistory,
        $allowConcurrent
    ) {
        $result = null;
        if (
            is_array($parameters)
            && !empty($parameters[self::CONFIG_TASK_CRON])
        ) {
            $result = new CronTask(
                $parameters[self::CONFIG_TASK_CRON],
                (
                    !empty($parameters[self::CONFIG_TASK_DISPLAY_NAME])
                    ? $parameters[self::CONFIG_TASK_DISPLAY_NAME]
                    : null
                ),
                (
                    !empty($parameters[self::CONFIG_TASK_DESCRIPTION])
                    ? $parameters[self::CONFIG_TASK_DESCRIPTION]
                    : null
                ),
                (
                    !empty($parameters[self::CONFIG_TTL])
                    ? intval($parameters[self::CONFIG_TTL])
                    : $ttl
                ),
                (
                    !empty($parameters[self::CONFIG_MAX_HISTORY])
                    ? intval($parameters[self::CONFIG_MAX_HISTORY])
                    : $maxHistory
                ),
                (
                    !empty($parameters[self::CONFIG_ALLOW_CONCURRENT])
                    ? boolval($parameters[self::CONFIG_ALLOW_CONCURRENT])
                    : $allowConcurrent
                )
            );
            self::parseCronString($result);
        }
        return $result;
    }

    /**
     * Parses stored in CronTask instance cronString to build a valid entries
     * array for each time element and adds the results to the instance values
     *
     * @param CronTask $cronTask the instance to operate on
     */
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
