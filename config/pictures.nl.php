<?php

/**
 * Pictures configuration
 *
 * Those are configuration overrides for OCNL node only.
 */

/**
 * Max size (MB) of attached picture (this is internal only restriction)
 * Please note other additional http/php server side restrictions.
 */
$pictures['maxFileSize'] = 10; // former $config['limits']['image']['filesize']

/**
 * Do not resize images smaller than this size (MB)
 */
$pictures['resizeLargerThan'] = 0.1; // former $config['limits']['image']['resize_larger']
