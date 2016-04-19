<?php

if (!isset($rootpath))
    $rootpath = './';

// yepp, we will use UTF-8
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
mb_language('uni');




function setCacheDefaultDescLang($cacheid)
{
    $rs = sql("SELECT `desc_languages` FROM `caches` WHERE `cache_id`='&1'", $cacheid);
    $r = sql_fetch_array($rs);
    mysql_free_result($rs);

    if (mb_strpos($r['desc_languages'], 'PL') !== false)
        $desclang = 'PL';
    else if (mb_strpos($r['desc_languages'], 'EN') !== false)
        $desclang = 'EN';
    else
    if ($r['desc_languages'] == '')
        $desclang = '';
    else
        $desclang = mb_substr($r['desc_languages'], 0, 2);

    sql("UPDATE `caches` SET `default_desclang`='&1', `last_modified`=NOW() WHERE cache_id='&2' LIMIT 1", $desclang, $cacheid);
}

function setLastFound($cacheid)
{
    $rs = sql("SELECT MAX(`date`) `date` FROM `cache_logs` WHERE `cache_id`=&1 AND `type`=&2 AND `deleted`=&3", $cacheid, 1, 0);
    $r = sql_fetch_array($rs);
    mysql_free_result($rs);

    if ($r['date'] == null)
        sql("UPDATE `caches` SET `last_found`=null WHERE `cache_id`=&1", $cacheid);
    else
        sql("UPDATE `caches` SET `last_found`='&1' WHERE `cache_id`=&2", $r['date'], $cacheid);
}

// update last_modified=NOW() for every object depending on that cacheid
function touchCache($cacheid)
{
    sql("UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`='&1'", $cacheid);
    sql("UPDATE `caches`, `cache_logs` SET `cache_logs`.`last_modified`=NOW() WHERE `caches`.`cache_id`=`cache_logs`.`cache_id` AND `caches`.`cache_id`='&1' AND `cache_logs`.`deleted`=&2", $cacheid, 0);
    sql("UPDATE `caches`, `cache_desc` SET `cache_desc`.`last_modified`=NOW() WHERE `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`cache_id`='&1'", $cacheid);
    sql("UPDATE `caches`, `pictures` SET `pictures`.`last_modified`=NOW() WHERE `caches`.`cache_id`=`pictures`.`object_id` AND `pictures`.`object_type`=2 AND `caches`.`cache_id`='&1'", $cacheid);
    sql("UPDATE `caches`, `cache_logs`, `pictures` SET `pictures`.`last_modified`=NOW() WHERE `caches`.`cache_id`=`cache_logs`.`cache_id` AND `cache_logs`.`id`=`pictures`.`object_id` AND `pictures`.`object_type`=1 AND `caches`.`cache_id`='&1' AND `cache_logs`.`deleted`=&2", $cacheid, 0);
    sql("UPDATE `caches`, `mp3` SET `mp3`.`last_modified`=NOW() WHERE `caches`.`cache_id`=`mp3`.`object_id` AND `mp3`.`object_type`=2 AND `caches`.`cache_id`='&1'", $cacheid);
    sql("UPDATE `caches`, `cache_logs`, `mp3` SET `mp3`.`last_modified`=NOW() WHERE `caches`.`cache_id`=`cache_logs`.`cache_id` AND `cache_logs`.`id`=`mp3`.`object_id` AND `mp3`.`object_type`=1 AND `caches`.`cache_id`='&1' AND `cache_logs`.`deleted`=&2", $cacheid, 0);
}

// read a file and return it as a string
// WARNING: no huge files!
function read_file($file = '')
{
    $fh = fopen($file, 'r');
    if ($fh) {
        $content = fread($fh, filesize($file));
    }

    fclose($fh);

    return $content;
}

// explode with more than one separator
function explode_multi($str, $sep)
{
    $ret = array();
    $nCurPos = 0;

    while ($nCurPos < mb_strlen($str)) {
        $nNextSep = mb_strlen($str);
        for ($nSepPos = 0; $nSepPos < mb_strlen($sep); $nSepPos++) {
            $nThisPos = mb_strpos($str, mb_substr($sep, $nSepPos, 1), $nCurPos);
            if ($nThisPos !== false)
                if ($nNextSep > $nThisPos)
                    $nNextSep = $nThisPos;
        }

        $ret[] = mb_substr($str, $nCurPos, $nNextSep - $nCurPos);

        $nCurPos = $nNextSep + 1;
    }

    return $ret;
}

