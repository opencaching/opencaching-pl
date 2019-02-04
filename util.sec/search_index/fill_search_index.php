<?php
/**
 *
 * This script refresh indexes used by search script
 *
 */

require_once __DIR__.'/../../lib/ClassPathDictionary.php';
require_once __DIR__.'/../../lib/ftsearch.inc.php';

ftsearch_refresh();
