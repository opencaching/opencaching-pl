<?php
/***************************************************************************
											./tpl/stdstyle/viewprofile.tpl.php
															-------------------
		begin                : August 21 2004
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

   Unicode Reminder ??

	 view another players profile

 ****************************************************************************/
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/home.png" class="icon32" alt="{{profile_data}}" title="{{profile_data}}" align="middle" />&nbsp;{{user_managment}}: {username}</div>

<div class="content-title-noshade">
<p><span class="content-title-noshade txt-blue08" >{{country_label}}:</span><strong> &nbsp;&nbsp;{country}</strong></p>
<p><span class="content-title-noshade txt-blue08" >{{registered_since_label}}:</span><strong>&nbsp;&nbsp; {registered}</strong></p>
<p><span class="content-title-noshade txt-blue08" >{{description_user}}:</span> <strong>&nbsp;&nbsp;{description}</strong></p>
<p><span class="content-title-noshade txt-blue08" >{{email_address}}:</span> &nbsp;&nbsp;{email}&nbsp;&nbsp;<img src="tpl/stdstyle/images/blue/email.png" class="icon32" alt="Email" title="Email" align="middle"/>&nbsp;<a href="mailto.php?userid={userid}">{{email_user}}</a></p>
<p><span class="content-title-noshade txt-blue08" >{{activation_code}}:</span> <strong>&nbsp;&nbsp;{activation_codes}</strong></p>
<p><span class="content-title-noshade txt-blue08" >{is_active_flags}</span><p>
<p><span class="content-title-noshade txt-blue08" >{hide_flag}</span><p>
<p><span class="content-title-noshade txt-blue08" >{stat_ban}</span></p>
<p><span class="content-title-noshade txt-blue08" >{remove_all_logs}</span></p>

</div>