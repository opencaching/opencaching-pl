<?php
namespace lib\Objects\User;

use lib\Objects\BaseObject;
use lib\Objects\OcConfig\OcConfig;

class UserCommons extends BaseObject
{
    // ROLE is a bitfild (SQL SET)
    const ROLE_OC_TEAM = 1;
    const ROLE_ADV_USER = 2;
    const ROLE_NEWS_PUBLISHER = 4;
    const ROLE_SYS_ADMIN = 8;

    public function __construct(array $params = null)
    {
        parent::__construct();
    }

    public function getRoleName($roleId)
    {
        switch($roleId)
        {
            case self::ROLE_OC_TEAM: return 'ocTeamMember';
            case self::ROLE_ADV_USER: return 'advUser';
            case self::ROLE_NEWS_PUBLISHER: return 'newsPublisher';
            case self::ROLE_SYS_ADMIN: return 'sysAdmin';
        }
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

    public static function getDefaultAvatarUrl()
    {
        return '/images/avatars/defaultAvatar.svg';
    }
}