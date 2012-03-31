<?php /* Smarty version Smarty-3.0.8, created on 2012-03-31 14:30:06
         compiled from "tpl/find.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19346627244f76f8cee18e37-30127742%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f22cfdf57277fed940c92385a425bfa1a7534e57' => 
    array (
      0 => 'tpl/find.tpl',
      1 => 1332775757,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19346627244f76f8cee18e37-30127742',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_smarty_tpl->tpl_vars['pagename'] = new Smarty_variable($_smarty_tpl->getVariable('find_title1')->value, null, null);?>

<?php $_template = new Smarty_Internal_Template("./tpl/header.inc.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>

<div id='pagetitle'><?php echo $_smarty_tpl->getVariable('find_title3')->value;?>
</div>

<div id="content">

	<?php if ($_smarty_tpl->getVariable('error')->value=='1'){?>
		<center><span class="error"><?php echo $_smarty_tpl->getVariable('no_wp')->value;?>
</span></center><br/>
	<?php }?>
	<?php if ($_smarty_tpl->getVariable('error')->value=='2'){?>
		<center><span class="error"><?php echo $_smarty_tpl->getVariable('too_short_find')->value;?>
</span></center><br/>
	<?php }?>

	<form action=".<?php echo $_smarty_tpl->getVariable('action')->value;?>
" method="get" name="form1">
		<?php echo $_smarty_tpl->getVariable('name')->value;?>
<br/>
		<input type="text" name="nazwa"/><br/><br/>
		<div class='menu'>
			<div class='button'>
				<a href='javascript: document.form1.submit()'><?php echo $_smarty_tpl->getVariable('seek_button')->value;?>
</a>
			</div>
		</div><br/><hr/>
	</form>	

	<form action=".<?php echo $_smarty_tpl->getVariable('action')->value;?>
" method="get" name="form2">	
		<?php echo $_smarty_tpl->getVariable('wpt')->value;?>
<br/>
		<input type="text" name="wp" value="OP"/><br/><br/>
		<div class='menu'>
			<div class='button'>
				<a href='javascript: document.form2.submit()'><?php echo $_smarty_tpl->getVariable('seek_button')->value;?>
</a>
			</div>
		</div><br/><hr/>
	</form>	

	<form action=".<?php echo $_smarty_tpl->getVariable('action')->value;?>
" method="get" name="form3">	
		<?php echo $_smarty_tpl->getVariable('owner')->value;?>
<br/>
		<input type="text" name="owner"/><br/><br/>
		<div class='menu'>
			<div class='button'>
				<a href='javascript: document.form3.submit()'><?php echo $_smarty_tpl->getVariable('seek_button')->value;?>
</a>
			</div>
		</div><br/>
	</form>

	<?php $_template = new Smarty_Internal_Template("./tpl/backbutton.inc.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
		
</div>

<?php $_template = new Smarty_Internal_Template("./tpl/footer.inc.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>	