<?php /* Smarty version Smarty-3.0.8, created on 2012-03-31 14:29:52
         compiled from "tpl/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7164152984f76f8c0e7bd93-24212896%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '33239c84de49b23b7fa2dd5fded24dcd2c7ab90c' => 
    array (
      0 => 'tpl/index.tpl',
      1 => 1332775757,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7164152984f76f8c0e7bd93-24212896',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_smarty_tpl->tpl_vars['pagename'] = new Smarty_variable($_smarty_tpl->getVariable('mainpage_title')->value, null, null);?>

<?php $_template = new Smarty_Internal_Template("./tpl/header.inc.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>

<div id="content">		
				
	<div class='menu'>	
		<div class="button"><a href="./find.php" ><?php echo $_smarty_tpl->getVariable('seek')->value;?>
</a></div>
		<div class="button"><a href="./near.php" ><?php echo $_smarty_tpl->getVariable('seek_near')->value;?>
</a></div>				
		
		<?php if ($_SESSION['user_id']){?>
			<div class="button"><a href="./logentryfind.php" ><?php echo $_smarty_tpl->getVariable('entry')->value;?>
</a></div>
			<div class="button"><a href="./menu.php" ><?php echo $_smarty_tpl->getVariable('my_menu')->value;?>
</a></div>			
		<?php }?>
		
		<div class="button"><a href="./moar.php" ><?php echo $_smarty_tpl->getVariable('more')->value;?>
</a></div>
	</div>
	
</div>
	
<?php $_template = new Smarty_Internal_Template("./tpl/footer.inc.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>	