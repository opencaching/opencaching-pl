<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder ăĄă˘
 ***************************************************************************/
$rootpath = '../';
require_once($rootpath.'lib/clicompatbase.inc.php');
require_once($rootpath.'lib/common.inc.php');


/* begin with some constants */

    $sDateformat = 'Y-m-d H:i:s';

/* end with some constants */

$STEP = array("START" => 0, "AFTER_FIRST_MAIL_SENT" => 1,"AFTER_SECOND_MAIL_SENT" => 2, "ARCH_COMPLETE" => 3);
class AutoArch
{

    function sendEmail($step, $cacheid)
    {
        global $STEP, $stylepath, $octeam_email, $site_name;
        $sql = "SELECT caches.cache_id, caches.name, caches.wp_oc, user.email FROM caches, user WHERE caches.cache_id = ".sql_escape(intval($cacheid))." AND user.user_id = caches.user_id";
        $query = mysql_query($sql);
        $cache = mysql_fetch_array($query);

        switch($step)
        {
            case $STEP["START"]:
                $email_content = read_file($stylepath . '/email/arch1.email');
            break;
            case $STEP["AFTER_FIRST_MAIL_SENT"]:
                $email_content = read_file($stylepath . '/email/arch2.email');
            break;
            case $STEP["AFTER_SECOND_MAIL_SENT"]:
                $email_content = read_file($stylepath . '/email/arch3.email');
            break;
        }
        $email_content = mb_ereg_replace('%cachename%', $cache['name'], $email_content);
        $email_content = mb_ereg_replace('%cache_wp%', $cache['wp_oc'], $email_content);
        $email_content = mb_ereg_replace('%cacheid%', $cacheid, $email_content);

        $emailheaders = "Content-Type: text/plain; charset=utf-8\r\n";
        $emailheaders .= "From: $site_name <$octeam_email>\r\n";
        $emailheaders .= "Reply-To: $site_name <$octeam_email>";

        //echo "email:".$cache['email']."-->".$emailheaders."<br />".$email_content."<br />";
        mb_send_mail($cache['email'], "[OCPL] Automatyczna archiwizacja Twojej skrzynki", $email_content, $emailheaders);

    }

