<?php

/* * *************************************************************************
  ./tpl/stdstyle/mailto.inc.php
  -------------------
  begin                : Oct 21 2005
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

  Unicode Reminder メモ

 * ************************************************************************** */

$message_user_not_found = tr('message_user_not_found');
$message_sent = tr('message_sent');

$errnosubject = '<span class="errormsg">' . tr('mailto_01') . '</span>';
$errnotext = '<span class="errormsg">' . tr('mailto_02') . '</span>';

$mailsubject = "[opencaching] " . tr('mailto_03') . " '{from_username}': {subject}";

$mailtext_email = tr('mailto_04') . " {to_username},\n\n";
$mailtext_email .= "'{from_username}' " . tr('mailto_05') . " {from_email} " . tr('mailto_06') . " $absolute_server_URI\n";
$mailtext_email .= tr('mailto_07') . $absolute_server_URI . "viewprofile.php?userid={from_userid}\n";
$mailtext_email .= tr('mailto_08') . "\n";
$mailtext_email .= "----------------------\n\n";
$mailtext_email .= "{{text}}\n";

$mailtext_anonymous = tr('mailto_04') . " {to_username},\n\n";
$mailtext_anonymous .= "'{from_username}' " . tr('mailto_06') . " $absolute_server_URI\n";
$mailtext_anonymous .= tr('mailto_07') . $absolute_server_URI . "viewprofile.php?userid={from_userid}\n";
$mailtext_anonymous .= tr('mailto_09') . "\n";
$mailtext_anonymous .= "----------------------\n";
$mailtext_anonymous .= "{{text}}\n";
?>
