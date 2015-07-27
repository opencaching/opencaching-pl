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
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/profile/32x32-home.png" border="0" width="32" height="32" alt="{profile_data}" title="{profile_data}" align="middle">&nbsp;{username} {email} - {user_profile}</div>
<div class="content-title-noshade">
<p>
	<img src="/tpl/stdstyle/images/profile/32x32-email.png" width="32" height="32" border="0" alt="Email" title="Email" align="middle"/>&nbsp;<a href="mailto.php?userid={userid}">{email_user}</a><br/>
		<img src="/tpl/stdstyle/images/misc/32x32-world.png" width="32" height="32" border="0" alt="Mapa" title="Map" align="middle"/>&nbsp;<a href="cachemap2.php?userid={userid}">{show_user_map}</a>
	{hide_flag}<br/>
	{stat_ban}<br/>
	{remove_all_logs}<br/>
</p>
</div>
<table class="table" border="0">
	<colgroup>
		<col width="260">
		<col>
	</colgroup>
	{opis_start}
	<tr>
		<td colspan="2">
		<p class="content-title-noshade-size2"><img src="tpl/stdstyle/images/profile/32x32-profile.png" width="32" height="32" align="middle" border="0" alt="{user_desc}" title="Logs"/>&nbsp;{user_desc}</p></td>
	</tr>
	<tr>
		<td class="content-title-noshade" colspan="2" valign="top">{description}</td>
	</tr>
	{opis_end}
	<tr>
		<td colspan="2">
		<p class="content-title-noshade-size2"><img src="tpl/stdstyle/images/profile/32x32-profile.png" width="32" height="32" align="middle" border="0" alt="{profile_data}" title="Logs">&nbsp;{profile_data}</p></td>
	</tr>
	<tr>
		<td class="content-title-noshade">{country_label}:</td>
		<td class="content-title-noshade">{country}</td>
	</tr>
	<tr>
		<td class="content-title-noshade">{registered_since_label}:</td>
		<td class="content-title-noshade">{registered}</td>
	</tr>
	<tr>
		<td class="content-title-noshade">{statpic_label}:</td>
		<td class="content-title-noshade"><img src="statpics/{userid}.jpg" align="middle"></td>
	</tr>
	<tr><td class="buffer"></td></tr>
	<tr>
		<td>
		<p class="content-title-noshade-size2"><img src="tpl/stdstyle/images/cache/24x24-traditional.png" width="24" height="24" align="middle" border="0" alt="Logs" title="Logs"/>&nbsp;{hidden_caches}:</p></td>
		<td>
		<p class="content-title-noshade-size2">{hidden}
			<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;ownerid={userid}&amp;searchbyowner=&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0" style="color: rgb(102, 102, 102); font-size: 12px;">{show_all}</a>
		</p>
		</td>
	</tr>
	{type_hidden}
	<tr><td class="buffer"></td></tr>
	<tr>
		<td><p class="content-title-noshade-size2">
		<img src="tpl/stdstyle/images/log/16x16-found.png" width="16" height="16" align="middle" border="0" alt="{found_caches}" title="Znalezione"/>&nbsp;{found_caches}:</p></td>
		<td>
		<p class="content-title-noshade-size2">{founds}
			<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={userid}&amp;searchbyfinder=&logtype=1&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0" style="color: rgb(102, 102, 102); font-size: 12px;">{show_all}</a>
			</p>
		</td>
	</tr>
	{type_found}
	<tr><td class="buffer"></td></tr>
	<tr>
		<td>
		<p class="content-title-noshade-size2">
		<img src="tpl/stdstyle/images/log/16x16-dnf.png" width="16" height="16" align="middle" border="0" alt="{not_found_caches}" title="Nienalezione"/>&nbsp;{not_found_caches}:
		</p>
		</td>
		<td>
		<p class="content-title-noshade-size2">
			{not_founds}
			<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={userid}&amp;searchbyfinder=&logtype=2&amp;f_ignored=0&amp;f_userfound=0&amp;f_userowner=0" style="color: rgb(102, 102, 102); font-size: 12px;">{show_all}</a>
		</p>
		</td>
	</tr>
	{type_notfound}
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td>
		<p class="content-title-noshade-size2">
		<img src="tpl/stdstyle/images/profile/32x32-statistic2.png" width="32" height="32" align="middle" border="0" alt="{my_recommendations}" title="{my_recommendations}"/>&nbsp;{my_recommendations}:</p></td>
		<td><p class="content-title-noshade-size2">{recommended} {out_of} {maxrecommended} <a href="usertops.php?userid={userid}" style="color: rgb(102, 102, 102); font-size: 12px;">{show_all}</a></p>
		</td>
	</tr>
	<tr><td class="buffer" colspan="2"></td></tr>
	<tr>
		<td style="vertical-align:top" colspan="2">
		<p class="content-title-noshade-size2">
			<img src="tpl/stdstyle/images/description/22x22-logs.png" width="22" height="22" align="middle" border="0" alt="{my_recommendations}" title="{my_recommendations}">&nbsp;{user_new_log_entries}:
		</p>
		</td>
	</tr>
			{lastlogs}
	<tr><td class="buffer" colspan="2"></td></tr>
	<!--<tr><td  colspan="2"></td></tr>
	<tr><td  colspan="2">{statistics}</td></tr>
	<tr><td  colspan="2"></td></tr>
	<tr>
		<td  style="vertical-align:top">
			{days_since_first_find_label}:
		</td>
		<td>
			{days_since_first_find}
		</td>
	</tr>
	<tr>
		<td  style="vertical-align:top">
			{days_went_caching_label}:
		</td>
		<td>
			{days_went_caching}
		</td>
	</tr>
	<tr>
		<td  style="vertical-align:top">
			{days_no_caching_label}:
		</td>
		<td>
			{days_no_caching}
		</td>
	</tr>
	<tr>
		<td  style="vertical-align:top">
			{obsession_indicator_label}:
		</td>
		<td>
			{obsession_indicator}
		</td>
	</tr>
	<tr>
		<td  style="vertical-align:top">
			{hide_to_find_label}:
		</td>
		<td>
			{hide_to_find}
		</td>
	</tr>
	<tr>
		<td  style="vertical-align:top">
			{caching_karma_label}:
		</td>
		<td>
			{caching_karma}
		</td>
	</tr>
	<tr>
		<td  style="vertical-align:top">
			{verbosity_label}:
		</td>
		<td>
			{verbosity}
		</td>
	</tr>
	<tr>
		<td  style="vertical-align:top">
			{total_dist_attempted_caches_label}:
		</td>
		<td>
			{total_dist_attempted_caches}
		</td>
	</tr>
	<tr>
		<td  style="vertical-align:top">
			{median_dist_attempted_caches_label}:
		</td>
		<td>
			{median_dist_attempted_caches}
		</td>
	</tr>
	<tr>
		<td  style="vertical-align:top">
			{average_dist_attempted_caches_label}:
		</td>
		<td>
			{average_dist_attempted_caches}
		</td>
	</tr>
	<tr>
		<td  style="vertical-align:top">
			{total_dist_hidden_caches_label}:
		</td>
		<td>
			{total_dist_hidden_caches}
		</td>
	</tr>
	<tr>
		<td  style="vertical-align:top">
			{median_dist_hidden_caches_label}:
		</td>
		<td>
			{median_dist_hidden_caches}
		</td>
	</tr>
	<tr>
		<td  style="vertical-align:top">
			{average_dist_hidden_caches_label}:
		</td>
		<td>
			{average_dist_hidden_caches}
		</td>
	</tr>-->
</table>
