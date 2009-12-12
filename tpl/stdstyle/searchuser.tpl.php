<?
global $usr;
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/blue/search3.png" border="0" width="32" height="32" alt="" title="" align="middle"/><img src="tpl/stdstyle/images/blue/profile.png" border="0" width="32" height="32" alt="" title="" align="middle"/>&nbsp;Szukaj użytkownika</div>
<div class="errormsg">{{not_found}}</div>
<form name="optionsform" style="display:inline;" action='searchuser.php' method="post">
		<input type="text" name="username" value="{username}" class="input200" /> <input type="submit" value="Szukaj" class="formbuttons" /><br/>
</form>
