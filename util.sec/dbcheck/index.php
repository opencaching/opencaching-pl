<?php

$regex_htmltag = '';
$regex_entity = '';

$rootpath = '../../';
require('../../lib/common.inc.php');
require('../../lib/class.inputfilter.php');
require('../../lib/caches.inc.php');

if ($error == true)
    die('Error after require(common.inc.php)');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$action = strtolower($action);

if ($action == '') {
    // do nothing
} else if ($action == 'htmldesc') {
    // argument id

    echo '<html><body>';

    $descid = isset($_REQUEST['id']) ? $_REQUEST['id'] : '0';
    $rs = mysql_query('SELECT `desc` FROM cache_desc WHERE id=\'' . sql_escape($descid) . '\'', $dblink);
    $r = mysql_fetch_array($rs);

    $myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
    $fdesc = $myFilter->process($r['desc']);

    echo '<b>- Orginal HTML -</b><br /><br />' . "\n";
    echo nl2br(htmlspecialchars($r['desc'])) . "\n";
    echo '<br /><br /><b>- Korrigiertes HTML -</b><br /><br />' . "\n";
    echo nl2br(htmlspecialchars($fdesc)) . "\n";
    echo '<br /><br /><b>- Orginal Vorschau -</b><br /><br />' . "\n";
    echo $r['desc'] . "\n";
    echo '<br /><br /><b>- Korrigierte Vorschau -</b><br /><br />' . "\n";
    echo $fdesc . "\n";

    echo '</body></html>';
    exit;
} else if ($action == 'clearqueries') {
    mysql_query('DELETE FROM queries WHERE last_queried<\'' . date('Y-m-d H:i:s', time() - 36000) . '\'', $dblink);
} else if ($action == 'recalclogpics') {
    $rs = mysql_query('SELECT `id` FROM cache_logs', $dblink);
    while ($r = mysql_fetch_array($rs)) {
        $rsPicCount = mysql_query('SELECT COUNT(*) count FROM pictures WHERE object_type=1 AND object_id=\'' . sql_escape($r['id']) . '\'', $dblink);
        $rPicCount = mysql_fetch_array($rsPicCount);
        mysql_free_result($rsPicCount);

        mysql_query('UPDATE cache_logs SET picturescount=\'' . sql_escape($rPicCount['count']) . '\' WHERE id=\'' . sql_escape($r['id']) . '\'', $dblink);
    }
    mysql_free_result($rs);
} else if ($action == 'removevisits') {
    $rs = mysql_query('SELECT `cache_id`, `user_id` FROM cache_visits', $dblink);
    while ($r = mysql_fetch_array($rs)) {
        if (!isUser($r['user_id'])) {
            mysql_query('DELETE FROM cache_visits WHERE cache_id=\'' . sql_escape($r['cache_id']) . '\' AND user_id=\'' . sql_escape($r['user_id']) . '\'', $dblink);
        } else {
            $rsCaches = mysql_query('SELECT COUNT(*) count FROM caches WHERE cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
            $rCache = mysql_fetch_array($rsCaches);
            mysql_free_result($rsCaches);

            if ($rCache['count'] == 0)
                mysql_query('DELETE FROM cache_visits WHERE cache_id=\'' . sql_escape($r['cache_id']) . '\' AND user_id=\'' . sql_escape($r['user_id']) . '\'', $dblink);
        }
    }
    mysql_free_result($rs);
}
else if ($action == 'recalccachevisits') {
    $rs = mysql_query('SELECT `cache_id` FROM caches', $dblink);
    while ($r = mysql_fetch_array($rs)) {
        $rsLog = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE `deleted`=0 AND `type`=1 AND cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
        $rFound = mysql_fetch_array($rsLog);
        mysql_free_result($rsLog);

        $rsLog = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE `deleted`=0 AND `type`=2 AND cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
        $rNotFound = mysql_fetch_array($rsLog);
        mysql_free_result($rsLog);

        $rsLog = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE `deleted`=0 AND `type`=3 AND cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
        $rNotes = mysql_fetch_array($rsLog);
        mysql_free_result($rsLog);

        $rsLog = mysql_query('SELECT COUNT(*) count FROM pictures WHERE object_type=2 AND object_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
        $rPictures = mysql_fetch_array($rsLog);
        mysql_free_result($rsLog);

        $rsLog = mysql_query('SELECT `date` FROM cache_logs WHERE deleted=0 AND type=1 AND cache_id=\'' . sql_escape($r['cache_id']) . '\' ORDER BY `date` DESC LIMIT 1', $dblink);
        $rLastLog = mysql_fetch_array($rsLog);
        mysql_free_result($rsLog);

        mysql_query('UPDATE caches SET last_found=\'' . sql_escape($rLastLog['date']) . '\', picturescount=\'' . sql_escape($rPictures['count']) . '\', founds=\'' . sql_escape($rFound['count']) . '\', notfounds=\'' . sql_escape($rNotFound['count']) . '\', notes=\'' . sql_escape($rNotes['count']) . '\' WHERE cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
    }
    mysql_free_result($rs);
} else if ($action == 'recalcuserstat') {
    $rs = mysql_query('SELECT user_id FROM `user`', $dblink);
    while ($r = mysql_fetch_array($rs)) {
        // notfounds_count
        $rsLog = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE `deleted`=0 AND `type`=2 AND user_id=\'' . sql_escape($r['user_id']) . '\'', $dblink);
        $rNotfounds_count = mysql_fetch_array($rsLog);
        mysql_free_result($rsLog);

        // founds_count
        $rsLog = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE `deleted`=0 AND `type`=1 AND user_id=\'' . sql_escape($r['user_id']) . '\'', $dblink);
        $rFounds_count = mysql_fetch_array($rsLog);
        mysql_free_result($rsLog);

        // log_notes_count
        $rsLog = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE `deleted`=0 AND `type`=3 AND user_id=\'' . sql_escape($r['user_id']) . '\'', $dblink);
        $rLog_notes_count = mysql_fetch_array($rsLog);
        mysql_free_result($rsLog);

        // hidden_count
        $rsLog = mysql_query('SELECT COUNT(*) count FROM caches WHERE user_id=\'' . sql_escape($r['user_id']) . '\'', $dblink);
        $rHidden_count = mysql_fetch_array($rsLog);
        mysql_free_result($rsLog);

        // cache_watches
        $rsLog = mysql_query('SELECT COUNT(*) count FROM cache_watches WHERE user_id=\'' . sql_escape($r['user_id']) . '\'', $dblink);
        $rCache_watches = mysql_fetch_array($rsLog);
        mysql_free_result($rsLog);

        mysql_query('UPDATE `user` SET notfounds_count=\'' . sql_escape($rNotfounds_count['count']) . '\', founds_count=\'' . sql_escape($rFounds_count['count']) . '\', log_notes_count=\'' . sql_escape($rLog_notes_count['count']) . '\', hidden_count=\'' . sql_escape($rHidden_count['count']) . '\', cache_watches=\'' . sql_escape($rCache_watches['count']) . '\' WHERE user_id=\'' . sql_escape($r['user_id']) . '\'', $dblink);
    }
    mysql_free_result($rs);
} else
    die('Unknown action: ' . $action);
