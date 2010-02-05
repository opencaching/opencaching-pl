<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

// try to include cache file
if (!file_exists($opt['rootpath'] . 'cache2/labels-' . $opt['template']['locale'] . '.inc.php'))
	labels::CreateCacheFile();

require($opt['rootpath'] . 'cache2/labels-' . $opt['template']['locale'] . '.inc.php');

class labels
{
	static $aLabels = array();

	static function CreateCacheFile()
	{
		global $opt;

		$f = fopen($opt['rootpath'] . 'cache2/labels-' . $opt['template']['locale'] . '.inc.php', 'w');
		fwrite($f, "<?php\n");

		$a = array();
		$rs = sql("SELECT `cache_attrib`.`id`, IFNULL(`sys_trans_text`.`text`, `cache_attrib`.`name`) AS `name`
		             FROM `cache_attrib`
		        LEFT JOIN `sys_trans` ON `cache_attrib`.`trans_id`=`sys_trans`.`id` AND `cache_attrib`.`name`=`sys_trans`.`text`
		        LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1'", 
		                  $opt['template']['locale']);
		while ($r = sql_fetch_assoc($rs))
			$a[$r['id']] = $r['name'];
		sql_free_result($rs);
		fwrite($f, 'labels::addLabels("cache_attrib", "' . addslashes(serialize($a)) . '");' . "\n");

		$a = array();
		$rs = sql("SELECT `cache_size`.`id`, IFNULL(`sys_trans_text`.`text`, `cache_size`.`name`) AS `name`
		             FROM `cache_size`
		        LEFT JOIN `sys_trans` ON `cache_size`.`trans_id`=`sys_trans`.`id` AND `cache_size`.`name`=`sys_trans`.`text`
		        LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1'", 
		                  $opt['template']['locale']);
		while ($r = sql_fetch_assoc($rs))
			$a[$r['id']] = $r['name'];
		sql_free_result($rs);
		fwrite($f, 'labels::addLabels("cache_size", "' . addslashes(serialize($a)) . '");' . "\n");

		$a = array();
		$rs = sql("SELECT `cache_status`.`id`, IFNULL(`sys_trans_text`.`text`, `cache_status`.`name`) AS `name`
		             FROM `cache_status`
		        LEFT JOIN `sys_trans` ON `cache_status`.`trans_id`=`sys_trans`.`id` AND `cache_status`.`name`=`sys_trans`.`text`
		        LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1'", 
		                  $opt['template']['locale']);
		while ($r = sql_fetch_assoc($rs))
			$a[$r['id']] = $r['name'];
		sql_free_result($rs);
		fwrite($f, 'labels::addLabels("cache_status", "' . addslashes(serialize($a)) . '");' . "\n");

		$a = array();
		$rs = sql("SELECT `cache_type`.`id`, IFNULL(`sys_trans_text`.`text`, `cache_type`.`name`) AS `name`
		             FROM `cache_type`
		        LEFT JOIN `sys_trans` ON `cache_type`.`trans_id`=`sys_trans`.`id` AND `cache_type`.`name`=`sys_trans`.`text`
		        LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1'", 
		                  $opt['template']['locale']);
		while ($r = sql_fetch_assoc($rs))
			$a[$r['id']] = $r['name'];
		sql_free_result($rs);
		fwrite($f, 'labels::addLabels("cache_type", "' . addslashes(serialize($a)) . '");' . "\n");

		$a = array();
		$rs = sql("SELECT `log_types`.`id`, IFNULL(`sys_trans_text`.`text`, `log_types`.`name`) AS `name`
		             FROM `log_types`
		        LEFT JOIN `sys_trans` ON `log_types`.`trans_id`=`sys_trans`.`id` AND `log_types`.`name`=`sys_trans`.`text`
		        LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1'", 
		                  $opt['template']['locale']);
		while ($r = sql_fetch_assoc($rs))
			$a[$r['id']] = $r['name'];
		sql_free_result($rs);
		fwrite($f, 'labels::addLabels("log_types", "' . addslashes(serialize($a)) . '");' . "\n");

		fwrite($f, "?>");
		fclose($f);
	}

	static function addLabels($name, $serialized)
	{
		self::$aLabels[$name] = unserialize($serialized);
	}

	static function getLabels($name)
	{
		if (isset(self::$aLabels[$name]))
			return self::$aLabels[$name];
		else
			return false;
	}

	static function getLabelValue($name, $id)
	{
		if (isset(self::$aLabels[$name]))
			if (isset(self::$aLabels[$name][$id]))
				return self::$aLabels[$name][$id];
			else
				return false;
		else
			return false;
	}
}
?>