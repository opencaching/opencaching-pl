<?php
//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');

		$sql1 = "SELECT user_id FROM caches WHERE cache_id=".intval($_REQUEST['cacheid']);
		$userid = @mysql_result(@mysql_query($sql1),0);

	if( $usr['admin'] || ($usr['userid'] == $userid) )
	{
		$_SESSION['submitted'] = false;
		$sql = "SELECT name FROM caches WHERE cache_id=".intval($_REQUEST['cacheid']);
		$cachename = @mysql_result(@mysql_query($sql),0);
		tpl_set_var('cachename', $cachename);
		tpl_set_var('cacheid', $_REQUEST['cacheid']);
		$tplname = 'add_octeam_comment';
		tpl_BuildTemplate();
	}
?>
