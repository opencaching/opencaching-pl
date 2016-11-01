<?php

use Utils\Database\XDb;

$rootpath = "../../";
require('../../lib/common.inc.php');

//setlocale(LC_ALL, 'pl_PL.utf-8');

$NUTS_AT_CSV_FILE = $argv[1];

$f = fopen($NUTS_AT_CSV_FILE, 'r');

while (($buffer = fgetcsv($f, 1000, ",")) !== false) {
    if (count($buffer) != 6)
        die('invalid format' . "\n");

    if ($buffer[1] != 'NUTS_ID' && $buffer[1] != '' && $buffer[2] != '') {
        //$bufferr = mb_convert_encoding($buffer[3], "utf-8", "auto");

        XDb::xSql("INSERT IGNORE INTO `nuts_codes` (`code`, `name`) VALUES ( ?, ?)", $buffer[1], $buffer[2]);
    }
}

fclose($f);
