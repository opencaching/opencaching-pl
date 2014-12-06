<?php
/***************************************************************************
                                                                ./logout.php
                                                            -------------------
        begin                : Mon June 14 2004
        copyright            : (C) 2004 The OpenCaching Group
        forum contact at     : http://www.opencaching.com/phpBB2

    ***************************************************************************/

/***************************************************************************
    *
    *   This program is free software; you can redistribute it and/or modify
    *   it under the terms of the GNU General Public License as published by
    *   the Free Software Foundation; either version 2 of the License, or
    *   (at your option) any later version.
    *
    ***************************************************************************/

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

    //Preprocessing
    if ($error == false)
    {
        if (isset($_GET['token']) && isset($_SESSION['logout_cookie']) 
                && $_GET['token'] == $_SESSION['logout_cookie'])
        {
            //load language specific variables
            require_once($stylepath . '/login.inc.php');
            if (auth_logout() == true)
            {
                $_SESSION = array();
                session_destroy();
            }
        }
    }

    $target = isset($_GET['target'])?$target:"index.php";
    header('Location: '.urlencode($target));
    die()
?>
