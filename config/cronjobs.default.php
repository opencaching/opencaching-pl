<?php

/**
 * Cronjob configuration
 *
 * This is a default configuration.
 * It may be customized in node-specific configuration file.
 *
 * Job that are scheduled for the same time will preferably run
 * in the same order, as they are defined in configuration.
 *
 * Examples for default settings (all times MUST be a multiple of 5 minutes):
 *
 *   'FrequentJob' => 'every 15 minutes',
 *   'HourlyJob' => 'hourly at :30', // :00 - :55
 *   'DailyJob' => 'daily at 5:20',
 *   'WeeklyJob' => 'weekly on Tuesday at 22:30',
 *   'MonthlyJob' => 'monthly on day 5 at 1:00', // day 1 - 28
 *   'DisabledJob' => 'disabled',
 */

$cronjobs['schedule'] = [
    # KOJOTY: temp disable as geokrety.org doesn't work 'GeoKretyDbQueueJob' => 'every 10 minutes',
    'AltitudeUpdateJob' => 'every 15 minutes',
    'PublishCachesJob' => 'hourly at :00',
    'WatchlistNotifyJob' => 'hourly at :05',
    'NewCachesNotifyJob' => 'hourly at :10',
    'GeoPathJob' => 'daily at 0:10',
    'ClearCacheAccessLogsJob' => 'daily at 1:00',
    'AutoArchiveCachesJob' => 'daily at 4:30',
    'GeoKretyNewJob' => 'daily at 4:45',
    'TitledCacheAddJob' => 'monthly on day 1 at 0:20',

    // Reentrant jobs; these will always run *after* non-reentrant jobs.
    // See Jobs::isReentrant() for explanation.
    'OkapiSignallingJob' => 'every 5 minutes',
    'ClearCacheOldUniqueVisitsJob' => 'daily at 5:00',
];
