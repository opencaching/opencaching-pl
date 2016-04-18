<?php
/**
 *
 * This script refresh indexes used by search script
 *
 */

$rootpath = '../../';
require_once $rootpath . '/lib/ClassPathDictionary.php';
require_once $rootpath . 'lib/ftsearch.inc.php';

ftsearch_refresh();

