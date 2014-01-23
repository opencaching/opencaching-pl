<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder Ă„Ă‚Ă„Ă„Ă‚Ă‹
 ***************************************************************************/
    $rootpath = '../';
    require_once($rootpath.'lib/clicompatbase.inc.php');


/* begin with some constants */

    $sDateformat = 'Y-m-d H:i:s';

/* end with some constants */

//checkJob(new geokrety());

class ClearFakeVotes
{

    //var $name = 'geokrety';
    //var $interval = 900;

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

    $sql = "SELECT * FROM scores";
    $scores_query = mysql_query($sql);
    while( $scores = mysql_fetch_array($scores_query) )
    {
        $sql2 = "SELECT * FROM cache_logs WHERE `deleted`=0 AND user_id = ".sql_escape($scores['user_id'])." AND cache_id = ".sql_escape($scores['cache_id'])." AND (`type`=1 OR `type`=7)";
        $logs_query = mysql_query($sql2);
        if( mysql_num_rows($logs_query)==0)
        {
            $sql3 = "SELECT username FROM user WHERE user_id = ".$scores['user_id'];
            $fakeUser = mysql_result(mysql_query($sql3),0);
            //echo "uzytkownik: ".$scores['user_id']."=".$fakeUser." ocenil skrzynke: ".$scores['cache_id']." na ".$scores['score'];
            //echo "<br />";
            $sql_del = "DELETE FROM scores WHERE user_id = ".sql_escape($scores['user_id'])." AND cache_id = ".sql_escape($scores['cache_id']);
            mysql_query($sql_del);

            $sql4 = "SELECT count(*) FROM scores WHERE cache_id='".sql_escape($scores['cache_id'])."'";
            $liczba = mysql_result(mysql_query($sql4),0);

            $sql4 = "SELECT score FROM scores WHERE cache_id='".sql_escape($scores['cache_id'])."'";
            $score = mysql_query($sql4);
            $suma = 0;

            // obliczenie nowej sredniej
            while( $res = mysql_fetch_array($score))
                $suma += $res['score'];

            if( $liczba != 0)
                $srednia = $suma / $liczba;
            else $srednia = 0;

            $sql4 = "UPDATE caches SET votes='".sql_escape($liczba)."', score='".sql_escape($srednia)."' WHERE cache_id='".sql_escape($scores['cache_id'])."'";
            mysql_query($sql4);
        }
    }

        db_disconnect();
    }
}

$clearFakeVotes = new ClearFakeVotes();
$clearFakeVotes->run();

?>
