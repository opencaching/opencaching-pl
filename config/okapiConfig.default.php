<?php

/**
 * OKAPI specific settings to be set in okapi_settings.php file
 *
 * This is a default configuration.
 * It may be customized in node-specific configuration file.
 */

$config = [];

/**
 * Blacklist of OKAPI cron jobs. Jobs from this list will not be started by OKAPI.
 * USUALLY THERE IS NO NEED TO BLACKLIST ANY OKAPI CRON JOB!
 * This is only for debug purpose4 in production env.
 */
$config['cronJobsBlackList'] = [];
