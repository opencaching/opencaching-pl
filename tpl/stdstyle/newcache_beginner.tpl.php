<?php

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder ąść


 ****************************************************************************/
 global $NEED_FIND_LIMIT,$NEED_APPROVE_LIMIT;
?>

<table class="content" border="0">
	<tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="{{new_cache}}" align="middle" /><font size="4" <b>Nowa skrzynka</b></font></td></tr>
	<tr><td class="spacer"></td></tr>
</table>
<br /><br />
<div style="width: 750px;">
<p style="font-size: 14px; line-height:1.6em; text-align: justify;"><b>Aby zarejstrować nowe skrzynki musisz najpierw znaleźć <font color="red"><?php echo $NEED_FIND_LIMIT; ?></font> skrzynek sposród następujących typów: 
<font color="blue">
<ul>
<li>Tradycyjna, </li>
<li>Multicache,</li> 
<li>Quiz, </li>
<li>Mobilna, </li>
<li>Inny typ.</li>
</ul></font>
Obecnie masz znalezionych <font color="green">{number_finds_caches}</font> skrzynek spośród typów wymienionych w powyższych wymaganiach.<br/><br/>

Po spełnieniu powyższego warunku Twoje pierwsze <font color="red"><?php echo $NEED_APPROVE_LIMIT; ?></font> skrzynki bedą weryfikowane przez Zespół OC PL. Po zatwierdzeniu skrzynki otrzymasz informacje via e-mail o tym fakcie i będziesz mógł ją opublikować poprzez edycje skrzynki i zmiane jej statusu. W przypadku uwag do skrzynki Zespół OC PL będzie się z Tobą kontaktował w sprawie uzpełnienia informacji lub zmian w skrzynce.</b>
</p>
<br />
</div>

