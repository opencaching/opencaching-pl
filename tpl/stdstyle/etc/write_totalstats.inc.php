<?php

use Utils\Database\XDb;
global $lang, $rootpath;

if (!isset($rootpath))
    $rootpath = '../../../';

require_once($rootpath . 'lib/common.inc.php');

ini_set('display_errors', 'On');

$total_hiddens = XDb::xSimpleQueryValue(
    "SELECT COUNT(*) FROM `caches` WHERE (`status`=1 OR `status`=2 OR `status`=3)", 0);

$hiddens = XDb::xSimpleQueryValue(
    'SELECT COUNT(*) AS `hiddens` FROM `caches` WHERE `status`=1', 0);

$founds = XDb::xSimpleQueryValue(
    'SELECT COUNT(*) AS `founds` FROM `cache_logs`
    WHERE (`type`=1 OR `type`=2) AND `deleted`=0', 0);

$users = XDb::xSimpleQueryValue(
    'SELECT COUNT(*) AS `users` FROM
        (
            SELECT DISTINCT `user_id` FROM `cache_logs` WHERE (`type`=1 OR `type`=2) AND `deleted`=0
                UNION DISTINCT
            SELECT DISTINCT `user_id` FROM `caches`
        ) AS `t`', 0);


require_once($rootpath . 'lib/settings.inc.php');
$hiddens = number_format($hiddens, 0, $config['numberFormatDecPoint'], $config['numberFormatThousandsSep']);
$total_hiddens = number_format($total_hiddens, 0, $config['numberFormatDecPoint'], $config['numberFormatThousandsSep']);
$founds = number_format($founds, 0, $config['numberFormatDecPoint'], $config['numberFormatThousandsSep']);
$users = number_format($users, 0, $config['numberFormatDecPoint'], $config['numberFormatThousandsSep']);

$file_content = "<?php\n";
$file_content .= "tpl_set_var('hiddens', '$hiddens');\n";
$file_content .= "tpl_set_var('total_hiddens', '$total_hiddens');\n";
$file_content .= "tpl_set_var('founds', '$founds');\n";
$file_content .= "tpl_set_var('users', '$users');\n";
$file_content .= "?>\n";

$n_file = fopen($dynstylepath . "totalstats.inc.php", 'w');
fwrite($n_file, $file_content);
fclose($n_file);
