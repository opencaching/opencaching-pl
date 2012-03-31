<?php /* Smarty version Smarty-3.0.8, created on 2012-03-31 14:29:53
         compiled from "./tpl/header.inc.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3535959164f76f8c1026cf6-82910321%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6ff1533e9a7f11d5b1039e33c11ec36aeab268ce' => 
    array (
      0 => './tpl/header.inc.tpl',
      1 => 1332775757,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3535959164f76f8c1026cf6-82910321',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN"
"http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
  
<html xmlns="http://www.w3.org/1999/xhtml">

<head>	
	<meta name="description" content="Geocaching Opencaching Polska"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="pl" />
	<title>Opencaching PL Mobile - <?php echo $_smarty_tpl->getVariable('pagename')->value;?>
</title>	
	<meta name="HandheldFriendly" content="true" />
	<meta name="Viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
	<link rel="stylesheet" type="text/css" href="../lib/style.css" />
	<script type="text/javascript" src="../lib/script.js"></script>	
	<?php echo $_smarty_tpl->getVariable('HeaderContent')->value;?>

</head>

<body style="max-width:600px; margin:auto; padding: 8px;">

	<div id="header">
	
		<div id="logo" class="headertitle">		
			<a href="./index.php" ><img alt="" src="../images/oc_logo.gif"/>m.Opencaching.pl</a>							
		</div>
		
		<div id="login_nav" class="button">
			<?php if ($_SESSION['user_id']){?>
				<a href='./logout.php'><?php echo $_smarty_tpl->getVariable('login_info')->value;?>
 <b><?php echo $_SESSION['username'];?>
</b><br/></span><span class='login'><?php echo $_smarty_tpl->getVariable('logout')->value;?>
</span></a>
			<?php }else{ ?>
				<a href='login.php'><span class='login'><?php echo $_smarty_tpl->getVariable('login')->value;?>
</span></a>
			<?php }?>
		</div> 
		<hr/>
		
	</div>