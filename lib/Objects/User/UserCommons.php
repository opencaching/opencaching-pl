<?php
namespace lib\Objects\User;

use lib\Objects\BaseObject;
use lib\Objects\OcConfig\OcConfig;

class UserCommons extends BaseObject
{

    public function __construct(array $params = null)
    {
        parent::__construct();
    }

    public static function GetUserProfileUrl($userId)
    {
        return "/viewprofile.php?userid=$userId";
    }

    public static function deleteStatpic($userId)
    {
        $userId = intval($userId);

        if (file_exists(OcConfig::instance()->getDynamicFilesPath() . 'images/statpics/statpic' . $userId . '.jpg')) {
            unlink(OcConfig::instance()->getDynamicFilesPath() . 'images/statpics/statpic' . $userId . '.jpg');
        }
    }
}