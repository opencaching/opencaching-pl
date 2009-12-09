<?php
/***************************************************************************
												  ./tpl/stdstyle/mywatches_properties.inc.php
															-------------------
		begin                : July 17 2004
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
                                     				                                
	 set template specific variables
	
 ****************************************************************************/
 
	$commit = '<div class="notice">Ustawienia zostały z powodzeniem zapisane.</div>';
	$commiterr = '<div class="warning">Błąd podczas zapisu!</div>';
	
	$weekday[1] = 'Poniedziałek';
	$weekday[2] = 'Wtorek';
	$weekday[3] = 'Środa';
	$weekday[4] = 'Czwartek';
	$weekday[5] = 'Piątek';
	$weekday[6] = 'Sobota';
	$weekday[7] = 'Niedziela';
	
	$intervalls[0] = 'Natychmiast';      // table indices are misplaced accordingly to
	$intervalls[1] = 'Raz dziennie';     // ones used in runwatch.php script that performs the real check
	$intervalls[2] = 'Raz na tygdzień';  // there: immediately=1, daily=0, and weekly=2
                                             // thus mywatches.php required a change
?>
