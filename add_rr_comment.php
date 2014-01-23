<?php
//prepare the templates and include all neccessary
    require_once('./lib/common.inc.php');

    if( $usr['admin'] )
    {
        $_SESSION['submitted'] = false;
        $sql = "SELECT name FROM caches WHERE cache_id=".intval($_REQUEST['cacheid']);
        $cachename = @mysql_result(@mysql_query($sql),0);
        tpl_set_var('cachename', $cachename);
        tpl_set_var('cacheid', $_REQUEST['cacheid']);
        $tplname = 'add_rr_comment';
        tpl_BuildTemplate();
    }
?>
