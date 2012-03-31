<?php /* Smarty version Smarty-3.0.8, created on 2012-03-31 14:59:35
         compiled from "tpl/viewcache.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5182844864f76ffb7ee8989-96924538%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '96dd3dc3bb5ad816973b3e12178cb2485c38bd9c' => 
    array (
      0 => 'tpl/viewcache.tpl',
      1 => 1332775757,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5182844864f76ffb7ee8989-96924538',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_smarty_tpl->tpl_vars['pagename'] = new Smarty_variable($_smarty_tpl->getVariable('view_title1')->value, null, null);?>

<?php $_template = new Smarty_Internal_Template("./tpl/header.inc.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>

<div id="content">

	<?php if ($_smarty_tpl->getVariable('error')->value=='1'){?>
		<span class="error"><center><?php echo $_smarty_tpl->getVariable('no_wp')->value;?>
</center></span>
	<?php }else{ ?>

		<div><?php if ($_smarty_tpl->getVariable('cache')->value['if_found']=='1'){?>
					<img src="../images/16x16-found.png"/>
					<?php }elseif($_smarty_tpl->getVariable('cache')->value['if_found']=='2'){?>
					<img src="../images/16x16-dnf.png"/>
					<?php }?>
					
					<?php if ($_smarty_tpl->getVariable('cache')->value['status2']=='2'){?>
					<img src="../images/flag.png" alt=""/>
					<?php }?>
					<?php if ($_smarty_tpl->getVariable('cache')->value['status2']=='3'){?>
					<img src="../images/bin.png" alt=""/>
					<?php }?>
	
		<b><?php echo $_smarty_tpl->getVariable('cache')->value['name'];?>
</b></div>
				
		<div><i><?php echo $_smarty_tpl->getVariable('cache')->value['short_desc'];?>
</i></div>
		
		<hr/>

		<b>N <?php echo $_smarty_tpl->getVariable('cache')->value['N'];?>
</b> (N <?php echo $_smarty_tpl->getVariable('cache')->value['latitude'];?>
)<br/>
		<b>E <?php echo $_smarty_tpl->getVariable('cache')->value['E'];?>
</b> (E <?php echo $_smarty_tpl->getVariable('cache')->value['longitude'];?>
)<br/><br/>
								
		<table class="tableview">
			<tr><td><?php echo $_smarty_tpl->getVariable('type')->value;?>
</td><td><b><?php echo $_smarty_tpl->getVariable('cache')->value['type'];?>
</b></td></tr>
			<tr><td><?php echo $_smarty_tpl->getVariable('size')->value;?>
</td><td><b><?php echo $_smarty_tpl->getVariable('cache')->value['size'];?>
</b></td></tr>
			<tr><td><?php echo $_smarty_tpl->getVariable('status')->value;?>
</td><td><b>
			
			<?php if ($_smarty_tpl->getVariable('cache')->value['status2']!='1'){?>
			<span class="error">
			<?php }?>
			<?php echo $_smarty_tpl->getVariable('cache')->value['status'];?>

			<?php if ($_smarty_tpl->getVariable('cache')->value['status2']!='1'){?>
			</span>
			<?php }?>
			</b></td></tr>
			<tr><td><?php echo $_smarty_tpl->getVariable('hidden_date')->value;?>
</td><td><b><?php echo $_smarty_tpl->getVariable('cache')->value['hidden_date'];?>
</b></td></tr>
			<tr><td><?php echo $_smarty_tpl->getVariable('view_wpt')->value;?>
</td><td><b><?php echo $_smarty_tpl->getVariable('cache')->value['wp_oc'];?>
</b></td></tr>
			<tr><td><?php echo $_smarty_tpl->getVariable('score')->value;?>
</td><td><b>
			
			<?php if ($_smarty_tpl->getVariable('cache')->value['score']=='0'){?><?php echo $_smarty_tpl->getVariable('rate0')->value;?>
<?php }?>
			<?php if ($_smarty_tpl->getVariable('cache')->value['score']=='1'){?><?php echo $_smarty_tpl->getVariable('rate1')->value;?>
<?php }?>
			<?php if ($_smarty_tpl->getVariable('cache')->value['score']=='2'){?><?php echo $_smarty_tpl->getVariable('rate2')->value;?>
<?php }?>
			<?php if ($_smarty_tpl->getVariable('cache')->value['score']=='3'){?><?php echo $_smarty_tpl->getVariable('rate3')->value;?>
<?php }?>
			<?php if ($_smarty_tpl->getVariable('cache')->value['score']=='4'){?><?php echo $_smarty_tpl->getVariable('rate4')->value;?>
<?php }?>
			<?php if ($_smarty_tpl->getVariable('cache')->value['score']=='5'){?>N/A<?php }?>
			
			</b></td></tr>
			<tr><td><?php echo $_smarty_tpl->getVariable('view_owner')->value;?>
</td><td>
			
			<?php if ($_SESSION['user_id']){?>
			<a href='./user.php?id=<?php echo $_smarty_tpl->getVariable('cache')->value['user_id'];?>
'><?php echo $_smarty_tpl->getVariable('cache')->value['owner'];?>
</a>
			<?php }else{ ?>
			<?php echo $_smarty_tpl->getVariable('cache')->value['owner'];?>

			<?php }?>
			</td></tr>
			<tr><td><?php echo $_smarty_tpl->getVariable('topratings')->value;?>
</td><td><?php echo $_smarty_tpl->getVariable('cache')->value['topratings'];?>
</td></tr>
			<tr><td><?php echo $_smarty_tpl->getVariable('stats')->value;?>
</td><td><b><span style="color:green"><?php echo $_smarty_tpl->getVariable('cache')->value['founds'];?>
</span> / <span style="color:red"><?php echo $_smarty_tpl->getVariable('cache')->value['notfounds'];?>
</span> / <?php echo $_smarty_tpl->getVariable('cache')->value['notes'];?>
</b></td></tr>		
		</table>
	<br/>
		<?php if ($_smarty_tpl->getVariable('attr_text')->value!=''){?>
			<div class='button' style="width:22%; margin: -10px 0 5px 0; display:inline-block;"><a style="display:block;" href='javascript:alert("<?php echo $_smarty_tpl->getVariable('attr_text')->value;?>
");'><img style="vertical-align: middle;" src="../images/attributes.png" alt="<?php echo $_smarty_tpl->getVariable('show_attrib')->value;?>
"/></a></div>
		<?php }?>
		
			<div class="button" style="width:22%; margin: -10px 0 5px 0; display:inline-block;"><a style="display:block;" href='./file2.php?wp=<?php echo $_smarty_tpl->getVariable('cache')->value['wp_oc'];?>
'><img style="vertical-align: middle; " src="../images/download.png" alt="<?php echo $_smarty_tpl->getVariable('download_file')->value;?>
"/></a></div>
			<div class="button" style="width:22%; margin: -10px 0 5px 0; display:inline-block;"><a style="display:block;" href='./googlemaps.php?wp=<?php echo $_smarty_tpl->getVariable('cache')->value['wp_oc'];?>
'><img style="vertical-align: middle;" src="../images/geo.png" alt="<?php echo $_smarty_tpl->getVariable('show_map')->value;?>
"/></a> </div>	
			
			<?php if ($_smarty_tpl->getVariable('cache')->value['watched']==-1){?>
			<div class="button" style="width:22%; margin: -10px 0 5px 0; display:inline-block;"><a style="display:block;" href='./watchcache.php?wp=<?php echo $_smarty_tpl->getVariable('cache')->value['wp_oc'];?>
'><img style="vertical-align: middle;" src="../images/eye.png" alt="<?php echo $_smarty_tpl->getVariable('watch')->value;?>
"/></a> </div>	
			<?php }?>
			<?php if ($_smarty_tpl->getVariable('cache')->value['watched']>-1){?>
			<div class="button" style="width:22%; margin: -10px 0 5px 0; display:inline-block;"><a style="display:block;" href='./removewatch.php?id=<?php echo $_smarty_tpl->getVariable('cache')->value['watched'];?>
'><img style="vertical-align: middle;" src="../images/eye2.png" alt="<?php echo $_smarty_tpl->getVariable('not_watch')->value;?>
"/></a> </div>	
			<?php }?>
		<br/><hr/><br/>
					
		<div id="description"><?php echo $_smarty_tpl->getVariable('cache')->value['desc'];?>
</div><br/>
			
		<?php if ($_smarty_tpl->getVariable('gk')->value!=''){?>
			<hr/><br/><div class='button'><a href='javascript:alert("\n<?php echo $_smarty_tpl->getVariable('gk')->value;?>
");'><?php echo $_smarty_tpl->getVariable('show_gk')->value;?>
</a></div><br/>
		<?php }?>
		
		<?php if ($_smarty_tpl->getVariable('cache')->value['hint']!=''){?>
			<br/><div class='button'><a href='javascript:alert("\n<?php echo $_smarty_tpl->getVariable('cache')->value['hint'];?>
\n\n");'><?php echo $_smarty_tpl->getVariable('show_spoiler')->value;?>
</a></div><br/>
		<?php }?>
				
		<?php if ($_smarty_tpl->getVariable('cache')->value['picturescount']>'0'){?>							
			<hr/><br/><b><?php echo $_smarty_tpl->getVariable('photos')->value;?>
:</b><br/><br/>
				<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('photos_list')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
					<div class='button'>
						<a target=blank href=<?php echo $_smarty_tpl->getVariable('photos_list')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['url'];?>
><?php echo $_smarty_tpl->getVariable('photos_list')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['title'];?>
			
						<?php ob_start();?><?php echo $_smarty_tpl->getVariable('photos_list')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['spoiler'];?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1=='1'){?>(spoiler)<?php }?>				
						</a>
					</div><br/>				
				<?php endfor; endif; ?>								
		<?php }?>
				
		<?php if ($_smarty_tpl->getVariable('cache')->value['founds']>0||$_smarty_tpl->getVariable('cache')->value['notfounds']>0||$_smarty_tpl->getVariable('cache')->value['notes']>0){?>
			<hr/><br/><div class='button'><a href=./logs.php?wp=<?php echo $_smarty_tpl->getVariable('cache')->value['wp_oc'];?>
><?php echo $_smarty_tpl->getVariable('show_entries')->value;?>
</a></div><br/>
		<?php }elseif($_SESSION['user_id']){?> 
			<hr/><br/>
		<?php }?>
				
				
		<?php if ($_SESSION['user_id']){?>
			<div class='button'><a href="./logentry.php?wp=<?php echo $_smarty_tpl->getVariable('cache')->value['wp_oc'];?>
"><?php echo $_smarty_tpl->getVariable('add_entry')->value;?>
</a></div><br/>
		<?php }?>
			
	<?php }?>

	<?php $_template = new Smarty_Internal_Template("./tpl/backbutton.inc.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
	
</div>	

<?php $_template = new Smarty_Internal_Template("./tpl/footer.inc.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
