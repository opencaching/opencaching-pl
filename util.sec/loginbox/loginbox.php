<?php
 /***************************************************************************
                                                    ./util/loginbox/loginbox.php
                                                            -------------------
        begin                : Sat Oktoboer 8 2005
        copyright            : (C) 2005 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

 /***************************************************************************

        echo the html-code of the loginbox.
        used for cms.opencaching.de

        Restricted access via .htaccess!

    ***************************************************************************/

    // Bogus z Polska, 2014-12-06 - is this script used at all? I don't think so.

    global $dblink, $language, $lang;

    require('../../lib/settings.inc.php');
    require_once('../../lib/language.inc.php');

    require('settings.inc.php');

    $userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : '';
    $loginid = isset($_REQUEST['sessionid']) ? $_REQUEST['sessionid'] : ''; // MD5 encoded

    db_connect();
    if ($dblink === false)
    {
        echo 'DB error';
        exit;
    }

    $rs = mysql_query('SELECT user_id, username, login_id FROM `user` WHERE user_id=\'' . addslashes($userid) . '\'', $dblink);
    if (mysql_num_rows($rs) == 0)
    {
        echo $loginbox_form;
    }
    else
    {
        $r = mysql_fetch_array($rs);

        $pm = new PasswordManager($userid);
        if ($pm->verify($loginid))
            echo str_replace('{username}', htmlspecialchars($r['username']), $loginbox_loggedin);
        else
            echo $loginbox_form;
    }
    mysql_free_result($rs);

    function db_connect()
    {
        global $dblink, $dbpconnect, $dbusername, $dbname, $dbserver, $dbpasswd, $dbpconnect;

        //connect to the database by the given method - no php error reporting!
        if ($dbpconnect == true)
        {
            $dblink = @mysql_pconnect($dbserver, $dbusername, $dbpasswd);
        }
        else
        {
            $dblink = @mysql_connect($dbserver, $dbusername, $dbpasswd);
        }

        if ($dblink != false)
        {
            //database connection established ... set the used database
            if (@mysql_select_db($dbname, $dblink) == false)
            {
                //error while setting the database ... disconnect
                db_disconnect();
                $dblink = false;
            }
        }
    }
?>
