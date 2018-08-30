<?php
namespace lib\Objects\Admin;

use lib\Objects\BaseObject;
use lib\Objects\User\User;

class AdminNoteSet extends BaseObject
{

    /**
     * Default admin notes count to display
     *
     * @var integer
     */
    const DEFAULT_NOTES_COUNT = 10;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns array of AdminNote objects for given $user
     *
     * @param User $user
     * @param int $limit
     * @param int $offset
     * @return AdminNote[]|NULL[]
     */
    public static function getNotesForUser(User $user, $limit = self::DEFAULT_NOTES_COUNT, $offset = 0)
    {
        list($limit, $offset) = self::db()->quoteLimitOffset($limit, $offset);

        $query = "
            SELECT `note_id`
            FROM `admin_user_notes`
            WHERE `user_id` = :1
            ORDER BY `datetime` DESC
            LIMIT $offset, $limit";

        $stmt = self::db()->multiVariableQuery($query, $user->getUserId());

        return self::db()->dbFetchAllAsObjects($stmt, function ($row) {
            return AdminNote::fromNoteIdFactory($row['note_id']);
        });
    }

    /**
     * Returns total count of $user's AdminNotes
     *
     * @param User $user
     * @return int
     */
    public static function getNotesForUserCount(User $user)
    {
        $query = "
            SELECT COUNT(*)
            FROM `admin_user_notes`
            WHERE `user_id` = :1";
        return self::db()->multiVariableQueryValue($query, 0, $user->getUserId());
    }
}