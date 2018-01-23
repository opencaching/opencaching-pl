<?php

/**
 * This is config file for apc.php script - it allows to override its config
 * without any modification in apc.php
 */

require_once __DIR__ . '/../ClassPathDictionary.php';   // class autoloader
require_once __DIR__ . '/../settingsGlue.inc.php';  // load OC settings

define('USE_AUTHENTICATION', 1);                        // Use (internal) authentication - best choice if

// no other authentication is available
// If set to 0:
//  There will be no further authentication. You
//  will have to handle this by yourself!
// If set to 1:
//  You need to change ADMIN_PASSWORD to make
//  this work!

define('ADMIN_USERNAME',$config['apc']['username']);    // Admin Username
define('ADMIN_PASSWORD',$config['apc']['password']);    // Admin Password - CHANGE THIS TO ENABLE!!!

define('DATE_FORMAT',$datetimeFormat);

define('GRAPH_SIZE',200);                               // Image size
