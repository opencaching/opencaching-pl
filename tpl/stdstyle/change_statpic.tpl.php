<?php
/* * *************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 * ************************************************************************* */

/* * **************************************************************************

  Unicode Reminder ??

  the users profile page

  template replacement(s):

  ...statpic_text_message
  ...statpic_text
  ...available_logos
  ...reset
  ...change_data

 * ************************************************************************** */
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/stat1.png" border="0" align="middle" width="32" height="32" alt=""  />&nbsp;{{choose_statpic}}</div>
<p class="content-title-noshade-size2">{{statpic_previews}}:</p>
<div class="buffer"></div>
<table class="table">
    <form name="change" action="change_statpic.php" method="post" enctype="application/x-www-form-urlencoded"  style="display: inline;">

        <colgroup>
            <col width="200">
            <col>
        </colgroup>
        <tr>
            <td class="content-title-noshade">{{user_statpic_text}}:</td>
            <td>
                <input type="text" name="statpic_text" maxlength="30" value="{statpic_text}" class="input200"/>
                {{statpic_text_message}}
            </td>
        </tr>
        <tr><td class="buffer" colspan="2"></td></tr>
        {available_logos}
        <tr><td class="buffer" colspan="2"></td></tr>
        <tr>
            <td colspan="2">
                <input type="reset" name="reset" value="{{reset}}" class="formbuttons"/>&nbsp;&nbsp;
                <input type="submit" name="submit" value="{{change}}" class="formbuttons"/>
            </td>
        </tr>
    </form>
</table>