// called if mysql_query faild, sends email to sysadmin
function sql_failed($sql)
{
    sql_error();
}

function sqlValue($sql, $default)
{
    $rs = sql($sql);
    if ($r = sql_fetch_row($rs)) {
        if ($r[0] == null)
            return $default;
        else
            return $r[0];
    } else
        return $default;
}

function getSysConfig($name, $default)
{
    return sqlValue('SELECT `value` FROM `sysconfig` WHERE `name`=\'' . sql_escape($name) . '\'', $default);
}

function setSysConfig($name, $value)
{
    if (sqlValue('SELECT COUNT(*) FROM sysconfig WHERE name=\'' . sql_escape($name) . '\'', 0) == 1)
        sql("UPDATE `sysconfig` SET `value`='&1' WHERE `name`='&2' LIMIT 1", $value, $name);
    else
        sql("INSERT INTO `sysconfig` (`name`, `value`) VALUES ('&1', '&2')", $name, $value);
}

function sql($sql)
{
    global $rootpath;
    global $sql_debug, $sql_warntime;
    global $sql_replacements;
    global $dblink, $sqlcommands;

    $args = func_get_args();
    unset($args[0]);

    $sqlpos = 0;
    $filtered_sql = '';

    // $sql von vorne bis hinten durchlaufen und alle &x ersetzen
    $nextarg = mb_strpos($sql, '&');
    while ($nextarg !== false) {
        // muss dieses & ersetzt werden, oder ist es escaped?
        $escapesCount = 0;
        while ((($nextarg - $escapesCount - 1) > 0) && (mb_substr($sql, $nextarg - $escapesCount - 1, 1) == '\\'))
            $escapesCount++;
        if (($escapesCount % 2) == 1)
            $nextarg++;
        else {
            $nextchar = mb_substr($sql, $nextarg + 1, 1);
            if (is_numeric($nextchar)) {
                $arglength = 0;
                $arg = '';

                // nächstes Zeichen das keine Zahl ist herausfinden
                while (mb_ereg_match('^[0-9]{1}', $nextchar) == 1) {
                    $arg .= $nextchar;

                    $arglength++;
                    $nextchar = mb_substr($sql, $nextarg + $arglength + 1, 1);
                }

                // ok ... ersetzen
                $filtered_sql .= mb_substr($sql, $sqlpos, $nextarg - $sqlpos);
                $sqlpos = $nextarg + $arglength;

                if (isset($args[$arg])) {
                    if (is_numeric($args[$arg]))
                        $filtered_sql .= $args[$arg];
                    else {
                        if ((mb_substr($sql, $sqlpos - $arglength - 1, 1) == '\'') && (mb_substr($sql, $sqlpos + 1, 1) == '\''))
                            $filtered_sql .= sql_escape($args[$arg]);
                        else if ((mb_substr($sql, $sqlpos - $arglength - 1, 1) == '`') && (mb_substr($sql, $sqlpos + 1, 1) == '`'))
                            $filtered_sql .= sql_escape($args[$arg]);
                        else
                            sql_error();
                    }
                }
                else {
                    // NULL
                    if ((mb_substr($sql, $sqlpos - $arglength - 1, 1) == '\'') && (mb_substr($sql, $sqlpos + 1, 1) == '\'')) {
                        // Anführungszeichen weg machen und NULL einsetzen
                        $filtered_sql = mb_substr($filtered_sql, 0, mb_strlen($filtered_sql) - 1);
                        $filtered_sql .= 'NULL';
                        $sqlpos++;
                    } else
                        $filtered_sql .= 'NULL';
                }

                $sqlpos++;
            }
            else {
                $arglength = 0;
                $arg = '';

                // nächstes Zeichen das kein Buchstabe/Zahl ist herausfinden
                while (mb_ereg_match('^[a-zA-Z0-9]{1}', $nextchar) == 1) {
                    $arg .= $nextchar;

                    $arglength++;
                    $nextchar = mb_substr($sql, $nextarg + $arglength + 1, 1);
                }

                // ok ... ersetzen
                $filtered_sql .= mb_substr($sql, $sqlpos, $nextarg - $sqlpos);

                if (isset($sql_replacements[$arg])) {
                    $filtered_sql .= $sql_replacements[$arg];
                } else
                    sql_error();

                $sqlpos = $nextarg + $arglength + 1;
            }
        }

        $nextarg = mb_strpos($sql, '&', $nextarg + 1);
    }

    // rest anhängen
    $filtered_sql .= mb_substr($sql, $sqlpos);

    // \& durch & ersetzen
    $nextarg = mb_strpos($filtered_sql, '\&');
    while ($nextarg !== false) {
        $escapesCount = 0;
        while ((($nextarg - $escapesCount - 1) > 0) && (mb_substr($filtered_sql, $nextarg - $escapesCount - 1, 1) == '\\'))
            $escapesCount++;
        if (($escapesCount % 2) == 0) {
            // \& ersetzen durch &
            $filtered_sql = mb_substr($filtered_sql, 0, $nextarg) . '&' . mb_substr($filtered_sql, $nextarg + 2);
            $nextarg--;
        }

        $nextarg = mb_strpos($filtered_sql, '\&', $nextarg + 2);
    }

    //
    // ok ... hier ist filtered_sql fertig
    //

        /* todo:
      - errorlogging
      - LIMIT
      - DROP/DELETE ggf. blocken
     */

    if (isset($sql_debug) && ($sql_debug == true)) {
        require_once($rootpath . 'lib/sqldebugger.inc.php');
        $result = sqldbg_execute($filtered_sql);
        if ($result === false)
            sql_error();
    }
    else {
        // Zeitmessung für die Ausführung
        require_once($rootpath . 'lib/bench.inc.php');
        $cSqlExecution = new Cbench;
        $cSqlExecution->start();

        $result = mysql_query($filtered_sql, $dblink);
        if ($result === false)
            sql_error();

        $cSqlExecution->stop();

        if ($cSqlExecution->diff() > $sql_warntime)
            sql_warn('execution took ' . $cSqlExecution->diff() . ' seconds');
    }

    return $result;
}

