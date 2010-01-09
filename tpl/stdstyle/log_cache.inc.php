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

	$submit = 'Wyślij wpis do logu';
	$date_message = '<span class="errormsg">Data ma niepoprawny format. Poprawny format: DD-MM-RRRR</span>';
	$score_message = '<span class="errormsg">Proszę ocenić skrzynkę lub zaznaczyć, że nie chcesz jej ocenić.</span>';

	$log_pw_field = '<tr><td colspan="2"><img src="tpl/stdstyle/images/free_icons/key_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;Hasło do logu: <input class="input100" type="text" name="log_pw" maxlength="20" value="" /> (tylko dla znalezionych skrzynek)</td></tr>
					<tr><td class="spacer" colspan="2"></td></tr>';
	$log_pw_field_pw_not_ok = '<tr><td colspan="2"><img src="tpl/stdstyle/images/free_icons/key_go.png" class="icon16" alt="" title="" align="middle" />&nbsp;Hasło do logu: <input type="text" name="log_pw" maxlength="20" size="20" value=""/><span class="errormsg"> Nieprawidłowe hasło!</span></td></tr><tr><td class="spacer" colspan="2"></td></tr>';

	$listed_only_oc = "Only here listed !";

	$smiley_link = '<a href="javascript:insertSmiley(\'{smiley_text}\')">{smiley_image}</a>';

 $log_not_ok_message = '&nbsp;<span class="errormsg">Brak typu logu!</span>';

 $sel_message = 'Wybierz';
 $log_types[] = array('id' => '-1', 'pl' => 'Prosze wybrac!', 'en' => 'Please select!');

 $default_lang = 'PL';
?>
