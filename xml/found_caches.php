<?php

require "../lib/settings.inc.php";
header('Content-type: text/xml');

function db_connect()
{
    global $dblink, $dbpconnect, $dbusername, $dbname, $dbserver, $dbpasswd, $dbpconnect;

    //connect to the database by the given method - no php error reporting!
    if ($dbpconnect == true) {
        $dblink = @mysql_pconnect($dbserver, $dbusername, $dbpasswd);
    } else {
        $dblink = @mysql_connect($dbserver, $dbusername, $dbpasswd);
    }

    if ($dblink != false) {
        //database connection established ... set the used database
        if (@mysql_select_db($dbname, $dblink) == false) {
            //error while setting the database ... disconnect
            db_disconnect();
            $dblink = false;
        }
    }
}

//disconnect the databse
function db_disconnect()
{
    global $dbpconnect, $dblink;

    //is connected and no persistent connect used?
    if (($dbpconnect == false) && ($dblink !== false)) {
        mysql_close($dblink);
        $dblink = false;
    }
}

function typeLetter($intType)
{
    switch ($intType) {
        case 1: return "U";
            break;      //unknown
        case 2: return "T";
            break;      //traditional
        case 3: return "M";
            break;      //multi
        case 4: return "V";
            break;      //virtual
        case 5: return "W";
            break;      //webcam
        case 6: return "E";
            break;      //event
        case 7: return "Q";
            break;      //quiz
        case 8: return "C";
            break;      //math
        case 9: return "O";
            break;      //mOving
        case 10: return "D";
            break; //Drive-in
        default: return "";
    }
}

db_connect();

$language = "pl";
$encoding = "UTF-8";

$user_id = mysql_escape_string($_GET['uid']);
mysql_query("SET NAMES 'utf8'");
$result = mysql_query("SELECT `cache_logs`.`cache_id` `cache_id` , `caches`.`latitude`, `caches`.`longitude`,  `caches`.`type` `type` , `cache_logs`.`date` `date` , `caches`.`name` `name` , `log_types`.`icon_small` , `log_types_text`.`text_combo` FROM `cache_logs` , `caches` , `log_types` , `log_types_text` WHERE `cache_logs`.`deleted`=0 AND `cache_logs`.`user_id` = '" . $user_id . "' AND `cache_logs`.`cache_id` = `caches`.`cache_id` AND `cache_logs`.`type` = 1 AND `log_types`.`id` = `cache_logs`.`type` AND `log_types_text`.`log_types_id` = `log_types`.`id` AND `log_types_text`.`lang` = '" . $language . "' ORDER BY `cache_logs`.`date` DESC , `cache_logs`.`date_created` DESC");

echo "<?xml version=\"1.0\" encoding=\"" . $encoding . "\"?>\n";
echo "<markers>\n";
while ($res = mysql_fetch_array($result)) {
    echo "<marker id=\"" . htmlspecialchars($res['cache_id']) . "\" name=\"" . htmlspecialchars($res['name']) . "\" lat=\"" . htmlspecialchars($res['latitude']) . "\" lng=\"" . htmlspecialchars($res['longitude']) . "\"  type=\"" . htmlspecialchars(typeLetter($res['type'])) . "\" date=\"" . htmlspecialchars(typeLetter($res['date'])) . "\" />\n";
}
echo "</markers>";

db_disconnect();
?>
