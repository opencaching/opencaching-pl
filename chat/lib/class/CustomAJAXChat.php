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

    //Check user Level
    $user_level = sqlValue("SELECT level FROM ajax_chat_users WHERE `userID`=$user_id",0);
    // get region from Home coordiantes
    $usrcountry = sqlValue("SELECT country FROM user WHERE `user_id`=$user_id",0);
    $lon = sqlValue("SELECT longitude FROM user WHERE `user_id`=$user_id",0);
    $lat = sqlValue("SELECT latitude FROM user WHERE `user_id`=$user_id",0);
    $point='POINT(' . $lon . ' ' . $lat . ')';
    if ($lat==0 && $lon==0) {$region=$usrcountry;} else {
            $sCode = '';
            $rsLayers = sql("SELECT `level`, `code`, AsText(`shape`) AS `geometry` FROM `nuts_layer` WHERE WITHIN(GeomFromText('$point'), `shape`) ORDER BY `level` DESC");
            while ($rLayers = mysql_fetch_assoc($rsLayers))
            {

                if (gis::ptInLineRing($rLayers['geometry'], 'POINT(' . $lon . ' ' . $lat . ')'))
                {
                    $sCode = $rLayers['code'];
                    break;
                }
            }
            mysql_free_result($rsLayers);
            if ($sCode != '')
            {
                $adm1 = null; $code1 = null;
                $adm2 = null; $code2 = null;
                $adm3 = null; $code3 = null;
                $adm4 = null; $code4 = null;

                if (mb_strlen($sCode) > 5) $sCode = mb_substr($sCode, 0, 5);

                if (mb_strlen($sCode) == 5)
                {
                    $code4 = $sCode;
                    $adm4 = sqlValue("SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'",0);
                    $sCode = mb_substr($sCode, 0, 4);
                }

                if (mb_strlen($sCode) == 4)
                {
                    $code3 = $sCode;
                    $adm3 = sqlvalue("SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'",0);
                    $sCode = mb_substr($sCode, 0, 3);
                }

                if (mb_strlen($sCode) == 3)
                {
                    $code2 = $sCode;
                    $adm2 = sqlvalue("SELECT `name` FROM `nuts_codes` WHERE `code`='$sCode'", 0);
                    $sCode = mb_substr($sCode, 0, 2);
                }

                if (mb_strlen($sCode) == 2)
                {

                    $adm1 = $usrcountry;
            }
            $region=$adm3;
            }
            }
            $usern=$usr['username'].'['.$region.']';
                    $userData = array();
                    $userData['userID'] = $usr['userid'];
                    $userData['userName'] = $this->trimUserName($usern);
                    if ($user_level>0)
                    {$userData['userRole'] = AJAX_CHAT_MODERATOR;}
                    else
                    {$userData['userRole'] = AJAX_CHAT_USER;}

                    return $userData;
        }

    // Store the channels the current user has access to
    // Make sure channel names don't contain any whitespace
    function &getChannels() {
    global $usr;
        if($this->_channels === null) {
            $this->_channels = array();

            $user_id=$this->getUserID();

            //Check user Level
            $user_level = sqlValue("SELECT level FROM ajax_chat_users WHERE `userID`=$user_id",0);
            if ($user_level>0){
            $oc_team_channels=array(17,18,19);
            $validChannels=array_merge($this->getConfig('limitChannelList'),$oc_team_channels);
            $limitChannelList=array_merge($this->getConfig('limitChannelList'),$oc_team_channels);
            } else {
            $validChannels =$this->getConfig('limitChannelList');
            $oc_team_channels=array();
            $limitChannelList=$this->getConfig('limitChannelList');
            }
            // Add the valid channels to the channel list (the defaultChannelID is always valid):
            foreach($this->getAllChannels() as $key=>$value) {
                // Check if we have to limit the available channels:
                if(!in_array($value, $limitChannelList)) {
                    continue;
                }

                if(in_array($value, $validChannels) || $value == $this->getConfig('defaultChannelID')) {
                    $this->_channels[$key] = $value;
                }
            }
        }
        return $this->_channels;
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
