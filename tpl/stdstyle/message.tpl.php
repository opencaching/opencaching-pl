<?php
/* * *************************************************************************
  ./tpl/stdstyle/message.tpl.php
  -------------------
  begin                : Mon June 14 2004
  copyright            : (C) 2004 The OpenCaching Group
  forum contact at     : http://www.opencaching.com/phpBB2

 * ************************************************************************* */

/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder メモ

  display the user a simple message

 * ************************************************************************** */
?>
<table class="content">
    <colgroup>
        <col width="150" />
        <col />
    </colgroup>
    <tr><td class="content2-pagetitle" colspan="2"><b>{messagetitle}</b></td></tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    {message_start}<tr><td colspan="2" class="message">{message}</td></tr><tr><td class="spacer" colspan="2"></td></tr>{message_end}
</table>
