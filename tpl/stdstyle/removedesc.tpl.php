<?php
/***************************************************************************
											./tpl/stdstyle/removedesc.tpl.php
															-------------------
		begin                : July 7 2004
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
                                   				                                
	 remove a cache description
		
		desclang_name
		cachename
		cacheid_urlencode
		desclang_urlencode
		
 ****************************************************************************/
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/trash.png" class="icon32" alt="" title="" align="middle" />&nbsp;{{Usunięcie opisu skrzynki}}</div>
<p>&nbsp;</p>
<p>Czy opis &quot;{desclang_name}&quot; dla skrzynki &quot;{cachename}&quot;
ma być usunięty?</p>
<p><a href="removedesc.php?cacheid={cacheid_urlencode}&desclang={desclang_urlencode}&commit=1">Tak, usunąć opis skrzynki</a></p>