?>
<html>
    <head>
        <title>Datenbank Integrit�tscheck</title>
    </head>
    <body>
        <?php
        echo '<b>Tabelle cache_desc</b><br/>';

// Tabelle dache_desc
        $rs = mysql_query('SELECT `id`, `cache_id`, `language`, `desc`, `desc_html`, `hint`, `short_desc`, `last_modified`, `uuid` FROM `cache_desc`', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            $rsCache = mysql_query('SELECT `desc_languages` FROM `caches` WHERE cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);

            // cache_id
            // existiert der cache?
            if (mysql_num_rows($rsCache) == 0) {
                echo 'Desc ' . $r['id'] . ': Cache ' . $r['cache_id'] . ' existiert nicht<br/>';
                $rCache['desc_languages'] = '';
            } else
                $rCache = mysql_fetch_array($rsCache);

            // language
            // existiert?
            $rsLang = mysql_query('SELECT * FROM languages WHERE short=\'' . sql_escape($r['language']) . '\'', $dblink);
            if (mysql_num_rows($rsLang) == 0)
                echo 'Desc ' . $r['id'] . ': Sprache ' . $r['language'] . ' existiert nicht<br/>';
            mysql_free_result($rsLang);

            // noch eine Beschreibung mit der Sprache?
            $rsDesc = mysql_query('SELECT COUNT(*) count FROM cache_desc WHERE cache_id=\'' . sql_escape($r['cache_id']) . '\' AND language=\'' . sql_escape($r['language']) . '\'', $dblink);
            $rDesc = mysql_fetch_array($rsDesc);
            if ($rDesc['count'] != 1)
                echo 'Desc ' . $r['id'] . ': Zu viele Beschreibungen (' . $rDesc['count'] . ') in der Sprache ' . $r['language'] . '<br/>';
            mysql_free_result($rsDesc);

            // beschreibung in cache vermerkt?
            if (strpos(',' . $rCache['desc_languages'] . ',', ',' . $r['language'] . ',') === false)
                echo 'Desc ' . $r['id'] . ': Beschreibung nicht in Cache vermerkt (' . $r['language'] . ')<br/>';

            // desc und desc_html
            // cache_desc 1 oder 0?
            if (($r['desc_html'] != 0) && ($r['desc_html'] != 1))
                echo 'Desc ' . $r['id'] . ': desc_html hat einen ung�ltigen Wert ' . $r['desc_html'] . '<br/>';

            if ($r['desc_html'] == 1) {
                // html-beschreibung durch den filter gegangen?

                $myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
                $fdesc = $myFilter->process($r['desc']);

                if ($fdesc != $r['desc'])
                    echo 'Desc ' . $r['id'] . ': HTML-Bechreibung falsch gefiltert [<a href="index.php?action=htmldesc&id=' . urlencode($r['id']) . '">Ansicht</a>]<br/>';
            }
            else {
                // beschreibung mit entities oder tags?
                $desc = $r['desc'];
                $desc = str_replace('<br />', '', $desc);

                $bFound = false;
                if (strpos($desc, '<') !== false)
                    $bFound = true;
                if (strpos($desc, '>') !== false)
                    $bFound = true;

                if ($bFound == true)
                    echo 'Desc ' . $r['id'] . ': Die Beschreibung enh�lt m�gl. HTML-Tags<br/>';
            }

            // hint und short_desc

            $hint = $r['hint'];
            $hint = str_replace('<br />', '', $hint);

            $bFound = false;
            if (strpos($hint, '<') !== false)
                $bFound = true;
            if (strpos($hint, '>') !== false)
                $bFound = true;

            if ($bFound == true)
                echo 'Desc ' . $r['id'] . ': Der Hint enh�lt m�gl. HTML-Tags<br/>';

            $short_desc = $r['short_desc'];

            $bFound = false;
            if (strpos($short_desc, '<') !== false)
                $bFound = true;
            if (strpos($short_desc, '>') !== false)
                $bFound = true;

            if ($bFound == true)
                echo 'Desc ' . $r['id'] . ': Die short_desc enh�lt m�gl. HTML-Tags<br/>';


            // last_modified

            if (strtotime($r['last_modified']) > time())
                echo 'Desc ' . $r['id'] . ': Last_modified liegt in der Zukunft<br/>';

            if (($r['last_modified'] == '0000-00-00 00:00:00') || ($r['last_modified'] == null))
                echo 'Desc ' . $r['id'] . ': Null-Datum in Feld last_modified<br/>';

            // uuid
            if (!isuuid($r['uuid']))
                echo 'Desc ' . $r['id'] . ': uuid ung�ltig ' . $r['uuid'] . '<br/>';

            mysql_free_result($rsCache);
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle cache_logs</b> [<a href="index.php?action=recalclogpics">picturescount neu berechnen</a>]<br/>';

        $rs = mysql_query('SELECT `id`, `cache_id`, `user_id`, `type`, `date`, `text`, `last_modified`, `uuid`, `picturescount`, `date_created`, `owner_notified` FROM cache_logs', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            // cache_id

            $rsCache = mysql_query('SELECT COUNT(*) count FROM caches WHERE cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
            $rCache = mysql_fetch_array($rsCache);
            if ($rCache['count'] != 1)
                echo 'Cachelog ' . $r['id'] . ': Cache ' . $r['cache_id'] . ' nicht vorhanden<br/>';
            mysql_free_result($rsCache);

            // user_id

            if (!isUser($r['user_id']))
                echo 'Cachelog ' . $r['id'] . ': User ' . $r['user_id'] . ' nicht vorhanden<br/>';

            // type

            $bFound = false;
            for ($i = 0; $i < count($log_types); $i++)
                if ($log_types[$i]['id'] == $r['type']) {
                    $bFound = true;
                    break;
                }

            if (!$bFound)
                echo 'Cachelog ' . $r['id'] . ': Type nicht in caches.inc.php vermerkt (Type=' . $r['type'] . ')<br/>';

            $rsType = mysql_query('SELECT COUNT(*) count FROM log_types WHERE id=\'' . $r['type'] . '\'');
            $rType = mysql_fetch_array($rsType);
            if ($rType['count'] != 1)
                echo 'Cachelog ' . $r['id'] . ': Type nicht in DB vorhanden type=' . $r['type'] . '<br/>';
            mysql_free_result($rsType);

            // date

            if (($r['date'] == '0000-00-00 00:00:00') || ($r['date'] == null))
                echo 'Cachelog ' . $r['id'] . ': Null-Datum in Feld date<br/>';

            // text
            // html?
            $text = $r['text'];
            $text = str_replace('<br />', '', $text);

            $bFound = false;
            if (strpos($text, '<') !== false)
                $bFound = true;
            if (strpos($text, '>') !== false)
                $bFound = true;

            if ($bFound == true)
                echo 'Cachelog ' . $r['id'] . ': Der text enh�lt m�gl. HTML-Tags<br/>';

            // last_modified

            if (($r['last_modified'] == '0000-00-00 00:00:00') || ($r['last_modified'] == null))
                echo 'Cachelog ' . $r['id'] . ': Null-Datum in Feld last_modified<br/>';

            if (strtotime($r['last_modified']) > time())
                echo 'Cachelog ' . $r['id'] . ': last_modified liegt in der Zukunft<br/>';

            // uuid

            if (!isuuid($r['uuid']))
                echo 'Cachelog ' . $r['id'] . ': uuid ung�ltig ' . $r['uuid'] . '<br/>';

            // picturescount

            $rsPictures = mysql_query('SELECT COUNT(*) count FROM pictures WHERE object_id=\'' . sql_escape($r['id']) . '\' AND object_type=1', $dblink);
            $rPictures = mysql_fetch_array($rsPictures);
            if ($rPictures['count'] != $r['picturescount'])
                echo 'Cachelog ' . $r['id'] . ': picturescount falsch ist=' . $r['picturescount'] . '; soll=' . $rPictures['count'] . '<br/>';
            mysql_free_result($rsPictures);

            // date_created

            if (($r['date_created'] == '0000-00-00 00:00:00') || ($r['date_created'] == null))
                echo 'Cachelog ' . $r['id'] . ': Null-Datum in Feld date_created<br/>';

            if (strtotime($r['date_created']) > time())
                echo 'Cachelog ' . $r['id'] . ': date_created liegt in der Zukunft<br/>';

            // owner_notified
            if (($r['owner_notified'] != 0) && ($r['owner_notified'] != 1))
                echo 'Cachelog ' . $r['id'] . ': owner_notified ung�ltig ist=' . $r['owner_notified'] . '<br/>';
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle cache_size</b><br/>';
        $rs = mysql_query('SELECT `id`, `de`, `en` FROM cache_size', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            $bFound = false;
            for ($i = 0; $i < count($cache_size); $i++) {
                if ($cache_size[$i]['id'] == $r['id']) {
                    $bFound = true;

                    if ($cache_size[$i]['de'] != $r['de'])
                        echo 'cache_size ' . $r['id'] . ': de stimmt nicht mit caches.inc.php �berein<br/>';

                    if ($cache_size[$i]['en'] != $r['en'])
                        echo 'cache_size ' . $r['id'] . ': en stimmt nicht mit caches.inc.php �berein<br/>';

                    break;
                }
            }
            if (!$bFound)
                echo 'cache_size ' . $r['id'] . ': nicht in caches.inc.php<br/>';

            if ($r['de'] == '')
                echo 'cache_size ' . $r['id'] . ': de-Feld ist leer<br/>';

            if ($r['en'] == '')
                echo 'cache_size ' . $r['id'] . ': en-Feld ist leer<br/>';
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle cache_status</b><br/>';
        $rs = mysql_query('SELECT `id`, `de`, `en` FROM cache_status', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            $bFound = false;
            for ($i = 0; $i < count($cache_status); $i++) {
                if ($cache_status[$i]['id'] == $r['id']) {
                    $bFound = true;

                    if ($cache_status[$i]['de'] != $r['de'])
                        echo 'cache_status ' . $r['id'] . ': de stimmt nicht mit caches.inc.php �berein<br/>';

                    if ($cache_status[$i]['en'] != $r['en'])
                        echo 'cache_status ' . $r['id'] . ': en stimmt nicht mit caches.inc.php �berein<br/>';

                    break;
                }
            }
            if (!$bFound)
                echo 'cache_status ' . $r['id'] . ': nicht in caches.inc.php<br/>';

            if ($r['de'] == '')
                echo 'cache_status ' . $r['id'] . ': de-Feld ist leer<br/>';

            if ($r['en'] == '')
                echo 'cache_status ' . $r['id'] . ': en-Feld ist leer<br/>';
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle cache_type</b><br/>';
        $rs = mysql_query('SELECT `id`, `short`, `de`, `en`, `icon_large` FROM cache_type', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            $bFound = false;
            for ($i = 0; $i < count($cache_types); $i++) {
                if ($cache_types[$i]['id'] == $r['id']) {
                    $bFound = true;

                    if ($cache_types[$i]['short'] != $r['short'])
                        echo 'cache_type ' . $r['id'] . ': short stimmt nicht mit caches.inc.php �berein<br/>';

                    if ($cache_types[$i]['de'] != $r['de'])
                        echo 'cache_type ' . $r['id'] . ': de stimmt nicht mit caches.inc.php �berein<br/>';

                    if ($cache_types[$i]['en'] != $r['en'])
                        echo 'cache_type ' . $r['id'] . ': en stimmt nicht mit caches.inc.php �berein<br/>';

                    break;
                }
            }
            if (!$bFound)
                echo 'cache_type ' . $r['id'] . ': nicht in caches.inc.php<br/>';

            if ($r['icon_large'] == '')
                echo 'cache_type ' . $r['id'] . ': icon_large-Feld ist leer<br/>';

            if ($r['short'] == '')
                echo 'cache_type ' . $r['id'] . ': short-Feld ist leer<br/>';

            if ($r['de'] == '')
                echo 'cache_type ' . $r['id'] . ': de-Feld ist leer<br/>';

            if ($r['en'] == '')
                echo 'cache_type ' . $r['id'] . ': en-Feld ist leer<br/>';
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle cache_visits</b> [<a href="index.php?action=removevisits">unbekannte entfernen</a>]<br/>';
        $rs = mysql_query('SELECT `cache_id`, `user_id`, `count`, `last_visited` FROM cache_visits', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            // cache_id

            $rsCache = mysql_query('SELECT COUNT(*) count FROM caches WHERE cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
            $rCache = mysql_fetch_array($rsCache);
            if ($rCache['count'] != 1)
                echo 'cache_visits ' . $r['id'] . ': Cache ' . $r['cache_id'] . ' nicht vorhanden<br/>';
            mysql_free_result($rsCache);

            // user_id

            if (!isUser($r['user_id'], true))
                echo 'cache_visits ' . $r['id'] . ': User ' . $r['user_id'] . ' nicht vorhanden<br/>';

            if ($r['count'] < 0)
                echo 'cache_visits ' . $r['id'] . ': count kleiner 0<br/>';

            if (strtotime($r['last_visited']) > time())
                echo 'cache_visits ' . $r['id'] . ': last_visited in der Zukunft<br/>';
        }
        mysql_free_result($rs);


        echo '<br /><b>Tabelle cache_watches</b><br/>';
        $rs = mysql_query('SELECT `cache_id`, `user_id`, `last_executed` FROM cache_watches', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            // cache_id

            $rsCache = mysql_query('SELECT COUNT(*) count FROM caches WHERE cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
            $rCache = mysql_fetch_array($rsCache);
            if ($rCache['count'] != 1)
                echo 'cache_watches ' . $r['id'] . ': Cache ' . $r['cache_id'] . ' nicht vorhanden<br/>';
            mysql_free_result($rsCache);

            // user_id

            if (!isUser($r['user_id']))
                echo 'cache_watches ' . $r['id'] . ': User ' . $r['user_id'] . ' nicht vorhanden<br/>';

            if (strtotime($r['last_executed']) > time())
                echo 'cache_watches ' . $r['id'] . ': last_executed in der Zukunft<br/>';
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle caches</b> [<a href="index.php?action=recalccachevisits">summen neu berechnen</a>]<br/>';
        $rs = mysql_query('SELECT `cache_id`,`user_id`,`name`,`longitude`,`latitude`,`last_modified`,`date_created`,`type`,`status`,`country`,`date_hidden`,`founds`,`notfounds`,`notes`,`last_found`,`desc_languages`,`size`,`difficulty`,`terrain`,`uuid`,`watcher`,`logpw`,`picturescount` FROM `caches`', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            // user_id

            if (!isUser($r['user_id']))
                echo 'caches ' . $r['cache_id'] . ': User ' . $r['user_id'] . ' nicht vorhanden<br/>';

            if ($r['name'] == '')
                echo 'caches ' . $r['cache_id'] . ': name ist leer<br/>';

            if (($r['longitude'] < -180) || ($r['longitude'] > 180))
                echo 'caches ' . $r['cache_id'] . ': longitude ung�ltig<br/>';

            if (($r['latitude'] < -90) || ($r['latitude'] > 90))
                echo 'caches ' . $r['cache_id'] . ': latitude ung�ltig<br/>';

            if (($r['last_modified'] == '0000-00-00 00:00:00') || ($r['last_modified'] == null))
                echo 'caches ' . $r['cache_id'] . ': Null-Datum in Feld last_modified<br/>';
            if (strtotime($r['last_modified']) > time())
                echo 'caches ' . $r['cache_id'] . ': last_modified liegt in der Zukunft<br/>';

            if (($r['date_created'] == '0000-00-00 00:00:00') || ($r['date_created'] == null))
                echo 'caches ' . $r['cache_id'] . ': Null-Datum in Feld date_created<br/>';
            if (strtotime($r['date_created']) > time())
                echo 'caches ' . $r['cache_id'] . ': date_created liegt in der Zukunft<br/>';

            // type

            $rsC = mysql_query('SELECT COUNT(*) count FROM cache_type WHERE id=\'' . sql_escape($r['type']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($rC['count'] != 1)
                echo 'caches ' . $r['cache_id'] . ': type existiert nicht<br/>';
            mysql_free_result($rsC);

            // status

            $rsC = mysql_query('SELECT COUNT(*) count FROM cache_status WHERE id=\'' . sql_escape($r['status']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($rC['count'] != 1)
                echo 'caches ' . $r['cache_id'] . ': status existiert nicht<br/>';
            mysql_free_result($rsC);

            // country

            $rsC = mysql_query('SELECT COUNT(*) count FROM countries WHERE short=\'' . sql_escape($r['country']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($rC['count'] != 1)
                echo 'caches ' . $r['cache_id'] . ': country existiert nicht<br/>';
            mysql_free_result($rsC);

            // date_hidden

            if (($r['date_hidden'] == '0000-00-00 00:00:00') || ($r['date_hidden'] == null))
                echo 'caches ' . $r['cache_id'] . ': Null-Datum in Feld date_hidden<br/>';

            // founds
            $rsC = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE `deleted`=0 AND type=1 AND cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($rC['count'] != $r['founds'])
                echo 'caches ' . $r['cache_id'] . ': anzahl founds stimmt nicht<br/>';
            mysql_free_result($rsC);

            // notfounds
            $rsC = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE deleted=0 AND type=2 AND cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($rC['count'] != $r['notfounds'])
                echo 'caches ' . $r['cache_id'] . ': anzahl notfounds stimmt nicht<br/>';
            mysql_free_result($rsC);

            // notes
            $rsC = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE deleted=0 AND type=3 AND cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($rC['count'] != $r['notes'])
                echo 'caches ' . $r['cache_id'] . ': anzahl notes stimmt nicht<br/>';
            mysql_free_result($rsC);

            // last_found
            $rsC = mysql_query('SELECT `date` FROM cache_logs WHERE deleted=0 AND type=1 AND cache_id=\'' . sql_escape($r['cache_id']) . '\' ORDER BY `date` DESC LIMIT 1', $dblink);
            if (mysql_num_rows($rsC) == 0) {
                if (($r['last_found'] != null) && ($r['last_found'] != '0000-00-00 00:00:00'))
                    echo 'caches ' . $r['cache_id'] . ': last_found stimmt nicht<br/>';
            }
            else {
                $rC = mysql_fetch_array($rsC);
                if ($rC['date'] != $r['last_found'])
                    echo 'caches ' . $r['cache_id'] . ': last_found stimmt nicht<br/>';
            }
            mysql_free_result($rsC);

            // desc_languages
            $desc_langs = explode(',', $r['desc_languages']);
            for ($i = 0; $i < count($desc_langs); $i++) {
                $rsC = mysql_query('SELECT COUNT(*) count FROM cache_desc WHERE language=\'' . sql_escape($desc_langs[$i]) . '\' AND cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
                $rC = mysql_fetch_array($rsC);
                if ($rC['count'] != 1)
                    echo 'caches ' . $r['cache_id'] . ': beschreibung ' . $desc_langs[$i] . ' nicht gefunden<br/>';
                mysql_free_result($rsC);
            }

            // size

            $rsC = mysql_query('SELECT COUNT(*) count FROM cache_size WHERE id=\'' . sql_escape($r['size']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($rC['count'] != 1)
                echo 'caches ' . $r['cache_id'] . ': cache-size existiert nicht<br/>';
            mysql_free_result($rsC);

            // difficulty
            if (($r['difficulty'] < 2) || ($r['difficulty'] > 10))
                echo 'caches ' . $r['cache_id'] . ': difficulty ung�ltig<br/>';

            // terrain
            if (($r['terrain'] < 2) || ($r['terrain'] > 10))
                echo 'caches ' . $r['cache_id'] . ': terrain ung�ltig<br/>';

            // uuid
            if (!isuuid($r['uuid']))
                echo 'caches ' . $r['cache_id'] . ': uuid ung�ltig ' . $r['uuid'] . '<br/>';

            // watcher
            $rsC = mysql_query('SELECT COUNT(*) count FROM cache_watches WHERE cache_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($r['watcher'] == null)
                $r['watcher'] = 0;
            if ($rC['count'] != $r['watcher'])
                echo 'caches ' . $r['cache_id'] . ': anzahl watcher stimmt nicht<br/>';
            mysql_free_result($rsC);

            // logpw
            // nix zu tun
            // picturescount

            $rsC = mysql_query('SELECT COUNT(*) count FROM pictures WHERE object_type=2 AND object_id=\'' . sql_escape($r['cache_id']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($rC['count'] != $r['picturescount'])
                echo 'caches ' . $r['cache_id'] . ': anzahl picturescount stimmt nicht<br/>';
            mysql_free_result($rsC);

            // cache doppelt gepostet?
            $rsD = mysql_query('SELECT COUNT(*) count FROM caches WHERE name=\'' . sql_escape($r['name']) . '\' AND user_id=\'' . sql_escape($r['user_id']) . '\'');
            $rD = mysql_fetch_array($rsD);
            if ($rD['count'] > 1) {
                if ($r['status'] == 3)
                    echo 'caches ' . $r['cache_id'] . ': Cache doppelt? (Archiviert)<br/>';
                else
                    echo 'caches ' . $r['cache_id'] . ': Cache doppelt?<br/>';
            }
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle countries</b><br/>';
        $rs = mysql_query('SELECT `country_id`,`de`,`en`,`pl`,`short`,`list_default_de`,`sort_de`,`list_default_en`, `sort_en`,`list_default_pl`, `sort_pl` FROM `countries`', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            // de
            if ($r['de'] == '')
                echo 'countries ' . $r['country_id'] . ': de ist leer<br/>';

            // en
            if ($r['en'] == '')
                echo 'countries ' . $r['country_id'] . ': en ist leer<br/>';

            if ($r['pl'] == '')
                echo 'countries ' . $r['country_id'] . ': en ist leer<br/>';

            //short

            $rsC = mysql_query('SELECT COUNT(*) count FROM countries WHERE short=\'' . sql_escape($r['short']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($rC['count'] != 1)
                echo 'countries ' . $r['country_id'] . ': short mehrfach vorhanden<br/>';
            mysql_free_result($rsC);

            if (($r['list_default_de'] != 0) && ($r['list_default_de'] != 1))
                echo 'countries ' . $r['country_id'] . ': list_default_de ung�ltig<br/>';

            if (($r['list_default_en'] != 0) && ($r['list_default_en'] != 1))
                echo 'countries ' . $r['country_id'] . ': list_default_en ung�ltig<br/>';

            if (($r['list_default_pl'] != 0) && ($r['list_default_pl'] != 1))
                echo 'countries ' . $r['country_id'] . ': list_default_pl ung�ltig<br/>';

            if ($r['sort_de'] != strtolower($r['sort_de']))
                echo 'countries ' . $r['country_id'] . ': sort_de nicht kleingeschrieben<br/>';

            if ($r['sort_en'] != strtolower($r['sort_en']))
                echo 'countries ' . $r['country_id'] . ': sort_en nicht kleingeschrieben<br/>';

            if ($r['sort_pl'] != strtolower($r['sort_pl']))
                echo 'countries ' . $r['country_id'] . ': sort_en nicht kleingeschrieben<br/>';
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle languages</b><br/>';
        $rs = mysql_query('SELECT `id`,`de`,`en`,`pl`,`short`,`list_default_de`,`list_default_en`,`list_default_pl` FROM `languages`', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            // de
            if ($r['de'] == '')
                echo 'languages ' . $r['id'] . ': de ist leer<br/>';

            // en
            if ($r['en'] == '')
                echo 'languages ' . $r['id'] . ': en ist leer<br/>';

            // pl
            if ($r['pl'] == '')
                echo 'languages ' . $r['id'] . ': en ist leer<br/>';

            //short

            $rsC = mysql_query('SELECT COUNT(*) count FROM languages WHERE short=\'' . sql_escape($r['short']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($rC['count'] != 1)
                echo 'languages ' . $r['id'] . ': short mehrfach vorhanden<br/>';
            mysql_free_result($rsC);

            if (($r['list_default_de'] != 0) && ($r['list_default_de'] != 1))
                echo 'languages ' . $r['id'] . ': list_default_de ung�ltig<br/>';

            if (($r['list_default_en'] != 0) && ($r['list_default_en'] != 1))
                echo 'languages ' . $r['id'] . ': list_default_en ung�ltig<br/>';

            if (($r['list_default_pl'] != 0) && ($r['list_default_pl'] != 1))
                echo 'languages ' . $r['id'] . ': list_default_pl ung�ltig<br/>';
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle log_types</b><br/>';
        $rs = mysql_query('SELECT `id`,`en`,`pl`,`icon_small` FROM `log_types`', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            $bFound = false;
            for ($i = 0; $i < count($log_types); $i++)
                if ($log_types[$i]['id'] == $r['id']) {
                    $bFound = true;

                    if ($log_types[$i]['en'] != $r['en'])
                        echo 'log_types ' . $r['id'] . ': en stimmt nicht mit caches.inc.php �berein<br/>';
                    if ($log_types[$i]['pl'] != $r['pl'])
                        echo 'log_types ' . $r['id'] . ': en stimmt nicht mit caches.inc.php �berein<br/>';

                    break;
                }

            if ($bFound == false)
                echo 'log_types ' . $r['id'] . ': steht nicht in caches.inc.php<br/>';

            if ($r['de'] == '')
                echo 'log_types ' . $r['id'] . ': de ist leer<br/>';

            if ($r['en'] == '')
                echo 'log_types ' . $r['id'] . ': en ist leer<br/>';

            if ($r['pl'] == '')
                echo 'log_types ' . $r['id'] . ': en ist leer<br/>';

            if ($r['icon_small'] == '')
                echo 'log_types ' . $r['id'] . ': icon_small ist leer<br/>';
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle pictures</b><br/>';
        $rs = mysql_query('SELECT `id`,`uuid`,`url`,`last_modified`,`title`,`date_created`,`object_id`,`object_type`,`user_id`,`thumb_url`,`thumb_last_generated`,`spoiler`,`local`,`unknown_format`,`display` FROM `pictures`', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            // uuid
            if (!isuuid($r['uuid']))
                echo 'pictures ' . $r['id'] . ': uuid ung�ltig ' . $r['uuid'] . '<br/>';

            // url
            if (substr($r['url'], 0, 7) != 'http://')
                echo 'pictures ' . $r['id'] . ': url ung�ltig ' . htmlspecialchars($r['url']) . '<br/>';

            // last_modified
            if (($r['last_modified'] == '0000-00-00 00:00:00') || ($r['last_modified'] == null))
                echo 'pictures ' . $r['id'] . ': Null-Datum in Feld last_modified<br/>';

            // title
            if ($r['title'] == '')
                echo 'pictures ' . $r['id'] . ': Titel ist leer<br/>';

            // date_created
            if (($r['date_created'] == '0000-00-00 00:00:00') || ($r['date_created'] == null))
                echo 'pictures ' . $r['id'] . ': Null-Datum in Feld date_created<br/>';

            // object_type und object_id
            switch ($r['object_type']) {
                case 1:
                    $rsCache = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE deleted=0 AND id=\'' . sql_escape($r['object_id']) . '\'', $dblink);
                    $rCache = mysql_fetch_array($rsCache);
                    if ($rCache['count'] != 1)
                        echo 'pictures ' . $r['id'] . ': cache_log ' . $r['object_id'] . ' nicht vorhanden<br/>';
                    mysql_free_result($rsCache);
                    break;

                case 2:
                    $rsCache = mysql_query('SELECT COUNT(*) count FROM caches WHERE cache_id=\'' . sql_escape($r['object_id']) . '\'', $dblink);
                    $rCache = mysql_fetch_array($rsCache);
                    if ($rCache['count'] != 1)
                        echo 'pictures ' . $r['id'] . ': Cache ' . $r['object_id'] . ' nicht vorhanden<br/>';
                    mysql_free_result($rsCache);
                    break;

                default:
                    echo 'pictures ' . $r['id'] . ': unbekannter object type<br/>';
                    break;
            }

            // user_id

            if (!isUser($r['user_id']))
                echo 'pictures ' . $r['id'] . ': User ' . $r['user_id'] . ' nicht vorhanden<br/>';

            // spoiler
            if (($r['spoiler'] != null) && ($r['spoiler'] != 0) && ($r['spoiler'] != 1))
                echo 'pictures ' . $r['id'] . ': Feld spoiler ung�ltig<br/>';

            // local
            if (($r['local'] != null) && ($r['local'] != 0) && ($r['local'] != 1))
                echo 'pictures ' . $r['id'] . ': Feld local ung�ltig<br/>';

            // unknown_format
            if (($r['unknown_format'] != null) && ($r['unknown_format'] != 0) && ($r['unknown_format'] != 1))
                echo 'pictures ' . $r['id'] . ': Feld unknown_format ung�ltig<br/>';

            // display
            if (($r['display'] != null) && ($r['display'] != 0) && ($r['display'] != 1))
                echo 'pictures ' . $r['id'] . ': Feld display ung�ltig<br/>';
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle queries</b><br/>';
        $rs = mysql_query('SELECT `user_id`, `uuid` FROM `queries`', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            // user_id

            if (!isUser($r['user_id'], true))
                echo 'queries ' . $r['id'] . ': User ' . $r['user_id'] . ' missing<br/>';

            // uuid

            if (!isuuid($r['uuid']))
                echo 'queries ' . $r['id'] . ': uuid invalidly ' . $r['uuid'] . '<br/>';
        }
        mysql_free_result($rs);

        $rs = mysql_query('SELECT count(*) count FROM queries WHERE last_queried<\'' . date('Y-m-d H:i:s', time() - 36000) . '\'', $dblink);
        $r = mysql_fetch_array($rs);
        echo $r['count'] . ' queries die �lter als 10h sind [<a href="index.php?action=clearqueries">L�schen</a>]<br/>';
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle removed_objects</b><br/>';
        $rs = mysql_query('SELECT `id`,`localid`,`uuid`,`type`,`removed_date` FROM `removed_objects`', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            // kucken ob das object wirklich entfernt wurde
            switch ($r['type']) {
                case 1:
                    $rsCache = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE deleted=0 AND id=\'' . sql_escape($r['localid']) . '\'', $dblink);
                    $rCache = mysql_fetch_array($rsCache);
                    if ($rCache['count'] != 0)
                        echo 'removed_objects ' . $r['id'] . ': cache_log ' . $r['localid'] . ' still available<br/>';
                    mysql_free_result($rsCache);
                    break;
                case 2:
                    $rsCache = mysql_query('SELECT COUNT(*) count FROM caches WHERE cache_id=\'' . sql_escape($r['localid']) . '\'', $dblink);
                    $rCache = mysql_fetch_array($rsCache);
                    if ($rCache['count'] != 0)
                        echo 'removed_objects ' . $r['id'] . ': caches ' . $r['localid'] . ' still available<br/>';
                    mysql_free_result($rsCache);
                    break;
                case 3:
                    $rsCache = mysql_query('SELECT COUNT(*) count FROM cache_desc WHERE id=\'' . sql_escape($r['localid']) . '\'', $dblink);
                    $rCache = mysql_fetch_array($rsCache);
                    if ($rCache['count'] != 0)
                        echo 'removed_objects ' . $r['id'] . ': cache_desc ' . $r['localid'] . ' still available<br/>';
                    mysql_free_result($rsCache);
                    break;
                case 4:
                    $rsCache = mysql_query('SELECT COUNT(*) count FROM `user` WHERE user_id=\'' . sql_escape($r['localid']) . '\'', $dblink);
                    $rCache = mysql_fetch_array($rsCache);
                    if ($rCache['count'] != 0)
                        echo 'removed_objects ' . $r['id'] . ': user ' . $r['localid'] . ' still available<br/>';
                    mysql_free_result($rsCache);
                    break;
                case 6:
                    $rsCache = mysql_query('SELECT COUNT(*) count FROM pictures WHERE id=\'' . sql_escape($r['localid']) . '\'', $dblink);
                    $rCache = mysql_fetch_array($rsCache);
                    if ($rCache['count'] != 0)
                        echo 'removed_objects ' . $r['id'] . ': picture ' . $r['localid'] . ' still available<br/>';
                    mysql_free_result($rsCache);
                    break;
                default:
                    echo 'removed_objects ' . $r['id'] . ': unbekannter type= ' . $r['type'] . '<br/>';
                    break;
            }

            // uuid
            if (!isuuid($r['uuid']))
                echo 'removed_objects ' . $r['id'] . ': uuid invalidly ' . $r['uuid'] . '<br/>';

            // removed_date
            if (($r['removed_date'] == '0000-00-00 00:00:00') || ($r['removed_date'] == null))
                echo 'removed_objects ' . $r['id'] . ': Null-Data w polu  removed_date<br/>';
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle watches_waiting</b><br/>';
        $rs = mysql_query('SELECT `user_id` FROM `watches_waiting`', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            // user_id

            if (!isUser($r['user_id']))
                echo 'watches_waiting ' . $r['cache_id'] . ': User ' . $r['user_id'] . ' nicht vorhanden<br/>';
        }
        mysql_free_result($rs);

        flush();

        echo '<br /><b>Tabelle user</b> [<a href="index.php?action=recalcuserstat">Summen neu berechnen</a>]<br/>';
        $rs = mysql_query('SELECT `user_id` id, `username`, `password`, `email`, `latitude`, `longitude`, `last_modified`, `was_loggedin`, `country`, `date_created`, `hidden_count`, `log_notes_count`, `founds_count`, `notfounds_count`, `uuid`, `cache_watches` FROM `user`', $dblink);
        while ($r = mysql_fetch_array($rs)) {
            // username
            if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\.\-_ @������=)(\/\\\&*+~#]{2,59}$/', $r['username']))
                echo 'user ' . $r['id'] . ': username ung�ltig<br/>';

            // password
            if (!preg_match('/^[a-zA-Z0-9]{32}$/', $r['password']))
                echo 'user ' . $r['id'] . ': password ung�ltig<br/>';

            // email
            if (!preg_match('/^[^\@]+\@[^\@]+\.\w{2,}$/', $r['email']))
                echo 'user ' . $r['id'] . ': email ung�ltig<br/>';

            // latitude
            if ($r['latitude'] == null)
                $r['latitude'] = 0;
            if (($r['latitude'] < -90) || ($r['latitude'] > 90))
                echo 'user ' . $r['id'] . ': latitude ung�ltig<br/>';

            // longitude
            if ($r['longitude'] == null)
                $r['longitude'] = 0;
            if (($r['longitude'] < -180) || ($r['longitude'] > 180))
                echo 'user ' . $r['id'] . ': longitude ung�ltig<br/>';

            // was_loggedin

            if (($r['was_loggedin'] != null) && ($r['was_loggedin'] != 0) && ($r['was_loggedin'] != 1))
                echo 'user ' . $r['id'] . ': was_loggedin ung�ltig<br/>';

            // country

            $rsC = mysql_query('SELECT COUNT(*) count FROM countries WHERE short=\'' . sql_escape($r['country']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($rC['count'] != 1)
                echo 'user ' . $r['id'] . ': country ' . $r['country'] . ' existiert nicht<br/>';
            mysql_free_result($rsC);

            // uuid
            if (!isuuid($r['uuid']))
                echo 'user ' . $r['id'] . ': uuid ung�ltig ' . $r['uuid'] . '<br/>';

            // last_modified
            if (($r['last_modified'] == '0000-00-00 00:00:00') || ($r['last_modified'] == null))
                echo 'user ' . $r['id'] . ': Null-Datum in Feld last_modified<br/>';

            // date_created
            if (($r['date_created'] == '0000-00-00 00:00:00') || ($r['date_created'] == null))
                echo 'user ' . $r['id'] . ': Null-Datum in Feld date_created<br/>';

            // hidden_count
            $rsC = mysql_query('SELECT COUNT(*) count FROM caches WHERE user_id=\'' . sql_escape($r['id']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($r['hidden_count'] == null)
                $r['hidden_count'] = 0;
            if ($rC['count'] != $r['hidden_count'])
                echo 'user ' . $r['id'] . ': hidden_count stimmt nicht soll=\'' . $rC['count'] . '\' ist=\'' . $r['hidden_count'] . '\'<br/>';
            mysql_free_result($rsC);

            // log_notes_count
            $rsC = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE deleted=0 AND user_id=\'' . sql_escape($r['id']) . '\' AND type=3', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($r['log_notes_count'] == null)
                $r['log_notes_count'] = 0;
            if ($rC['count'] != $r['log_notes_count'])
                echo 'user ' . $r['id'] . ': log_notes_count stimmt nicht soll=\'' . $rC['count'] . '\' ist=\'' . $r['log_notes_count'] . '\'<br/>';
            mysql_free_result($rsC);

            // founds_count
            $rsC = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE deleted=0 AND user_id=\'' . sql_escape($r['id']) . '\' AND type=1', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($r['founds_count'] == null)
                $r['founds_count'] = 0;
            if ($rC['count'] != $r['founds_count'])
                echo 'user ' . $r['id'] . ': founds_count stimmt nicht soll=\'' . $rC['count'] . '\' ist=\'' . $r['founds_count'] . '\'<br/>';
            mysql_free_result($rsC);

            // notfounds_count
            $rsC = mysql_query('SELECT COUNT(*) count FROM cache_logs WHERE deleted=0 AND user_id=\'' . sql_escape($r['id']) . '\' AND type=2', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($r['notfounds_count'] == null)
                $r['notfounds_count'] = 0;
            if ($rC['count'] != $r['notfounds_count'])
                echo 'user ' . $r['id'] . ': notfounds_count stimmt nicht soll=\'' . $rC['count'] . '\' ist=\'' . $r['notfounds_count'] . '\'<br/>';
            mysql_free_result($rsC);

            // cache_watches
            $rsC = mysql_query('SELECT COUNT(*) count FROM cache_watches WHERE user_id=\'' . sql_escape($r['id']) . '\'', $dblink);
            $rC = mysql_fetch_array($rsC);
            if ($r['cache_watches'] == null)
                $r['cache_watches'] = 0;
            if ($rC['count'] != $r['cache_watches'])
                echo 'user ' . $r['id'] . ': cache_watches stimmt nicht soll=\'' . $rC['count'] . '\' ist=\'' . $r['cache_watches'] . '\'<br/>';
            mysql_free_result($rsC);
        }
        mysql_free_result($rs);
        ?>
    </body>
</html>
<?php

function isuuid($uuid)
{
    if (preg_match('/^[a-zA-Z0-9]{8}\-[a-zA-Z0-9]{4}\-[a-zA-Z0-9]{4}\-[a-zA-Z0-9]{4}\-[a-zA-Z0-9]{12}/', $uuid))
        return true;
    else
        return false;
}

function isUser($user_id, $bAllow0 = false)
{
    global $dblink;

    if (($bAllow0 == true) && ($user_id == 0))
        return true;

    $rsUser = mysql_query('SELECT COUNT(*) count FROM `user` WHERE user_id=\'' . sql_escape($user_id) . '\'', $dblink);
    $rUser = mysql_fetch_array($rsUser);
    mysql_free_result($rsUser);

    if ($rUser['count'] != 1)
        return false;
    else
        return true;
}
?>
