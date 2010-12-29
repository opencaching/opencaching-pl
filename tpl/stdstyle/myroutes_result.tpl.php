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

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/route.png" class="icon32" alt="" />&nbsp;{{caches_along_route}}: {route_name}</div>
<div class="searchdiv">
<form action="myroutes_search.php" method="post" enctype="multipart/form-data" name="myroute_form" dir="ltr">
<input type="hidden" name="routeid" value="{routeid}"/>
<input type="hidden" name="distance" value="{distance}"/>
<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">
<tr>
<td colspan="2"><strong>{{date_hidden_label}}</strong></td>
<td><strong>Geocache</strong></td>
<td><strong>{{owner}}</strong>&nbsp;&nbsp;&nbsp;</td>
<td colspan="3"><strong>{{latest_logs}}</strong></td>
</tr>
<tr>
<td colspan="7"><hr></hr></td>
</tr>
		{file_content}
<tr>
<td colspan="7"><hr></hr></td>
</tr>
</table>
</div>
<br/>
			<button type="submit" name="back" value="back" style="font-size:12px;width:160px"><b>{{back}}</b></button>&nbsp;&nbsp;
			<button type="submit" name="submit_gpx" value="submit_gpx" style="font-size:12px;width:160px"><b>{{save_gpx}}</b></button>
			<br/><br/><br/>

