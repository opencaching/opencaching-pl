<?php

/**
 * Watchlist configuration
 *
 * This is a default configuration.
 * It may be customized in node-specific configuration file.
 *
 * - 'diag_file':
 *      The path of a file used for operating time diagnosis
 *      set to empty string to disable diagnosis
 * - 'use_logentries':
 *      Set to true if you want to use Log::logentries in the code
 *      Set to false if Log::logentries can be omitted
 */

$watchlist = [
    'diag_file' => '/var/log/ocpl/runwatch.log',
    'use_logentries' => true,
];
