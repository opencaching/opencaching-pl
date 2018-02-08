<?php
/*
 * Configuration setting for watchlist:
 * - "diag_file":
 *      the path of a file used for operating time diagnosis
 *      set to empty string to disable diagnosis
 * - "use_logentries":
 *      set to true if you want to use turn on Log::logentries in the code,
 *      set to false if Log::logentries can be ommited
 */
$watchlist = [
    'diag_file' => '/var/log/ocpl/runwatch.log',
    'use_logentries' => true
];
