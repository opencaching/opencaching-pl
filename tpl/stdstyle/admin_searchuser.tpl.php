<?
global $usr;
?>
<form name="optionsform" style="display:inline;" action='admin_searchuser.php' method="POST">
    <table class="content" border="0" cellspacing="0" cellpadding="0">
        <tr><td class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/search3.png" class="icon32" alt=""  /><img src="tpl/stdstyle/images/blue/profile.png" class="icon32" alt=""  /><font size="4">  <b>{{search_user}}</b></font></td></tr>
        <tr><td class="spacer"></td></tr>
        <tr>
            <td><br /><br />
                {not_found}
                <input type="text" name="username" value="{username}" class="input200" />
                <button type="submit" name="submit" value="{{search}}" style="font-size:12px;width:140px;"/><b>{{search}}</b></button>
                <br/>
            </td>
        </tr>
    </table>
</form>
