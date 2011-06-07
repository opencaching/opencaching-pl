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

		if ($userid_key==NULL)
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
		
		if (isset($_POST['new_key'])){
		$idkey=$_POST['idkey'];
		//remove 
		sql("DELETE FROM `keys` WHERE `id`='&1'", $idkey);

		}

	/**
	 * Key Create
	 *
	 * Insert a key into the database.
	 */

		$userid=$_POST['userid'];
		$tplname = 'api_keys';

		$userid_exists=sqlValue("SELECT COUNT(*) FROM `keys` WHERE `user_id`='".$userid."'",0);
		if ($userid_exist==NULL) {
		// Build a new key
		$key = _generate_key();

		// If no key level provided, give them a rubbish one
		$level = 1;
		$ignore_limits = 1;

		// Insert the new key
		sql("INSERT INTO `keys` (`id`,`key`,`user_id`,`level`,`ignore_limits`,`date_created`) VALUES ('','&1','&2','&3','&4',NOW())",$key,$user_id,$level,$ignore_limits);
		$idkey=sqlValue("SELECT `id` FROM `keys` WHERE `user_id`='".$userid."'",0);
		tpl_set_var('api_key',$key);
		tpl_set_var('userid',$userid);
		tpl_set_var('idkey',$idkey);


	}
    }



    }		
}
			tpl_BuildTemplate();
?>
