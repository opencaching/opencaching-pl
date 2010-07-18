<?
global $usr;
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/search3.png" class="icon32" alt="" /><img src="tpl/stdstyle/images/blue/profile.png" class="icon32" alt="" />&nbsp;{{search_user}}</div>
<div class="errormsg">{not_found}</div>
<form name="optionsform" style="display:inline;" action='searchuser.php' method="post">
		<input type="text" name="username" value="{username}" class="input200" /> 
			<button type="submit" name="submit" value="{{search}}" style="font-size:12px;width:140px;"/><b>{{search}}</b></button>
<br/>
</form>
