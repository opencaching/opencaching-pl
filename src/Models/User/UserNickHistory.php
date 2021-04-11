<?php
namespace src\Models\User;

use src\Models\BaseObject;

/**
 * Class to handle history of nick associated with user account
 * (for now this is a stub - see /myprofile.php for details)
 */
class UserNickHistory extends BaseObject
{

    public static function removeAllHistoryForUser(User $user): void
    {
        self::db()->multiVariableQuery("DELETE FROM user_nick_history WHERE user_id = :1", $user->getUserId());
    }
}
