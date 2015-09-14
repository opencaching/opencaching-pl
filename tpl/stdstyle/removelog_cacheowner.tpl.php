<?php

?>
<form action="removelog.php" method="post" enctype="application/x-www-form-urlencoded" name="removelog_form" dir="ltr">
    <input type="hidden" name="commit" value="1"/>
    <input type="hidden" name="logid" value="{logid}"/>
    <table class="table">
        <tr><td class="content2-pagetitle" colspan="2"><img src="tpl/stdstyle/images/blue/logs.png" class="icon32" alt="" /> &nbsp;<b>{{delete_logentry}} <a href="viewcache.php?cacheid={cacheid}">{cachename}</a></b></td></tr>
        <tr><td class="buffer"></td></tr>

        <tr><td>{{confirm_remove_log}}?</td></tr>
        <tr><td class="buffer"></td></tr>

        <tr><TD>{log}</TD></tr>
        <tr><td class="buffer"></td></tr>

        <tr><td >{{add_comment_to_remove}} {log_user_name}?</td></tr>
        <tr>
            <td>
                <textarea class="logs" name="logowner_message"></textarea>
            </td>
        </tr>
        <tr><td class="buffer"></td></tr>

        <tr>
            <td >
                <button type="submit" name="submit"  value="{{delete}}" style="font-size:12px;width:140px;"/><b>{{delete}}</b></button>
            </td>
        </tr>
    </table>
</form>
