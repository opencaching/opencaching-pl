<?php
global $usr;
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/search3.png" class="icon32" alt="" /><img src="tpl/stdstyle/images/blue/profile.png" class="icon32" alt="" />&nbsp;{{search_user}}</div>
<form name="optionsform" style="display:inline;" action='searchuser.php' method="post" class="form-group">
    <input type="text" name="username" value="{username}" class="form-control input200" />
    <button type="submit" name="submit" value="{{search}}" class="btn btn-primary" /><b>{{search}}</b></button>
<br/>
</form>
<div class="errormsg">{not_found}</div>


<div class="searchdiv">
    <table class="table" border="0" cellspacing="0">
        <colgroup>
            <col width="650px"/>
            <col width="1x"/>
        </colgroup>
        <tr>
            <td><p class="content-title-noshade">{{user}}</p></td>
            <td>&nbsp;</td>
            <td nowrap="nowrap"><p class="content-title-noshade">{{registered_since_label}}</p></td>
            <td nowrap="nowrap">&nbsp;</td>
        </tr>
        {lines}
    </table>
</div>
