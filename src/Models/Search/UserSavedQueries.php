<?php
namespace src\Models\Search;

use src\Models\BaseObject;
use src\Models\User\User;

/**
 * Class to handle saved user search queries
 * (for now this is a stub - see /query.php for details)
 */
class UserSavedQueries extends BaseObject
{
    public static function removeAllQueriesForUser(User $user): void
    {
        self::db()->multiVariableQuery("DELETE FROM queries WHERE user_id = :1", $user->getUserId());
    }
}
