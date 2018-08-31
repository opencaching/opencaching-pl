<?php
/**
 * Contains \lib\Objects\Cron\CronCommons class definition
 */
namespace lib\Objects\Cron;

/**
 * Defined to be called only in static mode, contains constants used in cron
 * processing and visualisation
 */
final class CronCommons
{
    /**
     * Default value of maximum number of rows stored in database per
     * entrypoint
     */
    const DEFAULT_MAX_HISTORY = 1000;
    /** Default value of task maximum duration in seconds */
    const DEFAULT_TTL = 300;
    /** Default setting if include ommited tasks in processing */
    const DEFAULT_INCLUDE_MISSED = true;
    /**
     * Default setting if start new task from the same entrypoint when the
     * previous one hasn't finished work
     */
    const DEFAULT_ALLOW_CONCURRENT = false;

    /**
     * Execution mode for starting subsequent tasks directly in the scheduler
     * process
     */
    const EXECUTION_MODE_DIRECT = "direct";
    /**
     * Execution mode for starting subsequent tasks via WWW request to the
     * external wrapper
     */
    const EXECUTION_MODE_EXTERNAL = "external";
    /** Default execution mode */
    const DEFAULT_EXECUTION_MODE = self::EXECUTION_MODE_EXTERNAL;

    /** Keys used in CronTask values */
    const MINUTE = "minute";
    const HOUR = "hour";
    const DAY = "day";
    const MONTH = "month";
    const WEEKDAY = "weekday";

    /** Section the special/system tasks are assigned to */
    const SECTION_SPECIALS = "specials";
    /** Section the common tasks are assigned to */
    const SECTION_TASKS = "tasks";
    /** Entrypoint for scheduler process, used in current and historical store */
    const SCHEDULER_ENTRYPOINT_VALUE = "== cron scheduler ==";

    /** Maximum length of task output stored in database */
    const OUTPUT_MAX_LEN = 1024;
    /** Maximum length of error message stored in database */
    const ERROR_MSG_MAX_LEN = 1024;

    /** URL of external wrapper to request in external execution mode */
    const WRAPPER_EXEC_URL = '/util.sec/cron/cron_external_wrapper.php';
    /** Response header for task execution result in external execution mode */
    const WRAPPER_HEADER_RESULT = 'Cron-Wrapper-Result';
    /** Response header for task execution error in external execution mode */
    const WRAPPER_HEADER_ERROR = 'Cron-Wrapper-Error';

    /** Default time format used in display, recognized by moment.js library */
    const DEFAULT_DISPLAY_DATETIME_FORMAT = 'YYYY/MM/DD HH:mm:ss';
    /** Default interval in seconds to automatic refresh display */
    const DEFAULT_DISPLAY_REFRESH_INTERVAL = 30;
    /** Default task instances to display per entrypoint */
    const DEFAULT_DISPLAY_HISTORY = 10;

    private function __construct() {}
}
