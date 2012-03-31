<?php /* Smarty version Smarty-3.0.8, created on 2012-03-31 14:29:53
         compiled from "./tpl/footer.inc.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18048009654f76f8c1092225-26663598%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '51a098026139725fd7b0a519f4d27bf418eb7fe3' => 
    array (
      0 => './tpl/footer.inc.tpl',
      1 => 1332775757,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18048009654f76f8c1092225-26663598',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
	<hr/>	
	<div id="footer">
		<div style="padding: 0 5px">
			<div class='menu'>	
				<table class="tablefooter" style="width: 100%">		
					<tr>
					<td class="button" style="width:50%"><a href="./about.php" ><?php echo $_smarty_tpl->getVariable('about')->value;?>
</a></td>
					<td class="button" style="width:50%"><a href="./contact.php" ><?php echo $_smarty_tpl->getVariable('contact')->value;?>
</a> </td></tr>
					<tr><td colspan="2" class="button"><a href="./index.php" ><?php echo $_smarty_tpl->getVariable('main_page')->value;?>
</a> </td></tr>
					<tr><td colspan="2" class="button"><a href="http://opencaching.pl/index.php?mobile=false" ><?php echo $_smarty_tpl->getVariable('pc_ver')->value;?>
</a></td>
					</tr>
				</table>
			</div>
		</div>
		
		<div class="footertitle">m.Opencaching.pl</div>
						
		<table class="tablefooter">
			<tr>
				<td class="button" style="width:35%"><a href="javascript:createcookie('lang','pl'); reloadpage();" >PL</a></td>
				<td class="button" style="width:35%"><a href="javascript:createcookie('lang','en'); reloadpage();" >EN</a></td>
				<!--<td class="button" style="width:25%"><a href="javascript:createcookie('lang','de'); reloadpage();" >DE</a></td>			-->
			</tr>
		</table>
					
	</div>

</body>

</html>

