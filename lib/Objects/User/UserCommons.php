<?php

namespace lib\Objects\User;

use lib\Objects\BaseObject;


class UserCommons extends BaseObject {

    public function __construct(array $params=null)
    {
        parent::__construct();

    }

    public static function GetUserProfileUrl($userId){
        return "/viewprofile.php?userid=$userId";
    }

}
