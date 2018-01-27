<?php
/**
 * Runs watchlist processing, should be executed from cron
 */
use Controllers\Cron\WatchlistController;

$rootpath = __DIR__ . '/../../';
require_once ($rootpath . 'lib/common.inc.php');

// Watchlist processing controller instantiation and execution
(new WatchlistController())->index();
exit();