function sql_escape($value)
{
    global $dblink;
    $value = mysql_real_escape_string($value, $dblink);
    $value = mb_ereg_replace('&', '\&', $value);
    return $value;
}

function sql_error()
{
    if (class_exists('\okapi\Okapi')) {
        throw new Exception("SQL Error " . mysql_errno() . ": " . mysql_error());
    }
    global $sql_errormail;

    global $absolute_server_URI;
    global $dberrormsg;

    // sendout email
    $email_content = mysql_errno() . ": " . mysql_error();
    $email_content .= "\n--------------------\n";
    $email_content .= print_r(debug_backtrace(), true);
    echo $sql_errormail . ' sql_error: ' . $absolute_server_URI . " " . $email_content;

        tpl_errorMsg('sql_error', $dberrormsg);

    die('sql_error');
}

function sql_warn($warnmessage)
{
    global $sql_errormail;

    global $absolute_server_URI;

    $email_content = $warnmessage;
    $email_content .= "\n--------------------\n";
    $email_content .= print_r(debug_backtrace(), true);


}

/*
  Ersatz für die in Mysql eingebauten Funktionen
 */

function sql_fetch_array($rs)
{
    return mysql_fetch_array($rs);
}

function sql_fetch_assoc($rs)
{
    return mysql_fetch_assoc($rs);
}

function sql_fetch_row($rs)
{
    return mysql_fetch_row($rs);
}

function sql_free_result($rs)
{
    return mysql_free_result($rs);
}

function mb_trim($str)
{
    $bLoop = true;
    while ($bLoop == true) {
        $sPos = mb_substr($str, 0, 1);

        if ($sPos == ' ' || $sPos == "\r" || $sPos == "\n" || $sPos == "\t" || $sPos == "\x0B" || $sPos == "\0")
            $str = mb_substr($str, 1, mb_strlen($str) - 1);
        else
            $bLoop = false;
    }

    $bLoop = true;
    while ($bLoop == true) {
        $sPos = mb_substr($str, -1, 1);

        if ($sPos == ' ' || $sPos == "\r" || $sPos == "\n" || $sPos == "\t" || $sPos == "\x0B" || $sPos == "\0")
            $str = mb_substr($str, 0, mb_strlen($str) - 1);
        else
            $bLoop = false;
    }

    return $str;
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

//database handling
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
        mysql_query("SET NAMES 'utf8'", $dblink);

        //database connection established ... set the used database
        if (@mysql_select_db($dbname, $dblink) == false) {
            //error while setting the database ... disconnect
            db_disconnect();
            $dblink = false;
        }
    }
}

?>
