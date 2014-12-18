<?php

/* * *************************************************************************
  ./tpl/stdstyle/remindemail.inc.php
  -------------------
  begin                : Wed September 21, 2005
  copyright            : (C) 2005 The OpenCaching Group
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

  set template specific language variables

 * ************************************************************************** */

$mail_subject = tr('ForgottenEmail_04');

$wrong_username = '<span class="errormsg"> ' . tr('ForgottenEmail_05') . '.</span>';
$mail_send = '<br/><p style="margin-top:0px;margin-left:0px;width:550px;background-color:#e5e5e5;border:1px solid black;text-align:left;padding:3px 8px 3px 8px;"> ' . tr('ForgottenEmail_06') . '.</p>';
?>
