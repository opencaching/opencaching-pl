<?php
//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	
	tpl_set_var("bla", "dupa");
	
	$tplname = 'userstats';
	tpl_BuildTemplate();
?>
