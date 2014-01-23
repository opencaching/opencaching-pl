<?php
  //prepare the templates and include all neccessary
  $rootpath = '../../';
    require_once($rootpath . 'lib/common.inc.php');

    //Preprocessing
    if ($error == false)
    {
        header('Content-type: text/html; charset=utf-8');

        $cache_id = 0;
        if (isset($_REQUEST['cacheid']))
        {
            $cache_id = $_REQUEST['cacheid'];
        }
        else if (isset($_REQUEST['uuid']))
        {
            $uuid = $_REQUEST['uuid'];

            $rs = sql("SELECT `cache_id` FROM `caches` WHERE uuid='&1' LIMIT 1", $uuid);
            if ($r = sql_fetch_assoc($rs))
            {
                $cache_id = $r['cache_id'];
            }
            mysql_free_result($rs);
        }
        else if (isset($_REQUEST['wp']))
        {
            $wp = $_REQUEST['wp'];
            $sql = 'SELECT `cache_id` FROM `caches` WHERE wp_';
            if (mb_strtoupper(mb_substr($wp, 0, 2)) == 'GC')
                $sql .= 'gc';
            else if (mb_strtoupper(mb_substr($wp, 0, 2)) == 'NC')
                $sql .= 'nc';
            else
                $sql .= 'oc';

            $sql .= '=\'' . sql_escape($wp) . '\' LIMIT 1';

            $rs = sql($sql);
            if ($r = sql_fetch_assoc($rs))
            {
                $cache_id = $r['cache_id'];
            }
            mysql_free_result($rs);
        }

        if ($cache_id != 0)
        {
            $rs = sql("SELECT `caches`.`cache_id` `cache_id`, `caches`.`name` `cachename`, `user`.`username` `username`, `caches`.`wp_oc` `wp_oc`, `caches`.`wp_gc` `wp_gc`, `caches`.`wp_nc` `wp_nc` FROM `caches`, `user` WHERE `caches`.`user_id`=`user`.`user_id` AND `caches`.`cache_id`='&1'", $cache_id);
            if ($r = sql_fetch_array($rs))
            {
                echo $r['cache_id'];
                echo ';';
                echo '"' . mb_ereg_replace('"', '\"', $r['cachename']) . '"';
                echo ';';
                echo '"' . mb_ereg_replace('"', '\"', $r['username']) . '"';
                echo ';';
                echo '"' . mb_ereg_replace('"', '\"', $r['wp_oc']) . '"';
                echo ';';
                echo '"' . mb_ereg_replace('"', '\"', $r['wp_gc']) . '"';
                echo ';';
                echo '"' . mb_ereg_replace('"', '\"', $r['wp_nc']) . '"';
            }
            else
                echo '0';
            mysql_free_result($rs);
        }
        else
            echo '0';
    }
?>
