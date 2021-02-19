<?php

/**
 * Default OKPAI specific settings to set in okapi_settings.php file
 *
 * This is a DEFAULT configuration for ALL nodes, which contains necessary vars.
 *
 * If you to customize it for your node
 * create config for your node and there override array values as needed.
 *
 */

$config = [];

/**
 * Blacklist of OKAPI cron jobs. Jobs from this list will not be started by OKAPI.
 * USUALLY THERE IS NO NEED TO BLACKLIST ANY OKAPI CRON JOB!
 * This is only for debug purpose4 in production env.
 */
$config['cronJobsBlackList'] = [];
