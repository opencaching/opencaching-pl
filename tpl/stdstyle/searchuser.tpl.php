<?
global $usr;
?>
<div class="content2-pagetitle"><img src="tpl/stdstyle/images/cache/traditional.png" border="0" width="32" height="32" alt="" title="" align="middle"/>&nbsp;Szukaj użytkownika</div>
<div class="errormsg">{not_found}</div>
<form name="optionsform" style="display:inline;" action='searchuser.php' method="POST">
		<input type="text" name="username" value="{username}" class="input200" /> <input type="submit" value="Szukaj" class="formbuttons" /><br/>
</form>