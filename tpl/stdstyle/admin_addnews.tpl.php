<?php
/* * *************************************************************************
  Unicode Reminder メモ

 * ************************************************************************* */
?>
<form action="admin_addnews.php" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="submit" value="1" />

    <table class="content">
        <tr>
            <td class="content2-pagetitle">
                <img src="tpl/stdstyle/images/blue/write.png" class="icon32" alt=""  /><font size="4">  <b>{{add_news}}</b></font>
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>

        <tr><td class="spacer" colspan="2"></td></tr>
        <tr>
            <td>
                <img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Hinweis" title="Hinweis" align="middle" />
                <span style="color:#666666; font-size:10px;">
                </span>
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>
        <tr>
            <td>
                <b>{{subject}}</b>:
                <select name="topic">
                    {topics}
                </select>
            </td>
        </tr>
        <tr><td><b>{{contents}}</b>:</td></tr>
        <tr>
            <td>
                <textarea name="newstext" cols="80" rows="10">{newstext}</textarea>
            </td>
        </tr>
        <tr><td><input type="checkbox" name="newshtml" id="newshtml" value="1" style="border:0;" {newshtml} /> <label for="newshtml">{{content_include_html}}</label></td></tr>
        <tr>
            <td>
                <img src="tpl/stdstyle/images/misc/hint.gif" border="0" width="15" height="11" alt="Hinweis" title="Hinweis" align="middle" />
                <span style="color:#666666; font-size:10px;">
                </span>
            </td>
        </tr>
        <tr><td class="spacer" colspan="2"></td></tr>
        <tr><td><b>{{email_address}}</b>: <input type="text" name="email" size="40" value="{email}" />{email_error}</td></tr>
        <tr><td class="spacer" colspan="2"></td></tr>
        <tr>
            <td>
                <button type="submit"  value="OK" style="font-size:12px;width:140px;"/><b>{{send}}</b></button>
            </td>
        </tr>
    </table>

</form>
