#!/usr/bin/php -q
<?php
/**
 * 19.04.2016, kojoty
 *
 * This script is obsolate and unmaintenance!!!
 *
 *
 */
/* * *************************************************************************

  Ggf. muss die Location des php-Binaries angepasst werden.

  Dieses Script erstellt den Suchindex für Ortsnamen aus den Daten der
  GNS-DB.

 * ************************************************************************* */
header('Content-Type: text/plain');
set_time_limit(0);

$rootpath = '../../';
require_once($rootpath . 'lib/clicompatbase.inc.php');
require_once($rootpath . 'lib/search.inc.php');

/* begin db connect */
db_connect();
if ($dblink === false) {
    echo 'Unable to connect to database';
    exit;
}
/* end db connect */

/* begin search index rebuild */

$doubleindex['sankt'] = 'st';

sql('TRUNCATE TABLE `gns_search`');
sql("DROP TABLE IF EXISTS `gns_search`");
sql("CREATE TABLE `gns_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uni_id` int(11) NOT NULL DEFAULT '0',
  `sort` varchar(255) NOT NULL,
  `simple` varchar(255) NOT NULL,
  `simplehash` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `simplehash` (`simplehash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

$in_count = 0;
$out_count = 0;

$rs = sql("SELECT `uni`, `full_name_nd` FROM `gns_locations` WHERE `dsg` LIKE 'PPL%'");
while ($r = sql_fetch_array($rs)) {
    $in_count++;
    $simpletexts = search_text2sort($r['full_name_nd']);
    $simpletextsarray = explode_multi($simpletexts, ' -/,');

    foreach ($simpletextsarray AS $text) {
        if ($text != '') {
            /*              if (nonalpha($text))
              die($r['uni'] . ' ' . $text . "\n");
             */
            $simpletext = search_text2simple($text);

            sql("INSERT INTO `gns_search` (`uni_id`, `sort`, `simple`, `simplehash`) VALUES ('&1', '&2', '&3', '&4')", $r['uni'], $text, $simpletext, sprintf("%u", crc32($simpletext)));
            $out_count++;
            if (isset($doubleindex[$text])) {
                sql("INSERT INTO `gns_search` (`uni_id`, `sort`, `simple`, `simplehash`) VALUES ('&1', '&2', '&3', '&4')", $r['uni'], $text, $doubleindex[$text], sprintf("%u", crc32($doubleindex[$text])));
                $out_count++;
            }
        }
    }
}
mysql_free_result($rs);

echo "Processed $in_count rows, inserted $out_count index items";

/* end search index rebuild */

function nonalpha($str)
{
    for ($i = 0; $i < mb_strlen($str); $i++)
        if (!((ord(mb_substr($str, $i, 1)) >= ord('a')) && (ord(mb_substr($str, $i, 1)) <= ord('z'))))
            return true;

    return false;
}
?>
