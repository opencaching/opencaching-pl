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

//image swapping function:
function Lite(nn) {
	if (rollOvers) {
		document.getElementById('smallmark'+nn).style.visibility = 'hidden';
		document.getElementById('bigmark'+nn).style.visibility = 'visible';
	}
}

function Unlite(nn) {
	if (rollOvers) {
		document.getElementById('bigmark'+nn).style.visibility = 'hidden';
		document.getElementById('smallmark'+nn).style.visibility = 'visible';
	}
}

//end hiding -->
</script> 
  	  
			<!-- Page title -->		
			<div class="content2-pagetitle">{{what_do_you_find}}</div>
			
			<? if (($_SERVER['HTTP_HOST'] == 'opencaching.pl') && (date('c') < '2013-03-07')) { ?>
				<style>
					#wrinfo { margin: 10px 20px 10px 0; padding: 10px 15px; }
					#wrinfo p { font-family: Tahoma, Verdana, Arial; font-size: 14px; margin: 12px 0; }
					#wrinfo p.podpis { text-align: right; }
					#wrinfo.green { border: 2px solid #494; background: #afa; }
					#wrinfo.green p { color: #020; }
					#wrinfo.green p.podpis { color: #6a6; }
				</style>
				<div id='wrinfo' class='green'>
					
					<p>Problem z hasłami najprawdopodobniej został rozwiązany. :)
					Więcej informacji <a href='http://forum.opencaching.pl/viewtopic.php?f=30&t=7449'>na forum</a>.</p>
					
					<p class='podpis'>- Rada Techniczna Opencaching.pl</p>
					
				</div>
			<? } ?>
			
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
					<div class="img-shadow" style="position: relative">
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
							<div class="content2-container-2col-left" id="new-events-area">
				  <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="" title="Event" align="middle" />&nbsp;{{latest_blog}}</p>

				
				  <?php
			global $dynstylepath;
			include ($dynstylepath . "start_newblogs.inc.php");
		?>
			</div>
		</div>
<br/>
{display_news}
<br/>
<!-- End Text Container -->
	
