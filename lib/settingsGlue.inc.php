<?php

/* ************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ************************************************************************** */

 require __DIR__.'/settings.inc.php';

/*
 * This file acts as an intermediary between old settings used in
 * settings.inc.php and the new $config settings (as they are defined in
 * settingsDefault.inc.php).
 *
 * This file will allow seamless transition while refactoring all settings
 * to the new format such that node admins are not required to intervene
 * until one single redefining of settings.inc.php at the end of this
 * project.
 */

/* ** Sample code *********************************************************

=== in settingsDefault.inc.php ============================================
// database name (schema).REQUIRED
$config['server']['db']['schema'] = 'ocpl';
// database username. REQUIRED
$config['server']['db']['username'] = 'my_username';
// database password. REQUIRED
$config['server']['db']['password'] = 'my_password';
===========================================================================

--- in settings.inc.php ---------------------------------------------------
    //local database settings
    $dbpconnect = false;
    $dbserver = 'localhost';
    $dbname = 'ocXX';
    $dbusername = 'ocXX';
    $dbpasswd = '12345';
    $opt['db']['server'] = 'localhost';
    $opt['db']['name'] = 'ocXX';
        $opt['db']['username'] = 'ocXX';
        $opt['db']['password'] = '12345';
---------------------------------------------------------------------------

*/

/* Glue code:
 * Note: this is a sample, it is unused at the moment.
if (isset($dbname))
    $config['server']['db']['schema'] = $dbname;
if (isset($dbusername))
    $config['server']['db']['username'] = $dbusername;
if (isset($dbpasswd))
    $config['server']['db']['password'] = $dbpasswd;

if (isset($opt['db']['name']))
    $config['server']['db']['schema'] = $opt['db']['name'];
if (isset($opt['db']['username']))
    $config['server']['db']['username'] = $opt['db']['username'];
if (isset($opt['db']['password']))
    $config['server']['db']['password'] = $opt['db']['password'];
*/

/* *** END sample code **************************************************** */


if (isset($site_name)){
    $config['siteName'] = $site_name;
}

if (isset($onlineusers)){
    $config['mainLayout']['displayOnlineUsers'] = ($onlineusers == 1);
}

if (isset($dynstylepath)){
    $config['path']['dynamicFilesDir'] = $dynbasepath;
}


if ( isset($opt['cookie']['name']) ){
    $config['cookie']['name'] = $opt['cookie']['name'];
}
if ( isset($opt['cookie']['path']) ){
    $config['cookie']['path'] = $opt['cookie']['path'];
}
if ( isset($opt['cookie']['domain']) ){
    $config['cookie']['domain'] = $opt['cookie']['domain'];
}




