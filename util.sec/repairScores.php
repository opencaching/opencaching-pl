<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder ăĄă˘
 ***************************************************************************/
    $rootpath = '../';
    require_once($rootpath.'lib/clicompatbase.inc.php');


/* begin with some constants */

    $sDateformat = 'Y-m-d H:i:s';

/* end with some constants */

class ClearFakeVotes
{

    function run()
    {
        /* begin db connect */
        db_connect();
        if ($dblink === false)
        {
            echo 'Unable to connect to database';
            exit;
        }
    /* end db connect */

$result = mysql_query("SELECT cache_id FROM caches");
set_time_limit(3600);
        while($rs = mysql_fetch_array($result))
        {
            // usuniecie falszywych ocen
            //echo "cache_logs.cache_id=".sql_escape($rs['cache_id']).", user.username=".sql_escape($rs['user_id'])."<br />";
            //$sql = "DELETE FROM scores WHERE cache_id = '".sql_escape($rs['cache_id'])."' AND user_id = '".sql_escape($rs['user_id'])."'";
            //mysql_query($sql);

            // zliczenie liczby ocen po usunieciu
            $sql = "SELECT count(*) FROM scores WHERE cache_id='".sql_escape($rs['cache_id'])."'";
            $liczba = mysql_result(mysql_query($sql),0);
            $sql = "SELECT score FROM scores WHERE cache_id='".sql_escape($rs['cache_id'])."'";
            $score = mysql_query($sql);
            $suma = 0;

            // repair founds
            $founds_query = mysql_query("SELECT count(*) FROM cache_logs WHERE deleted=0 AND cache_id = ".sql_escape($rs['cache_id'])." AND (type=1 OR type=7)");
            $founds = mysql_result($founds_query,0);
            $notfounds_query = mysql_query("SELECT count(*) FROM cache_logs WHERE deleted=0 AND cache_id = ".sql_escape($rs['cache_id'])." AND (type=2 OR type=8)");
            $notfounds = mysql_result($notfounds_query,0);
            $notes_query = mysql_query("SELECT count(*) FROM cache_logs WHERE deleted=0 AND cache_id = ".sql_escape($rs['cache_id'])." AND type=3");
            $notes = mysql_result($notes_query,0);
            $watcher_query = mysql_query("SELECT count(*) FROM cache_watches WHERE cache_id = ".sql_escape($rs['cache_id']));
            $watcher = mysql_result($watcher_query,0);

            // obliczenie nowej sredniej
            while( $res = mysql_fetch_array($score))
                $suma += $res['score'];

            if( $liczba != 0)
                $srednia = $suma / $liczba;
            else $srednia = 0;

            $sql = "UPDATE caches SET votes='".sql_escape($liczba)."', score='".sql_escape($srednia)."', founds=".sql_escape(intval($founds)).", notfounds=".sql_escape(intval($notfounds)).", notes=".sql_escape(intval($notes)).", watcher=".sql_escape(intval($watcher))." WHERE cache_id='".sql_escape($rs['cache_id'])."'";
            //echo "<br />";
            mysql_query($sql);
        }

        /*$sql = "select cache_id, count(*) as watches from cache_watches group by cache_id";
        $fixwatchers_query = mysql_query($sql);
        while( $fixwatchers = mysql_fetch_array($fixwatchers_query))
        {
            echo $sql2 = "UPDATE caches SET watcher='".sql_escape($fixwatchers['watches'])."' WHERE cache_id='".sql_escape($fixwatchers['cache_id'])."'";
            mysql_query($sql2);
        }*/
        set_time_limit(60);
        db_disconnect();

    }

}

$clearFakeVotes = new ClearFakeVotes();
$clearFakeVotes->run();

?>
