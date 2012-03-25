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
			<? if (isset($_GET['reklama']) || ((date('c') > '2012-03-27') && date('c') < '2012-04-03')) { ?>
				<div style='border: 1px solid #9c9; background: #dfd; margin: 10px 20px 10px 0; padding: 10px 15px; font-family: Tahoma, Verdana, Arial; font-size: 13px; color: #333'>
					<b>Programiści!</b> Jak wiecie, kod OC.PL jest otwarty. Dodatkowo, właśnie udostępniliśmy
					publicznie naszą deweloperską <b>maszynę wirtualną</b>. Teraz możesz uruchomić swój własny
					serwer OC w 10 minut i zobaczyć go "od środka".
					- <a href='http://code.google.com/p/opencaching-pl/'>więcej informacji</a>
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
				  <p class="content-title-noshade-size3"><img src="tpl/stdstyle/images/blue/crypt.png" class="icon32" alt="" title="Event" align="middle" />&nbsp;Najnowsze wpisy w blogach</p>

				
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
	
