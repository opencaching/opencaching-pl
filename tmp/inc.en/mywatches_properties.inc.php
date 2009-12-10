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
 
	$commit = '<div class="notice">Settings have been saved successfully.</div>';
	$commiterr = '<div class="warning">Record error!</div>';
	
	$weekday[1] = 'Monday';
	$weekday[2] = 'Tuesday';
	$weekday[3] = 'Wednesday';
	$weekday[4] = 'Thursday';
	$weekday[5] = 'Friday';
	$weekday[6] = 'Saturday';
	$weekday[7] = 'Sunday';
	
	$intervalls[0] = 'Immediately';      // table indices are misplaced accordingly to
	$intervalls[1] = 'Once a day';     // ones used in runwatch.php script that performs the real check
	$intervalls[2] = 'Once a week';  // there: immediately=1, daily=0, and weekly=2
                                             // thus mywatches.php required a change
?>