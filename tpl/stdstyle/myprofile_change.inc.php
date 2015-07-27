<?php
/***************************************************************************
									./tpl/stdstyle/myprofile_change.inc.php
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

	 edit profile

 ****************************************************************************/

 $change_data = $language[$lang]['change'];
 $allcountries = $language[$lang]['show_all'];
 $no_answer = $language[$lang]['no_choice'];

 $error_username_not_ok = '<span class="errormsg">'.$language[$lang]['username_incorrect'].'</span>';
 $error_username_exists = '<span class="errormsg">'.$language[$lang]['username_exists'].'</span>';
 $error_coords_not_ok = '<span class="errormsg">'.$language[$lang]['bad_coordinates'].'</span>';
 $error_radius_not_ok = '<span class="errormsg">'.$language[$lang]['bad_radius'].'</span>';
?>