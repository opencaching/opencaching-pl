<?php

$rootpath = '../../';
require($rootpath . 'lib/clicompatbase.inc.php');
require($rootpath . 'lib/settings.inc.php');

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

// zeichen die nicht am beginn eines Caches sein d�rfen
$evils[] = " ";
$evils[] = "\n";
$evils[] = "\r";

$rs = sql("SELECT `cache_id`, `name` FROM `caches` WHERE `name`<'\"' ORDER BY `name` ASC");
while ($r = mysql_fetch_array($rs)) {
    $name = $r['name'];

    $bFound = true;
    while ($bFound == true) {
        $bFound = false;

        for ($j = 0; $j < count($evils); $j++) {
            if (substr($name, 0, 1) == $evils[$j]) {
                $name = substr($name, 1);
                $bFound = true;
            }
        }
    }

    if ($name != '') {
        if ($name != $r['name']) {
            echo "Changed name to: " . $name . "\n";

            sql("UPDATE `caches` SET `last_modified`=NOW(), `name`='" . sql_escape($name) . "' WHERE `cache_id`=" . sql_escape($r['cache_id']));
        }
    } else
        echo 'new name would be empty, not changing' . "\n";
}
mysql_free_result($rs);
?>
