<?php


use Config\ExampleConfig;
use Config\XExampleConfig;
require_once 'lib/common.inc.php';
echo "TEST:<hr/>";


$a = ExampleConfig::getExampleVar1();
$b = ExampleConfig::getExampleVar2();
$c = ExampleConfig::getExampleVar3();

d($a);
d($b);
d($c);

$a = XExampleConfig::getXExampleVar1();
$b = XExampleConfig::getXExampleVar2();
$c = XExampleConfig::getXExampleVar3();

d($a);
d($b);
d($c);

echo XExampleConfig::getLoadedConfigName();
echo '<br/>';

echo ExampleConfig::getLoadedConfigName();
echo '<br/>';


echo "<hr/>END!";