<?php

/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************
	     
   Unicode Reminder ăĄă˘
                                    				                                
	 OC PL code for api keys
	
 ****************************************************************************/



	require('./lib/common.inc.php');

	if ($error == false)
	{

		//user logged in?
		if ($usr == false)
		{
		    $target = urlencode(tpl_get_current_page());
		    tpl_redirect('login.php?target='.$target);
		}
		else
		{



		$tplname = 'api_keys';
		$user_id=$usr['userid'];

		// exist user_id in keys table ?
		$userid_key=sqlValue("SELECT `key` FROM `keys` WHERE `user_id`='".$user_id."'",0);
		if ($userid_key =="0")
		{
		// DISPLAY Rules confirm
		$tplname="api_keys_confirm";
		}
		$idkey=sqlValue("SELECT `id` FROM `keys` WHERE `user_id`='".$user_id."'",0);
		tpl_set_var('api_key',$userid_key);
		tpl_set_var('userid',$user_id);
		tpl_set_var('idkey',$idkey);

	    

		if (isset($_POST['back']))
		{	

				tpl_redirect('index.php');
				exit;
		}						

		if (isset($_POST['delete']) )
		{	
			$idkey=$_POST['idkey'];
			//remove 
			sql("DELETE FROM `keys` WHERE `id`='&1'", $idkey);
			tpl_redirect('index.php');
			exit;
		}


		if (isset($_POST['confirm']) || isset($_POST['new_key']) )
		{	

	function _key_exists($key)
	{

		$key_exists=sqlValue("SELECT COUNT(*) FROM `keys` WHERE `key`='".$key."'",0);
			    
		return $key_exists;
	}


	function _generate_key()
	{
		
		do
		{
			$salt = sha1(time().mt_rand());
			// Key length = 16
			$new_key = substr($salt, 0,16);
		}

		// Already in the DB? Fail. Try again
		while (_key_exists($new_key)>0);

		return $new_key;
	}
		if (isset($_POST['userid']))
		{ $userid=$_POST['userid']; }
		if (isset($_REQUEST['userid']))
		{ $userid=$_REQUEST['userid']; }
		if (isset($_POST['idkey']))
		{ $idkey=$_POST['idkey']; }
		if (isset($_REQUEST['idkey']))
		{ $idkey=$_REQUEST['idkey']; }
		

	/**
	 * Key Create
	 *
	 * Insert a key into the database.
	 */
		$tplname = 'api_keys';
		// Build a new key
		$key = _generate_key();

		// If no key level provided, give them a rubbish one
		$level = 1;
		$ignore_limits = 1;
		if($idkey=="0") {
		// Insert the new key
		sql("INSERT INTO `keys` (`key`,`user_id`,`level`,`ignore_limits`,`date`) VALUES ('&1','&2','&3','&4',NOW())",$key,$user_id,$level,$ignore_limits);
		$idkey=sqlValue("SELECT `id` FROM `keys` WHERE `user_id`='".$user_id."'",0);
		tpl_set_var('idkey',$idkey);
		} else {
		sql("UPDATE `keys` SET `key`='&1',`user_id`='&2',`level`='&3',`ignore_limits`='&4' WHERE `id`='&5'",$key,$user_id,$level,$ignore_limits,$idkey);
		}
		tpl_set_var('api_key',$key);
		tpl_set_var('userid',$userid);


//	}
    }
    }		
}
			tpl_BuildTemplate();
?>
