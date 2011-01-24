<?php

/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*
	***************************************************************************/
//	 starting page
?>
<script language="javascript" type="text/javascript">
<!-- hide script from old browsers

var map_image_cache;

//detect browser:
if ((navigator.appName == "Netscape" && parseInt(navigator.appVersion) >= 3) || parseInt(navigator.appVersion) >= 4) {
        rollOvers = 1;
}       else {
        if (navigator.appName == "Microsoft Internet Explorer" && parseInt(navigator.appVersion) >= 4) {
                rollOvers = 1;
        } else {
                rollOvers = 0;
        }
}

window.onload = function() {
	//preload images
	if (rollOvers) {
		map_image_cache = [];
		map_image_cache[-1] = new Image();
		map_image_cache[-1].src = document.getElementById('main-cachemap').getAttribute('basesrc');
		for (i = 0; i < 20; i++)
		{
			var nc_elem = document.getElementById('newcache' + i);
			if (nc_elem != null)
			{
				map_image_cache[i] = new Image();
				map_image_cache[i].src = nc_elem.getAttribute('maphref');
			}
		}
	}
}

//image swapping function:
function Lite(nn) {
	if (rollOvers) {
		document.getElementById('main-cachemap').src = map_image_cache[nn].src;
	}
}

function Unlite() {
	if (rollOvers) {
		document.getElementById('main-cachemap').src = map_image_cache[-1].src;
	}
}

//end hiding -->
</script> 
  	  
			<!-- Page title -->		
		  <div class="content2-pagetitle">{{what_do_you_find}}</div>
			<div class="content-txtbox-noshade line-box">
				<p style="line-height: 1.6em;">{{what_do_you_find_intro}}<br/><br/></p>
				<p class="main-totalstats">{{total_of_caches}} <span class="content-title-noshade">{total_hiddens}</span> {{active_caches}} <span class="content-title-noshade">{hiddens}</span> | {{number_of_founds}}: <span class="content-title-noshade">{founds}</span> | {{number_of_active_users}}: <span class="content-title-noshade">{users} </span></p>
			</div>
<!-- Text container -->
			<div class="content2-container line-box">
				<div class="content2-container-2col-left" id="new-caches-area">
					<p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="Cache" align="middle" />&nbsp;{{newest_caches}}</p>
					<div class="content-txtbox-noshade">
						<?php
							global $dynstylepath;
							include ($dynstylepath . "start_newcaches.inc.php");
						?>
					</div>
				</div>
				<div class="content2-container-2col-right" id="main-cachemap-block">
					<div class="img-shadow">
						<?php
							global $dynstylepath;
							include ($dynstylepath . "main_cachemap.inc.php");
						?>
					</div>
				</div>
				<div class="content2-container-2col-left" id="new-events-area">
				  <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/event.png" class="icon32" alt="" title="Event" align="middle" />&nbsp;{{incomming_events}}</p>

				
				  <?php
			global $dynstylepath;
			include ($dynstylepath . "nextevents.inc.php");
		?>
			<br/></div>
		</div>
<br/>
{display_news}
<!-- End Text Container -->
	
