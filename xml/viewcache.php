<?php

require "../lib/settings.inc.php";

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
        @mysql_close($dblink);
        $dblink = false;
    }
}

if ($_GET['cacheid'] < 1) {
    echo "enter valid cacheid.";
    exit;
}
db_connect();

$lang = 'pl';
$encoding = 'UTF-8';

$xmlLine = "    <cache id=\"{cacheid}\">
        <name><![CDATA[{cachename}]]></name>
        <owner id=\"{ownerid}\"><![CDATA[{owner}]]></owner>
        <waypoint>{waypoint}</waypoint>
        <hidden>{time}</hidden>
        <status id=\"{statusid}\">{status}</status>
        <lon raw=\"{lonraw}\">{lon}</lon>
        <lat raw=\"{latraw}\">{lat}</lat>
        <type id=\"{typeid}\">{type}</type>
        <difficulty>{difficulty}</difficulty>
        <terrain>{terrain}</terrain>
        <size id=\"{sizeid}\">{container}</size>
        <country>{country}</country>
        <link><![CDATA[http://www.opencaching.pl/viewcache.php?wp={waypoint}]]></link>
        <desc><![CDATA[{desc}]]></desc>
        <hints><![CDATA[{hints}]]></hints>
    </cache>
";

$sql = "SELECT
    `caches`.`cache_id` `cacheid`, `caches`.`wp_oc` `waypoint`, `caches`.`status` `statusid`, `caches`.`name` `name`, `caches`.`type` `typeid`, `caches`.`size` `sizeid`, `caches`.`difficulty` `difficulty`, `caches`.`terrain` `terrain`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, `caches`.`date_hidden` `date_hidden`, `caches`.`country` `country`, `caches`.`user_id` `user_id`, `cache_desc`.`desc` `desc`, `cache_desc`.`desc_html` `html`, `cache_desc`.`hint` `hint`, `cache_status`.`" . $lang . "` `status`, `cache_size`.`" . $lang . "` `size`, `cache_type`.`" . $lang . "` `type`, `user`.`username` `owner`
    FROM `caches`, `cache_desc`, `cache_status`, `cache_size`, `cache_type`, `user`
    WHERE `caches`.`cache_id` = `cache_desc`.`cache_id` AND `cache_desc`.`language`='" . $lang . "' AND `cache_status`.`id` = `caches`.`status` AND `caches`.`size` = `cache_size`.`id` AND `caches`.`type` = `cache_type`.`id` AND `caches`.`user_id` = `user`.`user_id` AND `caches`.`cache_id` = " . mysql_escape_string($_GET['cacheid']);

if ($sqldebug == false) {
    header("Content-type: application/xml; charset=" . $encoding);
}

echo "<?xml version=\"1.0\" encoding=\"" . $encoding . "\"?>\n";
echo "<result>\n";

$rs = mysql_query($sql);
while ($r = mysql_fetch_array($rs)) {
    $thisline = $xmlLine;

    $lat = sprintf('%01.5f', $r['latitude']);
    $thisline = str_replace('{lat}', help_latToDegreeStr($lat), $thisline);
    $thisline = str_replace('{latraw}', $r['latitude'], $thisline);

    $lon = sprintf('%01.5f', $r['longitude']);
    $thisline = str_replace('{lon}', help_lonToDegreeStr($lon), $thisline);
    $thisline = str_replace('{lonraw}', $r['longitude'], $thisline);

    $time = date('d.m.Y', strtotime($r['date_hidden']));
    $thisline = str_replace('{time}', $time, $thisline);
    $thisline = str_replace('{waypoint}', $r['waypoint'], $thisline);
    $thisline = str_replace('{cacheid}', $r['cacheid'], $thisline);
    $thisline = str_replace('{cachename}', filterevilchars($r['name']), $thisline);
    $thisline = str_replace('{country}', $r['country'], $thisline);

    if ($r['hint'] == '')
        $thisline = str_replace('{hints}', '', $thisline);
    else
        $thisline = str_replace('{hints}', str_rot13_html(filterevilchars(strip_tags($r['hint']))), $thisline);

    if ($r['html'] == 0) {
        $thisline = str_replace('{desc}', filterevilchars(strip_tags($r['desc'])), $thisline);
    } else {
        $thisline = str_replace('{desc}', html2txt(filterevilchars($r['desc'])), $thisline);
    }

    $thisline = str_replace('{type}', $r['type'], $thisline);
    $thisline = str_replace('{typeid}', $r['typeid'], $thisline);
    $thisline = str_replace('{container}', $r['size'], $thisline);
    $thisline = str_replace('{sizeid}', $r['sizeid'], $thisline);
    $thisline = str_replace('{status}', $r['status'], $thisline);
    $thisline = str_replace('{statusid}', $r['statusid'], $thisline);

    $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
    $thisline = str_replace('{difficulty}', $difficulty, $thisline);

    $terrain = sprintf('%01.1f', $r['terrain'] / 2);
    $thisline = str_replace('{terrain}', $terrain, $thisline);

    $thisline = str_replace('{ownerid}', $r['user_id'], $thisline);
    $thisline = str_replace('{owner}', $r['owner'], $thisline);

    echo $thisline;
}

echo "</result>\n";

db_disconnect();
exit;

function html2txt($html)
{
    $str = str_replace("\r\n", '', $html);
    $str = str_replace("\n", '', $str);
    $str = str_replace('<br />', "\n", $str);
    $str = strip_tags($str);
    return $str;
}

function lf2crlf($str)
{
    return str_replace("\r\r\n", "\r\n", str_replace("\n", "\r\n", $str));
}

function filterevilchars($str)
{
    $evilchars = array(31 => 31, 30 => 30,
        29 => 29, 28 => 28, 27 => 27, 26 => 26, 25 => 25, 24 => 24,
        23 => 23, 22 => 22, 21 => 21, 20 => 20, 19 => 19, 18 => 18,
        17 => 17, 16 => 16, 15 => 15, 14 => 14, 12 => 12, 11 => 11,
        9 => 9, 8 => 8, 7 => 7, 6 => 6, 5 => 5, 4 => 4, 3 => 3,
        2 => 2, 1 => 1, 0 => 0);

    foreach ($evilchars AS $ascii)
        $str = str_replace(chr($ascii), '', $str);

    $str = preg_replace('/&([a-zA-Z]{1})caron;/', '\\1', $str);
    $str = preg_replace('/&([a-zA-Z]{1})acute;/', '\\1', $str);

    return $str;
}

// decimal longitude to string E/W hhh?mm.mmm
function help_lonToDegreeStr($lon)
{
    if ($lon < 0) {
        $retval = 'W ';
        $lon = -$lon;
    } else {
        $retval = 'E ';
    }

    $retval = $retval . sprintf("%03d", floor($lon)) . '� ';
    $lon = $lon - floor($lon);
    $retval = $retval . sprintf("%06.3f", round($lon * 60, 3)) . '\'';

    return $retval;
}

// decimal latitude to string N/S hh?mm.mmm
function help_latToDegreeStr($lat)
{
    if ($lat < 0) {
        $retval = 'S ';
        $lat = -$lat;
    } else {
        $retval = 'N ';
    }

    $retval = $retval . sprintf("%02d", floor($lat)) . '� ';
    $lat = $lat - floor($lat);
    $retval = $retval . sprintf("%06.3f", round($lat * 60, 3)) . '\'';

    return $retval;
}

//perform str_rot13 without renaming HTML-Tags
function str_rot13_html($str)
{
    $delimiter[0][0] = '&'; // start-char
    $delimiter[0][1] = ';'; // end-char
    $delimiter[1][0] = '<';
    $delimiter[1][1] = '>';
    $delimiter[2][0] = '[';
    $delimiter[2][1] = ']';

    $retval = '';

    while (strlen($retval) < strlen($str)) {
        $nNextStart = false;
        $sNextEndChar = '';
        foreach ($delimiter AS $del) {
            $nThisStart = strpos($str, $del[0], strlen($retval));

            if ($nThisStart !== false)
                if (($nNextStart > $nThisStart) || ($nNextStart === false)) {
                    $nNextStart = $nThisStart;
                    $sNextEndChar = $del[1];
                }
        }

        if ($nNextStart === false) {
            $retval .= str_rot13(substr($str, strlen($retval), strlen($str) - strlen($retval)));
        } else {
            // crypted part
            $retval .= str_rot13(substr($str, strlen($retval), $nNextStart - strlen($retval)));

            // uncrypted part
            $nNextEnd = strpos($str, $sNextEndChar, $nNextStart);

            if ($nNextEnd === false)
                $retval .= substr($str, $nNextStart, strlen($str) - strlen($retval));
            else
                $retval .= substr($str, $nNextStart, $nNextEnd - $nNextStart + 1);
        }
    }

    return $retval;
}

?>
