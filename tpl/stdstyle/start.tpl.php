<?php
/***************************************************************************
											./tpl/stdstyle/start.tpl.php
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
//	 starting page
?>
<script language="javascript" type="text/javascript">
<!-- hide script from old browsers

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

//preload images:
/*if (rollOvers) {
map = new Image(302,284);
map.src = "/images/mini-mapa/mapa-new.jpg";
c0 = new Image(302,284);
c0.src = "/images/mini-mapa/0.jpg";
c1 = new Image(302,284);
c1.src = "/images/mini-mapa/1.jpg";
c2 = new Image(302,284);
c2.src = "/images/mini-mapa/2.jpg";
c3 = new Image(302,284);
c3.src = "/images/mini-mapa/3.jpg";
c4 = new Image(302,284);
c4.src = "/images/mini-mapa/4.jpg";
c5 = new Image(302,284);
c5.src = "/images/mini-mapa/5.jpg";
c6 = new Image(302,284);
c6.src = "/images/mini-mapa/6.jpg";
c7 = new Image(302,284);
c7.src = "/images/mini-mapa/7.jpg";
c8 = new Image(302,284);
c8.src = "/images/mini-mapa/8.jpg";
c9 = new Image(302,284);
c9.src = "/images/mini-mapa/9.jpg";
}*/
//preload images:
if (rollOvers) {
map = new Image(302,284);
map.src = "tmp/mapa.png";
c0 = new Image(302,284);
c0.src = "tmp/0.png";
c1 = new Image(302,284);
c1.src = "tmp/1.png";
c2 = new Image(302,284);
c2.src = "tmp/2.png";
c3 = new Image(302,284);
c3.src = "tmp/3.png";
c4 = new Image(302,284);
c4.src = "tmp/4.png";
c5 = new Image(302,284);
c5.src = "tmp/5.png";
c6 = new Image(302,284);
c6.src = "tmp/6.png";
c7 = new Image(302,284);
c7.src = "tmp/7.png";
c8 = new Image(302,284);
c8.src = "tmp/8.png";
c9 = new Image(302,284);
c9.src = "tmp/9.png";
}

//image swapping function:
function Lite(img) {
if (rollOvers) {
document.roll.src = eval(img + ".src");
return true;
}}
//end hiding -->
</script> 
  	  
			<!-- Page title -->		
		  <div class="content2-pagetitle">{{what_do_you_find}}</div>
			<div class="content-txtbox-noshade line-box">
				<p>{{what_do_you_find_intro}}</p>
				<div class="buffer" style="width:500px;"></div>
				<b><p>{{total_of_active_caches}}: <span class="content-title-noshade">{hiddens}</span> | {{number_of_founds}}: <span class="content-title-noshade">{founds}</span> | {{number_of_active_users}}: <span class="content-title-noshade">{users} </span></p></b>
			</div>
<!-- Text container -->
			<div class="content2-container line-box">
				<div class="content2-container-2col-left" id="new-caches-area">
					<p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/cache.png" border="0" width="32" height="32" alt="Cachesuche" title="Cachesuche" align="middle" />&nbsp;{{newest_caches}}</p>
					<div class="content-txtbox-noshade">
						<?php
							global $dynstylepath;
							$dynstylepath = "tpl/stdstyle/etc/";
							include ($dynstylepath . "start_newcaches.inc.php");
						?>
					</div>
				</div>
				<div class="content2-container-2col-right" id="startpage-map"><div class="img-shadow">
					<img src="tmp/mapa.png" id="stickermap" name="roll" alt="Karte" /></div>
				</div>
			</div>
<!-- End Text Container -->

<!-- Text container -->
			<div class="content2-container line-box">
				<div class="content2-container-2col-left" id="new-events-area">
				  <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/event.png" border="0" width="32" height="32" alt="Cachesuche" title="Cachesuche" align="middle" />&nbsp;{{incomming_events}}</p>
		<?php
			global $dynstylepath;
			include ($dynstylepath . "nextevents.inc.php");
		?>
			</div>
		</div>
	
