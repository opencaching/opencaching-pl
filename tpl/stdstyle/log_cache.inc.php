<?php
/***************************************************************************
											./tpl/stdstyle/log_cache.inc.php
															-------------------
		begin                : July 4 2004
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

	 template replacements:

 ****************************************************************************/

	$submit = tr('lxg05');
    $coord_empty_message = '<img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt=""  />&nbsp;&nbsp;<span class="errormsg">'.tr('empty_coordinatest').'</span>';
    $logtext_empty_message = '<img src="tpl/stdstyle/images/misc/32x32-impressum.png" class="icon32" alt=""  />&nbsp;&nbsp;<span class="errormsg">'.tr('empty_log_text').'</span>';
	$date_message = '<span class="errormsg">'.tr('lxg01').'</span>';
	$score_message = '<span class="errormsg">'.tr('lxg02').'</span>';
	$log_pw_field = '<tr><td colspan="2"><img src="tpl/stdstyle/images/free_icons/key_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;<b>'.tr('password_to_log').': <input class="input100" type="text" name="log_pw" maxlength="20" value="" /> ('.tr('only_for_found_it').')</b></td></tr><tr><td class="spacer" colspan="2"></td></tr>';
	$log_pw_field_pw_not_ok = '<tr><td colspan="2"><img src="tpl/stdstyle/images/free_icons/key_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;<b>'.tr('password_to_log').': <input type="text" name="log_pw" maxlength="20" size="20" value=""/><span class="errormsg"> '.tr('incorrect_password_to_log').'!</span></b></td></tr><tr><td class="spacer" colspan="2"></td></tr>';
	$listed_only_oc = tr('lxg04');
	$smiley_link = '<a href="javascript:insertSmiley(\'{smiley_text}\')">{smiley_image}</a>';
	$log_not_ok_message = '&nbsp;<span class="errormsg">'.tr('no_logtype_choosen').'</span>';
	$sel_message = tr('lxg06');
 	$log_types = tr('lxg03');
 	$error_coords_not_ok = tr('error_coords_not_ok');

//  $log_types[] = array('id' => '-1', 'pl' => 'Prosze wybrac!', 'en' => 'Please select!');

?>
