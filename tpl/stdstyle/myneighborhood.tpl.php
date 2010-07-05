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
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="" align="middle"/>&nbsp;{{caches_neighborhood}}</div>
<table border="1"  bgcolor="#DBE6F1" style="border-collapse: collapse;font-weight:bold; margin-left: 10px; line-height: 1.4em; font-size: 12px;" width="95%">
<tr>
<td width="15%" align="center" onmouseover="this.style.backgroundColor='#9CBAD6'" onmouseout="this.style.background='#DBE6F1'"><a class="links" href="mycaches.php?status=1">{{active}}</a></td>
<td width="15%" align="center" onmouseover="this.style.backgroundColor='#9CBAD6'" onmouseout="this.style.background='#DBE6F1'"><a class="links" href="mycaches.php?status=2">{{temp_unavailable}}</a></td>
<td width="15%" align="center" onmouseover="this.style.backgroundColor='#9CBAD6'" onmouseout="this.style.background='#DBE6F1'"><a class="links" href="mycaches.php?status=3">{{archived}}</a></td>
<td width="15%" align="center" onmouseover="this.style.backgroundColor='#9CBAD6'" onmouseout="this.style.background='#DBE6F1'"><a class="links" href="mycaches.php?status=5">{{not_published}}</a></td>
<td width="15%" align="center" onmouseover="this.style.backgroundColor='#9CBAD6'" onmouseout="this.style.background='#DBE6F1'"><a class="links" href="mycaches.php?status=4">{{for_approval}}</a></td>
<td width="15%" align="center" onmouseover="this.style.backgroundColor='#9CBAD6'" onmouseout="this.style.background='#DBE6F1'"><a class="links" href="mycaches.php?status=6">{{blocked}}</a></td>
</tr>
</table>
<p>&nbsp;</p>

<table border="0" cellspacing="2" cellpadding="1" style="margin-left: 10px; line-height: 1.4em; font-size: 13px;" width="95%">
<tr>
<td colspan="2"><strong>{{date_hidden_label}}</strong></td>
<td></td>
<td><strong>Geocache</strong></td>
</tr>
<tr>
<td colspan="5"><hr></hr></td>
</tr>
		{file_content}
<tr>
<td colspan="5"><hr></hr></td>
</tr>
</table>
	<p>
		{pages}
	</p>

