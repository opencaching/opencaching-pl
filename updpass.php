<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder Ă„Ă‚Ă„Ă„Ă‚Ă‱
 ***************************************************************************/
	$rootpath = './';
	require_once($rootpath.'lib/clicompatbase.inc.php');
  

/* begin with some constants */

	$sDateformat = 'Y-m-d H:i:s';

/* end with some constants */

//checkJob(new geokrety());

class ClearFakeVotes
{
	
	//var $name = 'geokrety';
	//var $interval = 900;

	function run()
	{
		/* begin db connect */
		db_connect();
		if ($dblink === false)
		{
			echo 'Unable to connect to database';
			exit;
		}
	/* end db connect */

		$sql = "SELECT user_id, password FROM user";
		$query = mysql_query($sql);
		while( $res = mysql_fetch_array($query) )
		{
			//echo "has�o dla ".$res['user_id'].": ".$res['password']."<br>";
			$passold = $res['password'];
			$passnew = hash('sha512', $res['password']);
			$sql2 = "UPDATE user SET password='".$passnew."' WHERE user_id = ".$res['user_id'];
			mysql_query($sql2);
		}
		db_disconnect();
	}
}

$clearFakeVotes = new ClearFakeVotes();
$clearFakeVotes->run();

?>
