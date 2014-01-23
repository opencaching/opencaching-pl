<?php

$login = new login();

class login
{
    var $userid = 0;
    var $username = '';
    var $lastlogin = 0;
    var $sessionid = '';
    var $verified = false;

    function login()
    {
        global $cookie;
        db_connect();
        if ($cookie -> is_set('username') && $cookie -> is_set('userid') && $cookie -> is_set('lastlogin') && $cookie -> is_set('sessionid')){

            $this->username = mysql_real_escape_string($cookie -> get('username'));
            $this->userid = mysql_real_escape_string($cookie -> get('userid'));
            $this->lastlogin = mysql_real_escape_string($cookie -> get('lastlogin'));
            $this->sessionid = mysql_real_escape_string($cookie -> get('sessionid'));

            if ( !isset($_SESSION['user_id']) && !empty($this->username) && !empty($this->userid) && !empty($this->lastlogin) && !empty($this->sessionid))
                $this->verify();
        }
    }

    function pClear()
    {
        $this->userid = 0;
        $this->username = '';
        $this->lastlogin = '';
        $this->sessionid = '';
        $this->verified = true;
        $this->pStoreCookie();
    }

    function pStoreCookie()
    {
        global $cookie;
        $cookie->set('userid', $this->userid);
        $cookie->set('username', $this->username);
        $cookie->set('lastlogin', $this->lastlogin);
        $cookie->set('sessionid', $this->sessionid);
        $cookie -> header();
    }

    function verify()
    {
        if ($this->verified == true)
            return;

        if ($this->userid == 0)
        {
            $this->pClear();
            return;
        }

        $query = "select user_id,username,last_login_mobile,uuid_mobile from user where username = '".$this->username."' and user_id = '".$this->userid."' and last_login_mobile='".$this->lastlogin."' and uuid_mobile = '".$this->sessionid."'";
        $wynik = db_query($query);
        $ile = mysql_fetch_assoc($wynik);

        if(!empty($ile['username']) && !empty($ile['user_id']) && $ile['uuid_mobile']!='NULL' && $ile['last_login_mobile']!='0000-00-00 00:00:00')
        {
            $_SESSION['username']=$ile['username'];
            $_SESSION['user_id']=$ile['user_id'];
            $this->verified = true;
        }
        else
        {
            $this->pClear();
            $this->pStoreCookie();
        }

        return;
    }

    function try_login($user, $password, $remember)
    {
        $this->pClear();

        db_connect();

        $query = "select user_id,username from user where username = '".mysql_real_escape_string($user)."' and password ='".hash('sha512', md5(mysql_real_escape_string($password)))."';";
        $wynik = db_query($query);
        $wiersz=mysql_fetch_assoc($wynik);
        $user_id = $wiersz['user_id'];

        if(!empty($user_id)) {
            $_SESSION['username']=$wiersz['username'];
            $_SESSION['user_id']=$user_id;

            $query = "SELECT now() as now, uuid() as uuid";
            $wynik = db_query($query);
            $rekord = mysql_fetch_assoc($wynik);
            $dzis = $rekord['now'];
            $uuid = $rekord['uuid'];

            $query = "update user set last_login_mobile = '".$dzis."' where user_id='".$user_id."';";
            db_query($query);

            $this->userid = $user_id;
            $this->username = $user;
            $this->lastlogin = $dzis;
            $this->sessionid = $uuid;
            $this->verified = true;

            if($remember==1)
                $this->pStoreCookie();

            $query = "update user set uuid_mobile ='".$uuid."', last_login_mobile='".$dzis."' where user_id='".$user_id."';";
            db_query($query);

        }
        return;
    }

    function logout()
    {

        $query = "update user set uuid_mobile ='NULL', last_login_mobile='0000-00-00 00:00:00' where user_id='".$_SESSION['user_id']."';";
        db_query($query);

        unset($_SESSION['user_id']);
        unset($_SESSION['username']);

        session_destroy();

        $this->pClear();
        $this->pStoreCookie();
    }
}

?>