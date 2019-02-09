<?php
/**
 * Runs watchlist processing, should be executed from cron
 */
use src\Controllers\Cron\WatchlistController;

require_once (__DIR__.'/../../lib/common.inc.php');

// Watchlist processing controller instantiation and execution
(new WatchlistController())->index();
exit();
