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
	<tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/cache.png" class="icon32" alt="" title="{{new_cache}}" align="middle" /><font size="4" <b>Rejestracja nowej skrzynki - informacja</b></font></td></tr>
	<tr><td class="spacer"></td></tr>
</table>
<br />
<div class="searchdiv" style="background-color: #FFF9E3;">
<p style="margin: 10px;font-size: 12.5px; line-height:1.6em; text-align: justify;"><b>Aby zarejestrować nowe skrzynki musisz najpierw znaleźć <font color="red"><?php echo $NEED_FIND_LIMIT; ?></font> skrzynek spośród następujących typów: 
<font color="blue">
<ul>
<li><img src="tpl/stdstyle/images/cache/traditional-i.png" alt="cache"> Tradycyjna, </li>
<li><img src="tpl/stdstyle/images/cache/multi-i.png" alt="cache"> Multicache,</li> 
<li><img src="tpl/stdstyle/images/cache/quiz-i.png" alt="cache"> Quiz, </li>
<li><img src="tpl/stdstyle/images/cache/moving-i.png" alt="cache"> Mobilna, </li>
<li><img src="tpl/stdstyle/images/cache/unknown-i.png" alt="cache"> Nietypowa.</li>
</ul></font>
Obecnie Twoja liczba skrzynek znalezionych spośród wymienionych typów to: <font color="green">{number_finds_caches}</font><br/><br/>

Po spełnieniu powyższego warunku Twoje pierwsze <font color="red"><?php echo $NEED_APPROVE_LIMIT; ?></font> skrzynki bedą weryfikowane przez Zespół OC PL. Po zatwierdzeniu skrzynki otrzymasz informacje via e-mail o tym fakcie i będziesz mógł ją opublikować poprzez edycje skrzynki i zmiane jej statusu. W przypadku uwag do skrzynki Zespół OC PL będzie się z Tobą kontaktował w sprawie uzpełnienia informacji lub zmian w skrzynce.</b>
</p>
<br />
</div>

