<?php

$rootpath = '../../';
require_once($rootpath . 'lib/settings.inc.php');
require_once($rootpath . 'lib/clicompatbase.inc.php');

/* begin db connect */
$bFail = false;
$dblink = mysql_connect($dbserver, $dbusername, $dbpasswd);
if ($dblink != false) {
    //database connection established ... set the used database
    if (@mysql_select_db($dbname, $dblink) == false) {
        $bFail = true;
        mysql_close($dblink);
        $dblink = false;
    }
} else
    $bFail = true;

if ($bFail == true) {
    echo 'Unable to connect to database';
    exit;
}
/* end db connect */

$rs = sql('SELECT `cache_id` FROM `caches`');
while ($r = mysql_fetch_array($rs)) {
    setCacheDefaultDescLang($r['cache_id']);
}
mysql_free_result($rs);
?>
