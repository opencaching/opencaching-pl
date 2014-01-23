<?php
session_start();
/***************************************************************************
                                                                ./activation.php
                                                            -------------------
        begin                : October 14 2005
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

/****************************************************************************

   Unicode Reminder ăĄă˘

     accept rules

 ****************************************************************************/
    $_SESSION['called_from_confirm'] = 1;

    //prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

    //Preprocessing
    if ($error == false)
    {
        // check if user has already confirmed
        $rs = sql("SELECT `rules_confirmed` FROM `user` WHERE `user_id` = '".sql_escape(intval($usr['userid']))."'");
        if( $r = sql_fetch_array($rs))
        {
            if( $r['rules_confirmed'] == 0 && (strtotime("2008-11-01 00:00:00") <= strtotime(date("Y-m-d h:i:s"))))
            {
                // acceptance neccessary!

                //set here the template to process
                $tplname = 'confirm';

                $accepted = isset($_REQUEST['accepted']) ? 1 : 0;
                tpl_set_var('message_start', '');
                tpl_set_var('message', '<b>Aby korzystać z serwisu, należy zapoznać się z regulaminem i zaakceptować go.</b>');
                tpl_set_var('message_end', '');

                if (isset($_REQUEST['submit']))
                {
                    if ($accepted)
                    {
                        sql("UPDATE `user` SET `rules_confirmed` = 1 WHERE `user_id` = '".sql_escape(intval($usr['userid']))."'");
                        header("Location: index.php");
                    }
                }
                //make the template and send it out
                tpl_BuildTemplate();
            }
            else
                header("Location: index.php");
        }
    }
?>
