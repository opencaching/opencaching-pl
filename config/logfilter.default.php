<?php

/**
 * Configuration of cache log filter
 *
 * This is a default configuration.
 * It may be customized in node-specific configuration file.
 *
 * - 'mark_currentuser_logs':
 *      set to true if you want to make the current user log entries visually
 *      distinguished on the cache logs list
 * - 'enable_logs_filtering':
 *      set to true if you want to display and make usable the log filter on the
 *      cache logs list
 * - 'show_activities_tooltip':
 *      set to true if you want to display current user activities ('Found it',
 *      "Didn't found it", 'Attended') as a tooltip for cache icon on the cache
 *      page,
 *      set to false if you want to display only the cache type as a tooltip
 *      (title) for cache icon on the cache page regardless of current user
 *      activities
 */

$logfilter = [
    'mark_currentuser_logs' => true,
    'enable_logs_filtering' => true,
    'show_activities_tooltip' => true,
];
