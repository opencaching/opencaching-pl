<?php
/***************************************************************************
											./tpl/stdstyle/editlog.inc.php
															-------------------
		begin                : Mon July 5 2004
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

   Unicode Reminder メモ

	 language vars

 ****************************************************************************/

	$submit = 'Submit';

 	$error_wrong_node = "This entry in the log has been provided by another Opencaching server and you can edit it only on that server.";

	$date_message = '<span class="errormsg">Date format is invalid. Correct format: DD-MM-YYYY</span>';
	$smiley_link = '<a href="javascript:insertSmiley(\'{smiley_text}\')">{smiley_image}</a>';

	$log_pw_field = '<tr><td colspan="2">Log Password: <input class="input100" type="text" name="log_pw" maxlength="20" value="" /> (only where required)</td></tr>
					<tr><td class="spacer" colspan="2"></td></tr>';
	$log_pw_field_pw_not_ok = '<tr><td colspan="2">Log Password: <input type="text" name="log_pw" maxlength="20" size="20" value=""/><span class="errormsg"> Incorrect Password!</span></td></tr><tr><td class="spacer" colspan="2"></td></tr>';
?>