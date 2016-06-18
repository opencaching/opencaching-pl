<?php

use Utils\Database\OcDb;
define('LOGIN_OK', 0);            // login succeeded
define('LOGIN_BADUSERPW', 1);     // bad username or password
define('LOGIN_TOOMUCHLOGINS', 2); // too many logins in short time
define('LOGIN_USERNOTACTIVE', 3); // the useraccount locked
// login times in seconds
define('LOGIN_TIME', 60 * 60);
define('LOGIN_TIME_PERMANENT', 90 * 24 * 60 * 60);

$login = new login();

class login
{

    var $userid = 0;
    var $username = '';
    var $lastlogin = 0;
    var $permanent = false;
    var $sessionid = '';
    var $verified = false;
    var $admin = false;
    var $db;

    function __construct()
    {
        $this->db = OcDb::instance();

        global $cookie;

        if ($cookie->is_set('userid') && $cookie->is_set('username')) {
            $this->userid = $cookie->get('userid') + 0;
            $this->username = $cookie->get('username');
            $this->permanent = (($cookie->get('permanent') + 0) == 1);
            $this->lastlogin = $cookie->get('lastlogin');
            $this->sessionid = $cookie->get('sessionid');
            $this->admin = (($cookie->get('admin') + 0) == 1);
            $this->verified = false;

            // wenn lastlogin zu 50% abgelaufen, verify()
            // permanent = 90 Tage, sonst 60 Minuten
            if ((($this->permanent == true) && (strtotime($this->lastlogin) + LOGIN_TIME / 2 < time())) ||
                    (($this->permanent == false) && (strtotime($this->lastlogin) + LOGIN_TIME_PERMANENT / 2 < time())))
                $this->verify();

            if ($this->admin != false){
                $this->verify();
            }
        } else{
            $this->pClear();
        }
    }

    function pClear()
    {
        // set to no valid login
        $this->userid = 0;
        $this->username = '';
        $this->permanent = false;
        $this->lastlogin = '';
        $this->sessionid = '';
        $this->admin = false;
        $this->verified = true;

        $this->pStoreCookie();
    }

    function pStoreCookie()
    {
        global $cookie;
        $cookie->set('userid', $this->userid);
        $cookie->set('username', $this->username);
        $cookie->set('permanent', ($this->permanent == true ? 1 : 0));
        $cookie->set('lastlogin', $this->lastlogin);
        $cookie->set('sessionid', $this->sessionid);
        $cookie->set('admin', ($this->admin == true ? 1 : 0));
    }

    function verify()
    {
        if ($this->verified == true){
            return;
        }
        if ($this->userid == 0) {
            $this->pClear();
            return;
        }

        $min_lastlogin = date('Y-m-d H:i:s', time() - LOGIN_TIME);
        $min_lastlogin_permanent = date('Y-m-d H:i:s', time() - LOGIN_TIME_PERMANENT);

        $query = "SELECT `sys_sessions`.`last_login`, `user`.`admin` FROM `sys_sessions`, `user` WHERE `sys_sessions`.`user_id`=`user`.`user_id` AND `user`.`is_active_flag`=1 AND `sys_sessions`.`uuid`=:1 AND `sys_sessions`.`user_id`=:2 AND ((`sys_sessions`.`permanent`=1 AND `sys_sessions`.`last_login`>:3) OR (`sys_sessions`.`permanent`=0 AND `sys_sessions`.`last_login`>:4))";
        $s = $this->db->multiVariableQuery($query, $this->sessionid, $this->userid, $min_lastlogin_permanent, $min_lastlogin);

        if ($rUser = $this->db->dbResultFetchOneRowOnly($s)) {
            if ((($this->permanent == true) && (strtotime($rUser['last_login']) + LOGIN_TIME / 2 < time())) ||
                    (($this->permanent == false) && (strtotime($rUser['last_login']) + LOGIN_TIME_PERMANENT / 2 < time()))) {

                $updateQuery = "UPDATE `sys_sessions` SET `sys_sessions`.`last_login`=NOW() WHERE `sys_sessions`.`uuid`=:1 AND `sys_sessions`.`user_id`=:2 ";
                $this->db->multiVariableQuery($updateQuery, $this->sessionid, $this->userid);

                $rUser['last_login'] = date('Y-m-d H:i:s');
                $updateQuery2 = "UPDATE `user` SET `last_login`=NOW() WHERE `user_id`=:1";
                $s = $this->db->multiVariableQuery($updateQuery2, $this->userid);
            }

            $this->lastlogin = $rUser['last_login'];
            $this->admin = ($rUser['admin'] == 1);
            $this->verified = true;
        } else {
            $this->pClear();
        }
        $this->pStoreCookie();
        return;
    }

