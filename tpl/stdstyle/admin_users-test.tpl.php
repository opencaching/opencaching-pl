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
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/aprove-cache.png" class="icon32" alt="{{profile_data}}" title="{{profile_data}}" align="middle" />&nbsp;{{management_users}}: {username}</div>

<div class="content-title-noshade box-blue">
<form name="change" action="admin_users.php?action=change" method="post" enctype="application/x-www-form-urlencoded"  style="display: inline;">
<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<span class="content-title-noshade txt-blue08" >{{user_ident}}:</span><strong> &nbsp;&nbsp;<input type="text" name="username" maxlength="60" value="{username}" class="input250"/></strong>&nbsp;&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/arrow.png" alt="" /> (<a href="viewprofile.php?userid={userid}">{{user_profile}}</a>)</p>
<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<span class="content-title-noshade txt-blue08" >{{registered_since_label}}:</span><strong>&nbsp;&nbsp; {registered}</strong></p>
<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<span class="content-title-noshade txt-blue08" >{{email_address}}:</span> &nbsp;&nbsp;<input type="text" name="email" maxlength="100" value="{email}" class="input250"/>&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/email.png" width="22" height="22" alt="Email" title="Email" align="middle"/>&nbsp;<a href="mailto.php?userid={userid}">{{email_user}}</a></p>
<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<span class="content-title-noshade txt-blue08" >{{activation_code}}:</span> <strong>&nbsp;&nbsp;{activation_codes}</strong></p>
<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<span class="content-title-noshade txt-blue08" >{{country_label}}:</span><strong> &nbsp;&nbsp;{country}</strong></p>
<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<span class="content-title-noshade txt-blue08" >{{descriptions}}:</span> <strong>&nbsp;&nbsp;{description}</strong></p>
<input type="submit" name="submit" value="{{change}}" style="width:120px"/>
</form>
<br />
<hr></hr>
<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<span class="content-title-noshade txt-blue08" >{is_active_flags}</span></p>
<p><img src="tpl/stdstyle/images/blue/arrow2.png" alt="" align="middle" />&nbsp;&nbsp;<span class="content-title-noshade txt-blue08" >{stat_ban}</span></p>
{hide_flag}
{remove_all_logs}
</div>