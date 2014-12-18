<?php
/* * *************************************************************************
  ./tpl/stdstyle/event_attendance.tpl.php
  -------------------
  begin                : June 24 2004
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

  show who attends an events

  template replacements:

 * ************************************************************************** */
?>

<p>

    {nocacheid_start}
    <img src="tpl/stdstyle/images/blue/meeting.png" alt=""/>
<div style="margin-top:4px;">
    <font size="2" color="#000080">{{event_attendance_01}} <b>{cachename}</b></font><br />
    <font size="2" color="#000080">{{event_attendance_02}} <b> {event_date}</b></font><br />
    <font size="2" color="#000080">{{event_attendance_03}} <b>{owner}</b></font>
</div>
{nocacheid_end}
<div style="margin-top:16px;">
    <font size="2" color="#000080"><b>{{event_attendance_04}}</b>&nbsp;<br/>({{event_attendance_05}} {att_count})</font><br/>
    {attendants}
</div>
</p>
