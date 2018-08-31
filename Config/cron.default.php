<?php
/*
 * Configuration settings for cron:
 * - "execution_mode":
 *      defines how the consecutive tasks will be performed, possible values:
 *      - "direct":
 *           tasks are performed inside the cron process by direct call of
 *           method or function;
 *           pros: there is no overhead on the task execution;
 *           cons: in case of any uncatcheable fatal error the whole process
 *           dies, the next tasks waiting for execution never starts;
 *      - "external":
 *           tasks are performed by calling external wrapper page by WWW, using
 *           the protocol, host and port used in cron call, the wrapper calls
 *           method or function and returns results in response;
 *           pros: even the fatal errors should not prevent another tasks from
 *           being performed, the cron process should ends in the correct way;
 *           cons: there is an overhead - one additional WWW request per task,
 *           making the request and parsing the response; there is a possibility
 *           not to manage properly very long task durations;
 *      default value: "external"
 * - "include_missed":
 *      if true and there is the finished task in the current tasks and
 *      task cron string tells the task should be started in minutes between its
 *      previous instance finish and now, it will be scheduleed to start;
 *      the settings makes sense only if cron process itself is called less
 *      frequently than once a minute otherwise it should be set to false;
 *      default value: true
 * - "allow_concurrent":
 *      if true and there is an unfinished task in current tasks and the task
 *      cron string tells it should start now, it will be scheduled to start;
 *      otherwise the task will not start until the previous instance finishes
 *      and another start time comes
 *      default value: false
 * - "display_datetime_format":
 *      date format, recognized by momen.js, used in status display; defined
 *      because sometimes seconds ARE important
 *      default value: "YYYY/MM/DD HH:mm:ss"
 * - "display_refresh_interval":
 *      the number of seconds the displayed status will be refreshed after
 *      default value: 30
 * - "display_history":
 *      the number of rows retrieved from database per entrypoint; it is constant
 *      regardless of the view selected
 *      default value: 10
 * - "ttl":
 *      the maximum number of seconds the cron task (process) can work unless
 *      increased by subsequent tasks; in fact the cron increases its ttl
 *      according to the sum of ttls of tasks scheduled to start;
 *      the value is the default for each defined task unless the task definition
 *      redefines it; the task ttl of 0 or below means the task can run infinitely
 *      but this setting is not recommended;
 *      default value: 10
 * - "max_history":
 *      the maximum number of entries stored in database per each entrypoint,
 *      including the cron scheduler itself; when the value is exceeded, the
 *      least recent rows are deleted;
 *      the value is the default for each defined task unless the task definition
 *      redefines it; the value of 0 or below means the task history will grow
 *      infinitely;
 *      default value: 1000
 * - "tasks":
 *      an array containing [entrypoint, definition] pairs each defining the task
 *      to schedule:
 *      - "entrypoint":
 *           the parameterless static class method (full path including
 *           namespace) or function name, in both case without final parentheses,
 *           to call to start the task execution; In order to better recognize
 *           the task execution result it should return true on successful
 *           execution or false if execution has gone not as it was meant to
 *           pass in terms of contents;
 *      - definition:
 *           an array containing the entrypoint definition, with elements:
 *              "cron_string" - [required] the allowed execution time string
 *                              defined exactly as in the linux cron;
 *              "display_name" - [optional] if set its vaule will be used in
 *                              cron status visualisation instead of entrypoint;
 *                              should be as short as possible but unique among
 *                              tasks;
 *              "description" - [optional] the description of entrypoint,
 *                              displayed in the cron status;
 *              "ttl" - [optional] see the information above;
 *              "max_history" - [optional] see the information above;
 *              "allow_concurrent" - [optional] see the information above;
 */
// Example:
//
// $cron = [
//      'execution_mode' => 'external',
//      'include_missed' => true,
//      'allow_concurrent' => false,
//      'display_datetime_format' => 'YYYY/MM/DD HH:mm:ss',
//      'display_refresh_interval' => 30,
//      'display_history' => 10,
//      'ttl' => 300,
//      'max_history' => 2000,
//
//      'tasks' => [
//          'Some\Namespace\TestClass::start' => [
//              'cron_string' => '*/15 * * * *',
//              'display_name' => 'Test',
//              'description' => 'Some task running every 15 minutes',
//              'ttl' => 600,
//              'allow_concurrent' => true
//          ],
//          'Other\AnotherClass::run' => [
//              'cron_string' => '30 5 * * 6',
//              'display_name' => 'Another',
//              'description' => 'Some task running every Sunday at 5:30',
//              'max_history' => 500,
//          ],
//      ]
// ];

$cron = [
    'execution_mode' => 'external',
    'include_missed' => false,
    'allow_concurrent' => false,
    'display_datetime_format' => 'YYYY/MM/DD HH:mm:ss',
    'display_refresh_interval' => 30,
    'display_history' => 5,
    'ttl' => 300,
    'max_history' => 2000,

    'tasks' => [
    /* No tasks are defined here, please redefine $cron['tasks'] in the node or
     * local config
     */
    ]
];
