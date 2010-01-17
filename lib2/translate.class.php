<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$translate = new translate();

class translate
{
	/* create all files in cache2/translate
	 */
	function createMessageFiles()
	{
		$rs = sqlf("SELECT DISTINCT `lang` FROM `sys_trans_text`");
		while ($r = sql_fetch_assoc($rs))
		{
			$this->createMessageFile($r['lang']);
		}
		sql_free_result($rs);
	}

	/* create file in cache2/translate/$language/LC_MESSAGES/...
	 */
	function createMessageFile($language)
	{
		global $opt;

		$language_upper = mb_strtoupper($language);
		$language_lower = mb_strtolower($language);
		if (!isset($opt['locale'][$language_upper]))
			return;

		if (!is_dir($opt['rootpath'] . 'cache2/translate/' . $language_lower))
			mkdir($opt['rootpath'] . 'cache2/translate/' . $language_lower);
		if (!is_dir($opt['rootpath'] . 'cache2/translate/' . $language_lower . '/LC_MESSAGES'))
			mkdir($opt['rootpath'] . 'cache2/translate/' . $language_lower . '/LC_MESSAGES');

		$f = fopen($opt['rootpath'] . 'cache2/translate/' . $language_lower . '/LC_MESSAGES/messages.po', 'w');

		fwrite($f, 'msgid ""' . "\n");
		fwrite($f, 'msgstr ""' . "\n");
		fwrite($f, '"MIME-Version: 1.0\n"' . "\n");
		fwrite($f, '"Content-Type: text/plain; charset=UTF-8\n"' . "\n");
		fwrite($f, '"Content-Transfer-Encoding: 8bit\n"' . "\n");
		fwrite($f, "\n");

		$rs = sqlf("SELECT `sys_trans`.`text` AS `text`, `sys_trans_text`.`text` AS `trans` FROM `sys_trans` INNER JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' WHERE `sys_trans`.`text`!=''", $language_upper);
		while ($r = sql_fetch_assoc($rs))
		{
			fwrite($f, 'msgid "' . $this->escpae_text($r['text']) . '"' . "\n");
			fwrite($f, 'msgstr "' . $this->escpae_text($r['trans']) . '"' . "\n");
			fwrite($f, "\n");
		}
		sql_free_result($rs);

		fclose($f);

		@exec('msgfmt -o ' . escapeshellcmd($opt['rootpath'] . 'cache2/translate/' . $language_lower . '/LC_MESSAGES/messages.mo') . ' ' . escapeshellcmd($opt['rootpath'] . 'cache2/translate/' . $language_lower . '/LC_MESSAGES/messages.po'));
	}

	/* translate the given string
	 */
	function t($message, $style, $resource_name, $line, $plural='', $count=1)
	{
		global $opt;

		if ($message == '')
			return '';

		if ($plural != '' && $count!=1)
			$message = $plural;
		$search = $this->prepare_text($message);

		if (($opt['debug'] & DEBUG_TRANSLATE) == DEBUG_TRANSLATE)
		{
			$this->add_text($message, $style, $resource_name, $line);
			$this->add_text($plural, $style, $resource_name, $line);
		}

		return gettext($search);
	}

	/* strip whitespaces
	 */
	function prepare_text($text)
	{
		$text = mb_ereg_replace("\t", ' ', $text);
		$text = mb_ereg_replace("\r", ' ', $text);
		$text = mb_ereg_replace("\n", ' ', $text);
		while (mb_strpos($text, '  ') !== false)
			$text = mb_ereg_replace('  ', ' ', $text);

		return $text;
	}

	/* escape string for po-file
	 */
	function escpae_text($text)
	{
		$text = mb_ereg_replace('\\\\', '\\\\', $text);
		$text = mb_ereg_replace('"', '\"', $text);
		$text = mb_ereg_replace("\r", "", $text);
		while (mb_substr($text, -1, 1) == "\n")
			$text = mb_substr($text, 0, mb_strlen($text) - 1);
		$text = mb_ereg_replace("\n", "\\n\"\n\"", $text);
		return $text;
	}

	/* add text to database
	 */
	function add_text($text, $style, $resource_name, $line)
	{
		global $opt;

		if ($text == '') return;

		$text = $this->prepare_text($text);

		$trans_id = sqlf_value("SELECT `id` FROM `sys_trans` WHERE `text`='&1'", 0, $text);
		if ($trans_id == 0)
		{
			sqlf("INSERT INTO `sys_trans` (`text`) VALUES ('&1')", $text);
			$trans_id = sql_insert_id();
		}

		if (($opt['debug'] & DEBUG_TRANSLATE) == DEBUG_TRANSLATE)
		{
			sqlf("INSERT INTO `sys_trans_ref` (`trans_id`, `style`, `resource_name`, `line`) VALUES ('&1', '&2', '&3', '&4')", $trans_id, $style, $resource_name, $line);
		}
	}

	/* clear sys_trans_ref to begin new translation of resource
	 */
	function initTranslation($style, $resource_name)
	{
		global $opt;

		if (($opt['debug'] & DEBUG_TRANSLATE) == DEBUG_TRANSLATE)
		{
			sqlf("DELETE FROM `sys_trans_ref` WHERE `style`='&1' AND `resource_name`='&2'", $style, $resource_name);
		}
	}

	/* import strings from given field to sys_trans_text
	 */
	function importFromTable($table, $fname = 'name', $fid = 'trans_id')
	{
		$rs = sqlf("SELECT `&1`.`&2` FROM `&1` LEFT JOIN `sys_trans` ON `&1`.`&3`=`sys_trans`.`id` AND `&1`.`&2`=`sys_trans`.`text` WHERE ISNULL(`sys_trans`.`text`)", $table, $fname, $fid);
		while ($r = sql_fetch_array($rs))
		{
			$lastId = sqlf_value("SELECT `id` FROM `sys_trans` WHERE `text`='&1'", 0, $r[$fname]);
			if ($lastId == 0)
			{
				sqlf("INSERT INTO `sys_trans` (`text`) VALUES ('&1')", $r[$fname]);
				$lastId = sql_insert_id();
			}

			sqlf("INSERT INTO `sys_trans_ref` (`trans_id`, `style`, `resource_name`, `line`) VALUES ('&1', '', '&2', 0)", $lastId, $table);
		}
		sql_free_result($rs);
		sqlf("UPDATE `&1` SET `&2`=0", $table, $fid);
		sqlf("UPDATE `&1`, `sys_trans` SET `&1`.`&3`=`sys_trans`.`id` WHERE `&1`.`&2`=`sys_trans`.`text`", $table, $fname, $fid);
	}
}
?>