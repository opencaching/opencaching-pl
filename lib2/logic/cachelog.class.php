<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *   get/set has to be commited with save
 *   add/remove etc. is executed instantly
 ***************************************************************************/

require_once($opt['rootpath'] . 'lib2/logic/rowEditor.class.php');

class cachelog
{
	var $nLogId = 0;

	var $reCacheLog;

	static function logIdFromUUID($uuid)
	{
		$cacheid = sql_value("SELECT `id` FROM `cache_logs` WHERE `uuid`='&1'", 0, $uuid);
		return $cacheid;
	}

	static function fromUUID($uuid)
	{
		$logid = cachelog::logIdFromUUID($uuid);
		if ($logid == 0)
			return null;

		return new cachelog($logid);
	}

	function __construct($nNewLogId=ID_NEW)
	{
		$this->reCacheLog = new rowEditor('cache_logs');
		$this->reCacheLog->addPKInt('id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->reCacheLog->addString('uuid', '', false, RE_INSERT_OVERWRITE|RE_INSERT_UUID);
		$this->reCacheLog->addInt('node', 0, false);
		$this->reCacheLog->addDate('date_created', time(), true, RE_INSERT_IGNORE);
		$this->reCacheLog->addDate('last_modified', time(), true, RE_INSERT_IGNORE);
		$this->reCacheLog->addInt('cache_id', 0, false);
		$this->reCacheLog->addInt('user_id', 0, false);
		$this->reCacheLog->addInt('type', 0, false);
		$this->reCacheLog->addDate('date', time(), false);
		$this->reCacheLog->addString('text', '', false);
		$this->reCacheLog->addInt('text_html', 0, false);
		$this->reCacheLog->addInt('text_htmledit', 0, false);
		$this->reCacheLog->addInt('owner_notified', 0, false);
		$this->reCacheLog->addInt('picture', 0, false);

		$this->nLogId = $nNewLogId+0;

		if ($nNewLogId == ID_NEW)
		{
			$this->reCacheLog->addNew(null);
		}
		else
		{
			$this->reCacheLog->load($this->nLogId);
		}
	}

	function exist()
	{
		return $this->reCacheLog->exist();
	}

	function getLogId()
	{
		return $this->nLogId;
	}
	function getUserId()
	{
		return $this->reCacheLog->getValue('user_id');
	}
	function getCacheId()
	{
		return $this->reCacheLog->getValue('cache_id');
	}

	function getUUID()
	{
		return $this->reCacheLog->getValue('uuid');
	}
	function getLastModified()
	{
		return $this->reCacheLog->getValue('last_modified');
	}
	function getDateCreated()
	{
		return $this->reCacheLog->getValue('date_created');
	}
	function getNode()
	{
		return $this->reCacheLog->getValue('node');
	}
	function setNode($value)
	{
		return $this->reCacheLog->setValue('node', $value);
	}

	function getAnyChanged()
	{
		return $this->reCacheLog->getAnyChanged();
	}

	// return if successfull (with insert)
	function save()
	{
		return $this->reCacheLog->save();
	}

	function allowView()
	{
		global $login;

		$login->verify();
		if (sql_value("SELECT `cache_status`.`allow_user_view` FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE `caches`.`cache_id`='&1'", 0, $this->getCacheId()) == 1)
			return true;
		else if ($login->userid == sql_value("SELECT `user_id` FROM `caches` WHERE `cache_id`='&1'", 0, $this->getCacheId()))
			return true;

		return false;
	}

	function allowEdit()
	{
		global $login;

		$login->verify();
		if ($this->getUserId() == $login->userid)
			return true;

		return false;
	}
}
?>