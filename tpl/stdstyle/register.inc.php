<?php
/***************************************************************************
                                                  ./tpl/stdstyle/register.inc.php
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

/****************************************************************************

   Unicode Reminder ??

     set template specific language variables

 ****************************************************************************/

 $register = 'Rejestracja';
 $allcountries = ''.$language[$lang]['all_countries'].'';
 $default_country = $config['defaultCountry'];
 $no_answer = ''.$language[$lang]['not_spcified'].'';
 $register_email_subject = ''.$language[$lang]['register_email_subject'].'';

 $error_username_not_ok = '<span class="errormsg">'.$language[$lang]['error_username_not_ok'].'</span>';
 $error_username_exists = '<span class="errormsg">'.$language[$lang]['error_username_exists'].'</span>';
 $error_email_not_ok = '<span class="errormsg">'.$language[$lang]['error_email_not_ok'].'</span>';
 $error_email_exists = '<span class="errormsg">'.$language[$lang]['error_email_exists'].'</span>';
 $error_password_not_ok = '<span class="errormsg">'.$language[$lang]['error_password_not_ok'].'</span>';
 $error_password_diffs = '<span class="errormsg">'.$language[$lang]['error_password_diffs'].'</span>';
 $error_tos = '<br /><span class="errormsg">'.$language[$lang]['error_tos'].'</span>';

 ?>
