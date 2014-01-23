#!/usr/bin/php -q
<?php
 /***************************************************************************
                                                    ./util.sec/geodb_serachindex/index.php
                                                            -------------------
        begin                : Sat September 24 2005
        copyright            : (C) 2005 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

 /***************************************************************************

        Ggf. muss die Location des php-Binaries angepasst werden.

        Dieses Script erstellt den Suchindex für Ortsnamen aus den Daten der
        Opengeodb.

    ***************************************************************************/

  require_once('../../lib/settings.inc.php');
  require_once('../../lib/clicompatbase.inc.php');
  require_once('../../lib/search.inc.php');

/* begin db connect */
    $bFail = false;
    $dblink = mysql_connect($dbserver, $dbusername, $dbpasswd);
    if ($dblink != false)
    {
        //database connection established ... set the used database
        if (@mysql_select_db($dbname, $dblink) == false)
        {
            $bFail = true;
            mysql_close($dblink);
            $dblink = false;
        }
    }
    else
        $bFail = true;

    if ($bFail == true)
    {
        echo 'Unable to connect to database';
        exit;
    }
/* end db connect */

/* begin search index rebuild */

    mysql_query('DELETE FROM geodb_search', $dblink);

    $rs = mysql_query('SELECT `loc_id`, `text_val` FROM `geodb_textdata` WHERE `text_type`=500100000 AND text_locale IN (\'da\', \'de\', \'en\', \'fi\', \'fr\', \'it\', \'nl\', \'rm\')', $dblink);
    while ($r = mysql_fetch_array($rs))
    {
        $simpletexts = search_text2sort($r['text_val']);
        $simpletextsarray = explode_multi($simpletexts, ' -/,');

        foreach ($simpletextsarray AS $text)
        {
            if ($text != '')
            {
                if (nonalpha($text))
                    die($text . "\n");

                $simpletext = search_text2simple($text);

                mysql_query('INSERT INTO `geodb_search` (`loc_id`, `sort`, `simple`, `simplehash`) VALUES (' . $r['loc_id'] . ', \'' . addslashes($text) . '\', \'' . addslashes($simpletext) . '\', \'' . addslashes(crc32($simpletext)) . '\')', $dblink);
            }
        }
    }
    mysql_free_result($rs);

/* end search index rebuild */

function nonalpha($str)
{
    for ($i = 0; $i < strlen($str); $i++)
        if (!((ord(substr($str, $i, 1)) >= ord('a')) && (ord(substr($str, $i, 1)) <= ord('z'))))
            return true;

    return false;
}
?>
