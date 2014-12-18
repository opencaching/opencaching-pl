<table class="content">
    <tr>
        <td class="content2-pagetitle">
            <img src="tpl/stdstyle/images/blue/write.png" class="icon32" alt=""  /><font size="4">  <b>{{send_bulletin_01}}</b></font>
        </td>
    </tr>
    <tr><td class="spacer" colspan="2"></td></tr>
    <tr>
        <td>
            {{send_bulletin_02}}:<br />
            <form action='admin_bulletin.php' method='POST'>
                <input type="hidden" name="bulletin_final" value="1">
                {{bulletin}}
                <br />
                <input type='submit' value='{{send_bulletin_01}}'>
            </form>
        </td>
    </tr>
</table>
