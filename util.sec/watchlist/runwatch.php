<?php
/**
 * Runs watchlist processing, should be executed from cron
 */
use Controllers\Cron\WatchlistController;

require_once (__DIR__.'/../../lib/common.inc.php');

// Watchlist processing controller instantiation and execution
(new WatchlistController())->index();
exit();
