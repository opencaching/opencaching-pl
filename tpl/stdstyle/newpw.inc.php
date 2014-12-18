<?php

/* * *************************************************************************
  ./tpl/stdstyle/newpw.inc.php
  -------------------
  begin                : Mon June 14 2004
  copyright            : (C) 2004 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder ??

  set template specific language variables

 * ************************************************************************** */

$getcode = tr('generate_code');
$changepw = tr('chpw_01');
$emailnotexist = '<div class="errormsg">' . tr('error_email_not_ok') . '</div>';
$newpw_subject = tr('chpw_06');
$emailsend = '<div class="notice" style="width:500px;height:44px;"><b>' . tr('chpw_02') . '</b></div>';
$pw_not_ok = '<div class="errormsg">' . tr('error_password_not_ok') . '</div>';
$pw_no_match = '<div class="errormsg">' . tr('error_password_diffs') . '</div>';
$pw_changed = '<div class="notice" style="width:500px;height:44px;">' . tr('chpw_04') . '</div>';
$code_timed_out = '<div class="errormsg">' . tr('em12') . '</div>';
$code_not_ok = '<div class="errormsg">' . tr('chpw_05') . '</div>';
?>