    function run()
    {
        global $STEP, $dblink;
        /* begin db connect */
        db_connect();
        if ($dblink === false)
        {
            echo 'Unable to connect to database';
            exit;
        }

    /* end db connect */
            $sql = "SELECT cache_arch.step, caches.cache_id, caches.name, user.username FROM `cache_arch`, caches, user WHERE (step=1 OR step=2 OR step=3) AND (caches.cache_id = cache_arch.cache_id) AND (caches.user_id = user.user_id) ORDER BY step ASC";
            $query = mysql_query($sql);
            while( $linia = mysql_fetch_array($query))
            {
                switch( $linia['step'] )
                {
                    case 1:
//                      echo "Wysłano pierwsze powiadomienie (6 miesięcy).";
                    break;
                    case 2:
//                      echo "Wysłano drugie powiadomienie (8 miesięcy).";
                    break;
                    case 3:
//                      echo "Autoarchiwizacja zakończona.";
                    break;
                }
//              echo " cache: <a href='http://www.opencaching.pl/viewcache.php?cacheid=".$linia['cache_id']."'>".$linia['name']."</a> użytkownik: ".$linia['username']."<br />";
            }
        // anulowanie procedury archiwizacji, jeśli opis skrzynki został zmodyfikowany w ciągu 6 miesięcy
        $sql = "SELECT caches.cache_id FROM caches, cache_arch WHERE cache_arch.cache_id = caches.cache_id AND last_modified >= now() - interval 4 month";
        $result = mysql_query($sql);
        while($rs = mysql_fetch_array($result))
        {
            $del_sql = "DELETE FROM cache_arch WHERE cache_id = ".intval($rs['cache_id']);
            //echo "<br />";
            @mysql_query($del_sql);
        }

        $sql = "SELECT cache_id, last_modified FROM caches WHERE status = 2 AND last_modified < now() - interval 4 month";
        $result = mysql_query($sql);
        while($rs = mysql_fetch_array($result))
        {
            // sprawdz w ktorym miejscu procedury znajduje sie skrzynka
            $step_sql = "SELECT step FROM cache_arch WHERE cache_id = ".intval($rs['cache_id']);
            $step_query = mysql_query($step_sql);
            if( mysql_num_rows($step_query) > 0 )
            {
                $step_array = @mysql_fetch_array($step_query);
                $step = $step_array['step'];
                //echo "cache ". $rs['cache_id']." jest na stepie ".$step."<br />";
            }
            else $step = $STEP["START"];
            if( strtotime($rs['last_modified']) < time() - 6*31*24*60*60 && $step < $STEP["ARCH_COMPLETE"])
            {
                $this->sendEmail($STEP["AFTER_SECOND_MAIL_SENT"], $rs['cache_id']);
                // wlasnie mija 6 miesiecy od ostatniej modyfikacji - czas zarchiwizować skrzynkę
                $status_sql = "REPLACE INTO cache_arch (cache_id, step) VALUES (".intval($rs['cache_id']).", ".($STEP["ARCH_COMPLETE"]).")";
                @mysql_query($status_sql);
                $arch_sql = "UPDATE caches SET status = 3 WHERE cache_id=".intval($rs['cache_id']);
                @mysql_query($arch_sql);
                $log_uuid = create_uuid();
                $log_sql = "INSERT INTO cache_logs (cache_id, uuid, user_id, type, date, last_modified, date_created, text, owner_notified, node) VALUES (".sql_escape(intval($rs['cache_id'])).", '".sql_escape($log_uuid)."', '-1', 9,NOW(),NOW(), NOW(), 'Automatyczna archiwizacja - 6 miesięcy w stanie \"Tymczasowo niedostępna\"', 1, 2)";
                @mysql_query($log_sql);
            }
            else if( strtotime($rs['last_modified']) < time() - 5*31*24*60*60 && $step < $STEP["AFTER_SECOND_MAIL_SENT"])
            {
                $this->sendEmail($STEP["AFTER_FIRST_MAIL_SENT"], $rs['cache_id']);
                // wlasnie mija 5 miesiecy od ostatniej modyfikacji - czas wyslac drugie powiadomienie
                $status_sql = "REPLACE INTO cache_arch (cache_id, step) VALUES (".sql_escape(intval($rs['cache_id'])).", ".sql_escape(($STEP["AFTER_SECOND_MAIL_SENT"])).")";
                @mysql_query($status_sql);
            }
            else if( strtotime($rs['last_modified']) < time() - 4*31*24*60*60 && $step < $STEP["AFTER_FIRST_MAIL_SENT"])
            {
                $this->sendEmail($STEP["START"], $rs['cache_id']);
                // wlasnie mija 4 miesiecy od ostatniej modyfikacji - czas wyslac pierwsze powiadomienie
                $status_sql = "REPLACE INTO cache_arch (cache_id, step) VALUES (".sql_escape(intval($rs['cache_id'])).", ".sql_escape(($STEP["AFTER_FIRST_MAIL_SENT"])).")";
                @mysql_query($status_sql);
            }
        }
        db_disconnect();
    }

    function ArchEvent()
    {
        global $dblink;
        /* begin db connect */
        db_connect();
        if ($dblink === false)
        {
            echo 'Unable to connect to database';
            exit;
        }
        /* end db connect */

        // archiwizuj wszystkie wydarzenia, które odbyły się dawniej niż 2 miesiące temu
        $sql = "UPDATE caches SET status = 3 WHERE status<>3 AND type = 6 AND date_hidden < now() - interval 2 month";
        @mysql_query($sql);


        db_disconnect();
    }

}


$autoArch = new AutoArch();
$autoArch->run();
$autoArch->ArchEvent();
?>
