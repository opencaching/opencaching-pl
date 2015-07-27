<?php
/***************************************************************************
											./tpl/stdstyle/editcache.inc.php
															-------------------
		begin                : Mon July 6 2004
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

	 language vars

 ****************************************************************************/

	$submit = 'Wyślij';
	$remove = 'Usuń';
	$edit = 'Edycja';
$error_general = '<div class="warning">'.$language[$lang]['error_new_cache'].'</div>';
 $error_coords_not_ok = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['bad_coordinates'].'</span>';
 $time_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['time_incorrect'].'</span>';
 $way_length_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['distance_incorrect'].'</span>';
 $date_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['date_incorrect'].'</span>';
 $name_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['no_cache_name'].'</span>';
 $tos_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['new_cache_no_terms'].'</span>';
 $desc_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;<span class="errormsg">'.$language[$lang]['html_incorrect'].'</span>';
 $type_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;&nbsp;<span class="errormsg">'.$language[$lang]['type_incorrect'].'</span>';
 $size_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;&nbsp;<span class="errormsg">'.$language[$lang]['size_incorrect'].'</span>';
 $diff_not_ok_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;&nbsp;<span class="errormsg">'.$language[$lang]['diff_incorrect'].'</span>';
 $sizemismatch_message = '<br/><img src="tpl/stdstyle/images/misc/32x32-impressum.png" border="0" width="32" height="32" alt="" align="middle">&nbsp;&nbsp;<span class="errormsg">'.$language[$lang]['virtual_cache_size'].'</span>';
	$all_countries_submit = '<input type="submit" name="show_all_countries_submit" value="Wszystkie kraje"/>';

	$status_message = '&nbsp;<span class="errormsg">Status skrzynki nie pasuje do opcji publikacji.</span>';
	$nopictures = '<tr><td colspan="2"><div class="notice">Nie ma załączonych obrazków.</div></td></tr>';
	$pictureline = '<tr><td colspan="2"><a href="{link}">{title}</a> [<a href="editpic.php?uuid={uuid}">'.$edit.'</a>] [<a href="removepic.php?uuid={uuid}">'.$remove.'</a>]</td></tr>';
	$picturelines = '{lines}<tr><td colspan="2">&nbsp;</td></tr>';

	$cache_attrib_js = "new Array({id}, {selected}, '{img_undef}', '{img_large}')";
	$cache_attrib_pic = '<img id="attr{attrib_id}" src="{attrib_pic}" border="0" alt="{attrib_text}" title="{attrib_text}" onmousedown="toggleAttr({attrib_id})" />&nbsp;';

	$default_lang = 'PL';

	 $activation_form = '
		<tr><td class="spacer" colspan="2"></td></tr>
		<tr>
			<td>Publikacja skrzynki:</td>
			<td>
				<input type="radio" class="radio" name="publish" id="publish_now" value="now" {publish_now_checked}>&nbsp;<label for="publish_now">Publikuj teraz</label><br />
				<input type="radio" class="radio" name="publish" id="publish_later" value="later" {publish_later_checked}>&nbsp;<label for="publish_later">Opublikuj dnia:</label>
				<input class="input20" type="text" name="activate_day" maxlength="2" value="{activate_day}"/>.
				<input class="input20" type="text" name="activate_month" maxlength="2" value="{activate_month}"/>.
				<input class="input40" type="text" name="activate_year" maxlength="4" value="{activate_year}"/>&nbsp;
				<select name="activate_hour" class="input40">
					{activation_hours}
				</select>&nbsp;godzina&nbsp;{activate_on_message}<br />
				<input type="radio" class="radio" name="publish" id="publish_notnow" value="notnow" {publish_notnow_checked}>&nbsp;<label for="publish_notnow">Jeszcze nie publikuj</label>
			</td>
		</tr>
		';
?>