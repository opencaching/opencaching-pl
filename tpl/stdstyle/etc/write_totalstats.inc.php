<?php

global $lang, $rootpath;

if (!isset($rootpath)) $rootpath = '../../../';

require_once($rootpath . 'lib/common.inc.php');

ini_set('display_errors', 'On');

$hiddenCaches = sqlValue("SELECT COUNT(*) FROM `caches` WHERE (`status`=1 OR `status`=2 OR `status`=3)", 0);
$rs = sql('SELECT COUNT(*) AS `hiddens` FROM `caches` WHERE `status`=1');
$r = sql_fetch_array($rs);
$hiddens = $r['hiddens'];
$total_hiddens = $hiddenCaches;
mysql_free_result($rs);

$rs = sql('SELECT COUNT(*) AS `founds` FROM `cache_logs` WHERE (`type`=1 OR `type`=2) AND `deleted`=0');
$r = sql_fetch_array($rs);
$founds = $r['founds'];
mysql_free_result($rs);

$rs = sql('SELECT COUNT(*) AS `users` FROM (SELECT DISTINCT `user_id` FROM `cache_logs` WHERE (`type`=1 OR `type`=2) AND `deleted`=0 UNION DISTINCT SELECT DISTINCT `user_id` FROM `caches`) AS `t`');
$r = sql_fetch_array($rs);
$users = $r['users'];
mysql_free_result($rs);

$file_content = "<?php\n";
$file_content .= "tpl_set_var('hiddens', '$hiddens');\n";
$file_content .= "tpl_set_var('total_hiddens', '$total_hiddens');\n";
$file_content .= "tpl_set_var('founds', '$founds');\n";
$file_content .= "tpl_set_var('users', '$users');\n";
$file_content .= "?>\n";

$n_file = fopen($dynstylepath . "totalstats.inc.php", 'w');
fwrite($n_file, $file_content);
fclose($n_file);

?>