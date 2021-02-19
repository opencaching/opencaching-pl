<?php

/*
 * Configuration setting for watchlist for OCUS node.
 * It disables logging into logentries table.
 * The rest of settings are stored in watchlist.default.php file
 */

$watchlist = [
//    'diag_file' => '/www/oc_us/cron-result/runwatch.log',
    'diag_file' => '',
    'use_logentries' => false,
];
