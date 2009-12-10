<?php
	/***************************************************************************
												/lib/expressions.inc.php
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

		language specific expressions

	****************************************************************************/

	//only debugging
 	$runtime = 'Runtime: {time} sec';

	// set Date/Time language
	setlocale(LC_TIME, 'en_GB.UTF-8');

	//common vars
	$datetimeformat = '%d %B %Y at %H:%M:%S';
	$dateformat = '%d %B %Y';
	$reset = 'Clear';
	$yes = 'Yes';
	$no = 'No';

	//common errors
	$error_pagenotexist = 'This page does not exist!';
?>
