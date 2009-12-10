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

	$submit = 'Send entry in the log';
	$date_message = '<span class="errormsg">Date format is invalid. Correct format: DD-MM-YYYY</span>';
	$score_message = '<span class="errormsg">Please rate cache, or indicate that you do not want to rate it.</span>';

	$log_pw_field = '<tr><td colspan="2">Log Password: <input class="input100" type="text" name="log_pw" maxlength="20" value="" /> (Only where found)</td></tr>
					<tr><td class="spacer" colspan="2"></td></tr>';
	$log_pw_field_pw_not_ok = '<tr><td colspan="2">Log Password: <input type="text" name="log_pw" maxlength="20" size="20" value=""/><span class="errormsg"> Invalid password!</span></td></tr><tr><td class="spacer" colspan="2"></td></tr>';

	$listed_only_oc = "Only listed here !";

	$smiley_link = '<a href="javascript:insertSmiley(\'{smiley_text}\')">{smiley_image}</a>';

 $log_not_ok_message = '&nbsp;<span class="errormsg">Log Type Missing!</span>';

 $sel_message = 'Select';
 $log_types[] = array('id' => '-1', 'pl' => 'Prosze wybrac!', 'en' => 'Please select!','sv' => 'Please select!');

 $default_lang = 'SV';
?>