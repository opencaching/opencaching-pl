<?php
/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*   
	*  UTF-8 ąść
	***************************************************************************/
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
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="" align="middle"/>&nbsp;{{caches_in_neighborhood}} </div>
<!-- Text container -->
			<div class="content2-container line-box">
				<div class="content2-container-2col-left" id="new-caches-area">
					<p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="Cache" align="middle" />&nbsp;{{newest_caches}}</p>
					<div class="content-txtbox-noshade">
					{new_caches}
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
			</div>
		</div>
