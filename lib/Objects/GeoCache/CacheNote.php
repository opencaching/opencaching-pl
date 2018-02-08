<?php
namespace lib\Objects\GeoCache;

use lib\Objects\BaseObject;

class CacheNote extends BaseObject
{
    /**
     * Returns user note for given cache
     *
     * @param int $userId
     * @param int $cacheId
     * @return string
     */
    public static function getNote($userId, $cacheId)
    {
        return self::db()->multiVariableQueryValue(
            "SELECT `desc` FROM cache_notes
            WHERE cache_id = :1 AND user_id = :2
            LIMIT 1", '', $cacheId, $userId);
    }

    /**
     * Save given user note to DB
     *
     * @param int $userId
     * @param int $cacheId
     * @param string $noteContent
     */
    public static function storeNote($userId, $cacheId, $noteContent)
    {
        //TODO: Table cache_notes should have index on cache_id/user_id instead of autoincrement index!
        //      Then it could be possible to use INSERT ... ON DUPLICATE KEY UPDATE Syntax
        //      DELETE old coords to be sure there is no duplicates...
        self::deleteNote($userId, $cacheId);

        $noteContent = htmlspecialchars($noteContent, ENT_COMPAT, 'UTF-8');

        self::db()->multiVariableQuery(
            "INSERT INTO cache_notes
                (cache_id, user_id, `desc`, date)
            VALUES(:1, :2, :3, NOW() );",
            $cacheId, $userId, $noteContent);
    }

    /**
     * Delete selected user note
     *
     * @param int $userId
     * @param int $cacheId
     */
    public static function deleteNote($userId, $cacheId)
    {
        //TODO: add LIMIT 1 after note_id removig

        self::db()->multiVariableQuery(
            "DELETE FROM cache_notes
             WHERE cache_id = :1 AND user_id = :2", $cacheId, $userId);
    }

    /**
     * Returns the count of caches which has at least one of:
     *  - user custom coords (mod_coords)
     *  - user note
     * @param int $userId
     * @return int count
     */
    public static function getCountOfUserNotesAndModCoords($userId)
    {
        return self::db()->multiVariableQueryValue(
            "SELECT COUNT(*) FROM (
                SELECT cache_id FROM cache_notes WHERE user_id = :1
                UNION
                SELECT cache_id FROM cache_mod_cords WHERE user_id = :1
            ) x", 0, $userId);
    }

    /**
     * Returns array of cache-ids which
     *  - has custom coords (mod_coords) for current user
     *  - has note for current user
     * ordered by cache name and with limit/offset
     *
     * @param int $userId
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public static function getCachesIdsForNotesAndModCoords(
        $userId, $limit=null, $offset=null)
    {
        $db = self::db();

        list($limit, $offset) = $db->quoteLimitOffset($limit, $offset);

        $stmt = $db->multiVariableQuery(
            "SELECT cache_id FROM (
                SELECT cache_id FROM cache_notes WHERE user_id = :1
                UNION
                SELECT cache_id FROM cache_mod_cords WHERE user_id = :1
            ) x LEFT JOIN caches USING (cache_id)
            ORDER BY caches.name
            LIMIT $limit OFFSET $offset", $userId);

        return $db->dbFetchOneColumnArray($stmt, 'cache_id');
    }

    public static function getNotesByCacheIds(array $cacheIds, $userId)
    {
        if(empty($cacheIds)){
            return [];
        }
        $db = self::db();

        $cacheIdsStr = $db->quoteString( implode(',', $cacheIds) );

        $rs = $db->multiVariableQuery(
                "SELECT * FROM cache_notes
                WHERE user_id = :1 AND cache_id IN ($cacheIdsStr)", $userId);

        return $db->dbResultFetchAll($rs);
    }

}
