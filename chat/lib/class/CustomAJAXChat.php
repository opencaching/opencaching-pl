<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

class CustomAJAXChat extends AJAXChat {

	// Returns an associative array containing userName, userID and userRole
	// Returns null if login is invalid

	// Initialize custom configuration settings
	function initCustomConfig() {
		global $dblink;
		
		// Use the existing MyBB database connection:
		$this->setConfig('dbConnection', 'link', $dblink);
	}



	// Initialize custom request variables:
	function initCustomRequestVars() {
		global $usr;

		// Auto-login ocpl users:
		if(!$this->getRequestVar('logout') && $usr==true) 
		{
			$this->setRequestVar('login', true);
		} 		
	}
	// Returns true if the userID of the logged in user is identical to the userID of the authentication system
	// or the user is authenticated as guest in the chat and the authentication system
	function revalidateUserID() {
		global $usr;
		
		if($this->getUserID() === $usr['userid']) {
			return true;
		}
		return false;
	}


	function getValidLoginUserData() {
	global $usr,$dblink;

	$user_id=$usr['userid'];
	$asadmin=0;
	$asadmin = sqlValue("SELECT admin FROM user WHERE `user_id`=$user_id",0);
		
					$userData = array();
					$userData['userID'] = $usr['userid'];
					$userData['userName'] = $this->trimUserName($usr['username']);
					if ($asadmin==1)
					{$userData['userRole'] = AJAX_CHAT_MODERATOR;}
					else
					{$userData['userRole'] = AJAX_CHAT_USER;}

					return $userData;
		}
	
	// Store all existing channels
	// Make sure channel names don't contain any whitespace
	function &getAllChannels() {
		if($this->_allChannels === null) {
			// Get all existing channels:
			$customChannels = $this->getCustomChannels();
			
			$defaultChannelFound = false;
			
			foreach($customChannels as $key=>$value) {
				$forumName = $this->trimChannelName($value);
				
				$this->_allChannels[$forumName] = $key;
				if($key == $this->getConfig('defaultChannelID')) {
					$defaultChannelFound = true;
				}
			}
			
			if(!$defaultChannelFound) {
				// Add the default channel as first array element to the channel list:
				$this->_allChannels = array_merge(
					array(
						$this->trimChannelName($this->getConfig('defaultChannelName'))=>$this->getConfig('defaultChannelID')
					),
					$this->_allChannels
				);
			}
		}
		return $this->_allChannels;
	}
	
	function &getCustomChannels() {
		// List containing the custom channels:
		$channels = null;
		require(AJAX_CHAT_PATH.'lib/data/channels.php');
		return $channels;
	}

}
?>
