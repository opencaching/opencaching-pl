<?php 

if (!isset($rootpath)) global $rootpath;
require_once('./lib/common.inc.php');
require_once('lib/cache_icon.inc.php');
global $caches_list, $usr, $hide_coords, $cache_menu, $octeam_email;
global $dynbasepath;

if ($_REQUEST['print'] != 'y')
{
	print __line__ . ' to powinno się wykonać<br/><br/>';
	var_dump($_REQUEST['print']);
}
else
{
	print __line__ . "to nie powinno się wykonać<br/><br/>";
	var_dump($_REQUEST['print']);
}

if(isset($_SESSION['i_m_sure_this_is_a_not_existing_index']))
{
	print __line__ . "to nie powinno się wykonać!! <br/><br/>";
}

exit;

print 'skrypt teraz uzywa pamieci: ' . memory_get_usage() . '<br>';
require_once 'lib/db.php';
print 'skrypt teraz uzywa pamieci: ' . memory_get_usage() . '<br>';

$a = new dataBase(true);

$cwpt = $a->paramQuery("SELECT `wp_oc` FROM `caches` WHERE `cache_id` = :cache_id", array('cache_id' => array ('value' => 4576, 'data_type' => 'integer')));
$data = $a->dbResultFetchAll();
$rows = $a->rowCount();
print 'skrypt teraz uzywa pamieci: ' . memory_get_usage() . '<br>';
unset ($a);
print 'skrypt teraz uzywa pamieci: ' . memory_get_usage() . '<br>';
// $a->__destruct();

print "result: <br>";
var_dump($data);
print '<br>';
print 'skrypt teraz uzywa pamieci: ' . memory_get_usage() . '<br>';
print 'max: uzyta pamiec: ' . memory_get_peak_usage () .'<br/><br/>';

/*
$zmienna = $a->multiVariableQuery('SELECT * from `caches` where  `user_id` between :1 and :2 and type = :3', 1, 5000, 2);

print 'uzyta pamiec: ' . memory_get_peak_usage ();
*/

?>