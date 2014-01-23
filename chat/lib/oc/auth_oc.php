<?php
$rootpat=__DIR__;

require($rootpat.'/../../../lib/settings.inc.php');
require($rootpat.'/../../../lib/gis/gis.class.php');

    //disconnect the databse
    function db_disconnect()
    {
        global $dbpconnect, $dblink;

        //is connected and no persistent connect used?
        if (($dbpconnect == false) && ($dblink !== false))
        {
            mysql_close($dblink);
            $dblink = false;
        }
    }

    //database handling
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
            mysql_query("SET NAMES 'utf8'", $dblink);

            //database connection established ... set the used database
            if (@mysql_select_db($dbname, $dblink) == false)
            {
                //error while setting the database ... disconnect
                db_disconnect();
                $dblink = false;
            }
        }
    }

//if ((!isset($GLOBALS['no-session'])) || ($GLOBALS['no-session'] == false))
//  session_start();

//    ob_start();
    //detecting errors
    $error = false;

    if (get_magic_quotes_gpc())
    {
        if (is_array($_GET))
        {
            while (list($k, $v) = each($_GET))
            {
                if (is_array($_GET[$k]))
                {
                    while (list($k2, $v2) = each($_GET[$k]))
                    {
                        $_GET[$k][$k2] = stripslashes($v2);
                    }
                    @reset($_GET[$k]);
                }
                else
                {
                    $_GET[$k] = stripslashes($v);
                }
            }
            @reset($_GET);
        }

        if (is_array($_POST))
        {
            while (list($k, $v) = each($_POST))
            {
                if (is_array($_POST[$k]))
                {
                    while (list($k2, $v2) = each($_POST[$k]))
                    {
                        $_POST[$k][$k2] = stripslashes($v2);
                    }
                    @reset($_POST[$k]);
                }
                else
                {
                    $_POST[$k] = stripslashes($v);
                }
            }
            @reset($_POST);
        }

        if (is_array($HTTP_COOKIE_VARS))
        {
            while (list($k, $v) = each($HTTP_COOKIE_VARS))
            {
                if (is_array($HTTP_COOKIE_VARS[$k]))
                {
                    while (list($k2, $v2) = each($HTTP_COOKIE_VARS[$k]))
                    {
                        $HTTP_COOKIE_VARS[$k][$k2] = stripslashes($v2);
                    }
                    @reset($HTTP_COOKIE_VARS[$k]);
                }
                else
                {
                    $HTTP_COOKIE_VARS[$k] = stripslashes($v);
                }
            }
            @reset($HTTP_COOKIE_VARS);
        }
}
//  if (!isset($rootpath)) $rootpath = './';

    require_once('./lib/oc/functions.inc.php');

    // load HTML specific includes
    require_once('./lib/oc/cookie.class.php');


    //restore cookievars[]
    load_cookie_settings();


    //open a databse connection
    db_connect();

    if ($dblink === false)
    {
        //error while connecting to the database
        $error = true;

        //set up error report
//  echo "Nie zalogowany";
    }
    else
    {
        // include the authentication functions
        require('./lib/oc/auth.inc.php');

        //user authenification from cookie
        auth_user();
        if ($usr == false)
        {
//          echo "nologin3";
        }
        else
        {

            // check for user_id in session
            if( !isset($_SESSION['user_id']) )
            {
                $_SESSION['user_id'] = $usr['userid'];
//          echo $usr['username'];
//          echo $usr['userid'];
            }
        }
    }



    //get the stored settings and authentification data from the cookie
    function load_cookie_settings()
    {
        global $cookie, $lang, $style;

        //speach
        if ($cookie->is_set('lang'))
        {
            $lang = $cookie->get('lang');
        }

        //style
        if ($cookie->is_set('style'))
        {
            $style = $cookie->get('style');
        }
    }

    //store the cookie vars
    function write_cookie_settings()
    {
        global $cookie, $lang, $style;

        //language
        $cookie->set('lang', $lang);

        //style
        $cookie->set('style', $style);

        //send cookie
        $cookie->header();
    }

    //returns the cookie value, otherwise false
    function get_cookie_setting($name)
    {
        global $cookie;

        if ($cookie->is_set($name))
        {
            return $cookie->get($name);
        }
        else
        {
            return false;
        }
    }

    //sets the cookie value
    function set_cookie_setting($name, $value)
    {
        global $cookie;
        $cookie->set($name, $value);
    }

?>