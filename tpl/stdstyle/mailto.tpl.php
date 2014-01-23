<?php
/***************************************************************************
                                            ./tpl/stdstyle/log_cache.tpl.php
                                                            -------------------
        begin                : July 4 2004
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

     log a cache visit

     template replacements:

        cacheid
        logtypeoptions
        logdate
        logtext
        reset
        submit

 ****************************************************************************/
?>
<form action="mailto.php" method="post" enctype="application/x-www-form-urlencoded" name="mailto_form" dir="ltr">
<input type="hidden" name="userid" value="{userid}"/>

<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/email.png" class="icon32" alt=""  align="middle" /> {{email_user}} &nbsp;<a href='viewprofile.php?userid={userid}'>{to_username}</a></div>
<table class="table">

    <tr><td colspan="2">&nbsp;</td></tr>

    {message_start}
    <tr><TD colspan="2"><b>{message}</b></TD></tr>
    {message_end}
    {formular_start}

    <tr>
        <td colspan="2">{{titles}}: <input type="text" name="subject" value="{subject}" class="input400" /> {errnosubject}
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td colspan="2">{{content}} {errnotext}</td>

    </tr>
    <tr>
        <td colspan="2">
            <textarea class="logs" name="text" cols="68" rows="15">{text}</textarea>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td colspan="2"><label for="l_send_emailaddress">{{my_email_will_send}}</label><input type="checkbox" name="send_emailaddress" value="1"{send_emailaddress_sel} id="l_send_emailaddress" class="checkbox" />
        </td>
    </tr>
    <tr>
        <td class="help" colspan="2">
            <div class="notice" style="width:500px;height:44px;">
            {{email_publish}}<br />
            </div>


        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>

    <tr>
        <td class="header-small" colspan="2">
            <input type="reset" name="reset" value="{{email_reset}}" class="formbuttons" />&nbsp;&nbsp;
            <input type="submit" name="submit" value="{{email_submit}}" class="formbuttons" />
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    {formular_end}
</table>
</form>
