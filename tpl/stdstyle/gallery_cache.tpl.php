<?php
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
                                       				                                

 ****************************************************************************/
?>
			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size1">
				<img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt=""/>
			&nbsp;{{gallery_of_cache}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a>&nbsp;&nbsp;
			&nbsp;&nbsp;
				</p>
			</div>
<div class="content2-container">
<div class="logs">
<span style="font-size:12px;font-weight:bold;float:left;">{{images_cache}}</span><br/><br/>
<center><table>
<tr><td>
		{cachepictures}

</td></tr></table></center>	
</div>
<div class="logs">
<span style="font-size:12px;font-weight:bold;">{{images_logs}}</span><br/><br/>
<table>
<tr><td>

		{logpictures}
	</td></tr></table>
</div>
</div>
<div id="viewlogs-end">[<a class="links" href="viewcache.php?cacheid={cacheid}">{{back_to_the_geocache_listing}}</a>]</div>
