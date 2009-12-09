<?php
/***************************************************************************
		./tpl/stdstyle/rating.inc.php
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

	$rating_tpl =	"<tr>
				<td valign=\"top\">".tr('my_recommend').":</td>
				<td valign=\"top\">
					{rating_msg}
					<noscript><br />Rekomendacje mogą być dodawane tylko podczas robienia wpisu do logu!</noscript>
				</td>
			</tr>
			<tr><td class=\"spacer\" colspan=\"2\"></td></tr>";

	$rating_allowed = '<input type="checkbox" name="rating" id="l_rating" value="1" class="checkbox" {chk_sel}/><label for="l_rating">'.$language[$lang][want_to_recommend].'.</label>';
	$rating_maxreached = 'Alternatywnie możesz cofnąć przyznane rekomendacje <a href="mytop5.php">TUTAJ</a>.';
	$rating_too_few_founds = ''.$language[$lang][possible_recommend].': {anzahl}.';
	$rating_stat = ''.$language[$lang][number_my_recommend].': {curr} '.$language[$lang][number_possible_recommend].' {max}.';
	$rating_own = '<input type="checkbox" name="rating" id="l_rating" value="1" class="checkbox" {chk_dis}/><label for="l_rating">Nie można zarekomendować własnej skrzynki.</label>';
?>