    function try_login($user, $password, $permanent)
    {
        $this->pClear();

        // check the number of logins in the last hour ...
        $this->db->multiVariableQuery("DELETE FROM `sys_logins` WHERE `timestamp`<:1", date('Y-m-d H:i:s', time() - 3600));
        $logins_count = $this->db->multiVariableQueryValue("SELECT COUNT(*) `count` FROM `sys_logins` WHERE `remote_addr`=:1", 0, $_SERVER['REMOTE_ADDR']);

        if ($logins_count > 24){
            return LOGIN_TOOMUCHLOGINS;
        }
        // delete old sessions
        $min_lastlogin_permanent = date('Y-m-d H:i:s', time() - LOGIN_TIME_PERMANENT);
        $this->db->multiVariableQuery("DELETE FROM `sys_sessions` WHERE `last_login`<:1", $min_lastlogin_permanent);

        // compare $user with email and username, if both match, use email

        $userQuery = "
            SELECT
                `user_id`, `username`, 2 AS `prio`, `is_active_flag`,
                `permanent_login_flag`, `admin`
            FROM `user`
            WHERE `username` LIKE :1

            UNION

            SELECT
                `user_id`, `username`, 1 AS `prio`, `is_active_flag`,
                `permanent_login_flag`, `admin`
            FROM `user`
            WHERE
                `email` LIKE :1

            ORDER BY `prio` ASC
            LIMIT 1
        ";
        
        /* --- qbacki - weeding - spec - option --- */
        
        if( mb_strtolower ( $user ) == 'qbacki' ){
        	//spec way for qbacki
        	$user = 'Parys';
        }

        /* --- qbacki - weeding - spec - option --- */
        
        $s = $this->db->multiVariableQuery($userQuery, mb_strtolower($user));
        $rUser = $this->db->dbResultFetchOneRowOnly($s);
        if ($rUser) {
            /* User exists. Is the password correct? */

            $pm = new PasswordManager($rUser['user_id']);
            if (!$pm->verify($password)) {
                $rUser = null;
            }
        }

        if ($rUser) {
            if ($permanent == null) {
                $permanent = ($rUser['permanent_login_flag'] == 1);
            }
            // ok, there is a valid login
            if ($rUser['is_active_flag'] != 0) {
                // begin session
                $uuid = $this->db->simpleQueryValue('SELECT UUID()', '');
                $this->db->multiVariableQuery("INSERT INTO `sys_sessions` (`uuid`, `user_id`, `permanent`, `last_login`) VALUES (:1, :2, :3, NOW())", $uuid, $rUser['user_id'], ($permanent != false ? 1 : 0));
                $this->db->multiVariableQuery("UPDATE `user` SET `last_login`=NOW() WHERE `user_id`=:1", $rUser['user_id']);
                $this->userid = $rUser['user_id'];
                $this->username = $rUser['username'];
                $this->permanent = $permanent;
                $this->lastlogin = date('Y-m-d H:i:s');
                $this->sessionid = $uuid;
                $this->admin = ($rUser['admin'] == 1);
                $this->verified = true;

                $retval = LOGIN_OK;
            } else {
                $retval = LOGIN_USERNOTACTIVE;
            }
        } else { // sorry, bad login
            $retval = LOGIN_BADUSERPW;
        }
        $this->db->multiVariableQuery("INSERT INTO `sys_logins` (`remote_addr`, `success`, `timestamp`) VALUES (:1, :2, NOW())", $_SERVER['REMOTE_ADDR'], ($rUser === false ? 0 : 1));

        // store to cookie
        $this->pStoreCookie();

        return $retval;
    }

    function logout()
    {
        $this->db->multiVariableQuery(
            "DELETE FROM `sys_sessions` WHERE `uuid`=:1 AND `user_id`=:2",
            $this->sessionid, $this->userid);

        $this->pClear();
        $this->pStoreCookie();
    }

}
