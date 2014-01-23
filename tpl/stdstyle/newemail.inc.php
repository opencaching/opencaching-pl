<?php
/***************************************************************************
                                                  ./tpl/stdstyle/newpw.inc.php
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

 $change_email = tr('em05');
 $get_code = tr('generate_code');

 $email_changed = '<div class="notice" style="width:500px;height:24px;">'.tr('em06').'.</div>';
 $email_send = '<div class="notice" style="width:500px;height:24px;">'.tr('em07').'.</div>';
 $email_subject = tr('em08');

 $error_email_not_ok = '<div class="errormsg">'.tr('em09').'.</div>';
 $error_email_exists = '<div class="errormsg">'.tr('em10').'.</div>';
 $error_no_new_email = '<div class="errormsg">'.tr('em10').'.</div>';
 $error_wrong_code = '<div class="errormsg">'.tr('em11').'.</div>';
 $error_code_timed_out = '<div class="errormsg">'.tr('em12').'.</div>';
?>
