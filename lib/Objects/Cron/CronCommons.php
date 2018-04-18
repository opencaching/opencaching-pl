<?php

namespace lib\Objects\Cron;

final class CronCommons
{
    const DEFAULT_MAX_HISTORY = 10;
    const DEFAULT_TTL = 300;
    const DEFAULT_INCLUDE_MISSED = true;
    const DEFAULT_ALLOW_CONCURRENT = false;
    
    const EXECUTION_MODE_DIRECT = "direct";
    const EXECUTION_MODE_EXTERNAL = "external";
    const DEFAULT_EXECUTION_MODE = self::EXECUTION_MODE_EXTERNAL;
    
    const MINUTE = "minute";
    const HOUR = "hour";
    const DAY = "day";
    const MONTH = "month";
    const WEEKDAY = "weekday";
    
    const SECTION_SPECIALS = "specials";
    const SECTION_TASKS = "tasks";
    const SCHEDULER_ENTRYPOINT_VALUE = "== cron scheduler ==";
    
    const OUTPUT_MAX_LEN = 1024;
    const ERROR_MSG_MAX_LEN = 1024;

    const WRAPPER_EXEC_URL = '/util.sec/cron/cron_external_wrapper.php';
    
    const WRAPPER_HEADER_RESULT = 'Cron-Wrapper-Result';
    const WRAPPER_HEADER_ERROR = 'Cron-Wrapper-Error';
    
    const DEFAULT_DISPLAY_DATETIME_FORMAT = 'YYYY/MM/DD HH:mm:ss';
    const DEFAULT_DISPLAY_TIME_FORMAT = 'HH:mm:ss';
    const DEFAULT_DISPLAY_REFRESH_INTERVAL = 30;
    const DEFAULT_DISPLAY_HISTORY = 10;
    
    private function __construct() {}
}