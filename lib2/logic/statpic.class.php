<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require_once($opt['rootpath'] . 'lib2/logic/rowEditor.class.php');

class statpic
{
	var $nUserId = 0;

	var $reUser;

	function __construct($nNewUserId)
	{
		$this->reUser = new rowEditor('user');
		$this->reUser->addPKInt('user_id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->reUser->addString('statpic_text', '', false);
		$this->reUser->addString('statpic_logo', 0, false);

		$this->nUserId = $nNewUserId+0;

		$this->reUser->load($this->nUserId);
	}

	function getStyle()
	{
		return $this->reUser->getValue('statpic_logo');
	}

	function setStyle($value)
	{
		return $this->reUser->setValue('statpic_logo', $value);
	}

	function getText()
	{
		return $this->reUser->getValue('statpic_text');
	}

	function setText($value)
	{
		if ($value != '')
			if (!mb_ereg_match(REGEX_STATPIC_TEXT, $value))
				return false;

		return $this->reUser->setValue('statpic_text', $value);
	}

	function save()
	{
		$retval = $this->reUser->save();
		if ($retval)
			$this->invalidate();

		return $retval;
	}

	// unlink stored picture to force regeneration
	function invalidate()
	{
		global $opt;

		if (file_exists($opt['rootpath'] . 'images/statpics/statpic' . ($this->nUserId+0) . '.jpg'))
			unlink($opt['rootpath'] . 'images/statpics/statpic' . ($this->nUserId+0) . '.jpg');
	}
}
?>