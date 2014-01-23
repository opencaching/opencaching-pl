<?php
 /***************************************************************************
                                                    ./util/notification/run_notify.php
                                                            -------------------
        begin                : August 25 2006
        copyright            : (C) 2006 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

 /***************************************************************************

        Unicode Reminder ăĄă˘

        Ggf. muss die Location des php-Binaries angepasst werden.

        Arbeitet die Tabelle `notify_waiting` ab und verschickt
        Benachrichtigungsmails ueber neue Caches.

    ***************************************************************************/
// wlacz wyswietlanie bledow
// ini_set ('display_errors', On);

    $rootpath = '../../';
    require_once($rootpath . 'lib/common.inc.php');
    require_once($rootpath . 'util.sec/notification/settings.inc.php');
    require_once($rootpath . 'lib/consts.inc.php');

/* begin with some constants */

    $sDateformat = 'Y-m-d H:i:s';

/* end with some constants */

// Check if another instance of the script is running
    $lock_file = fopen("/tmp/notification-run_notify.lock", "w");
    if (!flock($lock_file, LOCK_EX | LOCK_NB))
    {
        // Another instance of the script is running - exit
        echo "Another instance of run_notify.php is currently running.\nExiting.\n";
        fclose($lock_file);
        exit;
    }

// No other instance - do normal processing

/* begin db connect */
    db_connect();
    if ($dblink === false)
    {
        echo 'Unable to connect to database';
        exit;
    }
/* end db connect */

  $rsNotify = sql(" SELECT  `notify_waiting`.`id`, `notify_waiting`.`cache_id`, `notify_waiting`.`type`,
                `user`.`username`,
                `user2`.`email`, `user2`.`username` as `recpname`, `user2`.`latitude` as `lat1`, `user2`.`longitude` as `lon1`, `user2`.`user_id` as `recid`,
                `caches`.`name` as `cachename`, `caches`.`date_hidden`, `caches`.`latitude` as `lat2`, `caches`.`longitude` as `lon2`, `caches`.`wp_oc`,
                `cache_type`.`pl` as `cachetype`,
                `cache_size`.`pl` as `cachesize`
            FROM `notify_waiting`, `caches`, `user`, `user` `user2`, `cache_type`, `cache_size`
            WHERE `notify_waiting`.`cache_id`=`caches`.`cache_id`
              AND `notify_waiting`.`user_id`=`user2`.`user_id`
              AND `caches`.`user_id`=`user`.`user_id`
              AND `caches`.`type`=`cache_type`.`id`
              AND `caches`.`size`=`cache_size`.`id`");

  while($rNotify = sql_fetch_array($rsNotify))
  {
        if (process_new_cache($rNotify) == 0)
            sql("DELETE FROM `notify_waiting` WHERE `id` ='&1'", $rNotify['id']);
  }
  mysql_free_result($rsNotify);

/* end send out everything that has to be sent */

// Release lock
    fclose($lock_file);

function process_new_cache($notify)
{
    global $notify_text, $mailfrom, $mailsubject, $debug, $debug_mailto, $rootpath;

//  echo "process_new_cache(".$notify['id'].")\n";
    $fehler = false;

    // mail-template lesen
    switch($notify['type'])
    {
        case notify_new_cache: // Type: new cache
            $mailbody = read_file($rootpath . 'util.sec/notification/notify_newcache.email');
            break;
        default:
            $fehler = true;
            break;
    }

    if(!$fehler)
    {
        $mailbody = mb_ereg_replace('{username}', $notify['recpname'], $mailbody);
        $mailbody = mb_ereg_replace('{date}', date('d.m.Y', strtotime($notify['date_hidden'])), $mailbody);
        $mailbody = mb_ereg_replace('{cacheid}', $notify['cache_id'], $mailbody);
        $mailbody = mb_ereg_replace('{wp_oc}', $notify['wp_oc'], $mailbody);
        $mailbody = mb_ereg_replace('{user}', $notify['username'], $mailbody);
        $mailbody = mb_ereg_replace('{cachename}', $notify['cachename'], $mailbody);
        $mailbody = mb_ereg_replace('{distance}', round(calcDistance($notify['lat1'], $notify['lon1'], $notify['lat2'], $notify['lon2'], 1), 1), $mailbody);
        $mailbody = mb_ereg_replace('{unit}', 'km', $mailbody);
        $mailbody = mb_ereg_replace('{bearing}', Bearing2Text(calcBearing($notify['lat1'], $notify['lon1'], $notify['lat2'], $notify['lon2'])), $mailbody);
        $mailbody = mb_ereg_replace('{cachetype}', $notify['cachetype'], $mailbody);
        $mailbody = mb_ereg_replace('{cachesize}', $notify['cachesize'], $mailbody);

        $subject = mb_ereg_replace('{cachename}', $notify['cachename'], $mailsubject);

        /* begin send out everything that has to be sent */
        $email_headers = "Content-Type: text/plain; charset=utf-8\r\n";
        $email_headers .= 'From: "' . $mailfrom . '" <' . $mailfrom . '>';

        // mail versenden
        if ($debug == true)
            $mailadr = $debug_mailto;
        else
            $mailadr = $notify['email'];

        mb_send_mail($mailadr, $subject, $mailbody, $email_headers);
    }
    else
    {
        echo "Unbekannter Notification-Typ: " . $notify['type'] . "<br />";
    }

    // logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)
    logentry('notify_newcache', 5, $notify['recid'], $notify['cache_id'], 0, 'Sending mail to ' . $mailadr, array());

    return 0;
}
?>
