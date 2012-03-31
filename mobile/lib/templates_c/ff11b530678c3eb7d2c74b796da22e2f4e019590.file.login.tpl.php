<?php /* Smarty version Smarty-3.0.8, created on 2012-03-31 14:59:16
         compiled from "tpl/login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9017716874f76ffa467c413-64278542%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ff11b530678c3eb7d2c74b796da22e2f4e019590' => 
    array (
      0 => 'tpl/login.tpl',
      1 => 1332775757,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9017716874f76ffa467c413-64278542',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_smarty_tpl->tpl_vars['pagename'] = new Smarty_variable($_smarty_tpl->getVariable('login_title')->value, null, null);?>

<?php $_template = new Smarty_Internal_Template("./tpl/header.inc.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>

<div id='pagetitle'><?php echo $_smarty_tpl->getVariable('login_title2')->value;?>
</div>

<div id='content'>

	<?php if ($_smarty_tpl->getVariable('error')->value=='2'){?>
		<center><span class="error"><?php echo $_smarty_tpl->getVariable('no_cookie')->value;?>
</span></center>
	<?php }else{ ?>
		<?php if ($_smarty_tpl->getVariable('error')->value=='1'){?>
			<center><span class="error"><?php echo $_smarty_tpl->getVariable('wrong_login_pass')->value;?>
<br/><br/></span></center>
		<?php }?>
		<form name='form1' action='#' method='post'>
			<?php echo $_smarty_tpl->getVariable('user')->value;?>
<br/>
			<input type='text' name='username'/><br/><br/>			
			<?php echo $_smarty_tpl->getVariable('passw')->value;?>
<br/>
			<input type='password' name='pass'/><br/><br/>
			<input type='checkbox' name='remember' /> <span onClick="document.form1.remember.checked=(! document.form1.remember.checked);"><?php echo $_smarty_tpl->getVariable('remember_me')->value;?>
</span><br/><br/>				
			<div class='menu'>
				<div class='button'><a href='javascript: document.form1.submit()'><?php echo $_smarty_tpl->getVariable('login_button')->value;?>
</a></div>
			</div>
		</form><br/>
	<?php }?>
		
	<?php $_template = new Smarty_Internal_Template("./tpl/backbutton.inc.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
	
</div>
	
<?php $_template = new Smarty_Internal_Template("./tpl/footer.inc.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>	