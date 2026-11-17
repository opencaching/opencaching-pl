<?php

declare(strict_types=1);

namespace src\Models\Badges;

use src\Enums\Badges\BadgePositionsEnum;
use src\Enums\CacheStatusEnum;
use src\Utils\Database\OcDb;
use src\Utils\Debug\Debug;

/**
 * Database access for badge positions list.
 */
class BadgePositionsRepository
{
    private readonly OcDb $db;

    public function __construct(?OcDb $db = null)
    {
        $this->db = $db ?? OcDb::instance();
    }

    /**
     * Check whether a user exists.
     */
    public function userExists(int $userId): bool
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT 1 FROM `user` WHERE `user_id` = :1 LIMIT 1',
            $userId
        );

        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row);
    }

    /**
     * Read a badge (raw) by id.
     *
     * @return array<string, mixed>|null
     */
    public function findBadge(int $badgeId): ?array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `id`, `name`, `picture`, `belonging_query`,
                    `show_positions_list`, `show_positions_map`, `active`
             FROM `badges`
             WHERE `id` = :1
             LIMIT 1',
            $badgeId
        );

        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? $row : null;
    }

    /**
     * Count positions of a badge (excluding OC-Team-blocked caches).
     *
     * @param int|null $userId When provided, found-filter may be applied.
     * @param int $foundFilter 0 = all, 1 = not found, 2 = found
     */
    public function countPositions(int $badgeId, string $belongingQuery, ?int $userId = null, string $foundFilter = 'all'): int
    {
        $query = trim($belongingQuery);
        if ($query === '' || strtolower($query) === 'all') {
            return 0;
        }

        $blockedStatus = CacheStatusEnum::BLOCKED_BY_OC_TEAM->value;
        $where = '`c`.`status` <> ' . $blockedStatus;

        if ($userId !== null && $userId > 0) {
            if ($foundFilter === 'not_found') {
                $where .= ' AND `ul`.`cache_id` IS NULL';
            } elseif ($foundFilter === 'found') {
                $where .= ' AND `ul`.`cache_id` IS NOT NULL';
            }
        }

        // For found-filter we need the join; for 'all' count is independent of user.
        $sql = 'SELECT COUNT(*) AS `cnt`
                FROM (' . $query . ') AS `bp`
                JOIN `caches` AS `c` ON `c`.`cache_id` = `bp`.`badge_position_id`';

        if ($userId !== null && $userId > 0 && $foundFilter !== 'all') {
            $sql .= '
                LEFT JOIN `cache_logs` AS `ul`
                       ON `ul`.`cache_id` = `c`.`cache_id`
                      AND `ul`.`user_id` = ' . (int) $userId . '
                      AND `ul`.`type` = 1';
        }

        $sql .= '
                WHERE ' . $where;

        try {
            $statement = $this->db->simpleQuery($sql);
            $row = $this->db->dbResultFetchOneRowOnly($statement);
        } catch (\Throwable $e) {
            Debug::errorLog('BadgePositionsRepository::countPositions failed for badge ' . $badgeId . ': ' . $e->getMessage());

            return 0;
        }

        if (is_array($row) && isset($row['cnt'])) {
            return (int) $row['cnt'];
        }

        return 0;
    }

    /**
     * Build additional SQL condition for found-filter.
     */
    private function foundFilterSql(?int $userId, string $foundFilter): string
    {
        if ($userId === null || $userId <= 0 || $foundFilter === BadgePositionsEnum::FOUND_ALL) {
            return '';
        }

        if ($foundFilter === BadgePositionsEnum::FOUND_NOT_FOUND) {
            return ' AND `ul`.`cache_id` IS NULL';
        }

        if ($foundFilter === BadgePositionsEnum::FOUND_FOUND) {
            return ' AND `ul`.`cache_id` IS NOT NULL';
        }

        return '';
    }

    /**
     * Read positions of a badge (paginated, sorted), excluding OC-Team-blocked caches.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findPositions(
        int $badgeId,
        string $belongingQuery,
        int $limit,
        int $offset,
        string $orderBy,
        ?int $userId = null,
        string $foundFilter = 'all'
    ): array {
        $query = trim($belongingQuery);
        if ($query === '' || strtolower($query) === 'all') {
            return [];
        }

        $foundJoin = '';
        $foundSelect = '0 AS `found_by_user`';
        if ($userId !== null && $userId > 0) {
            $foundSelect = 'CASE WHEN `ul`.`cache_id` IS NOT NULL THEN 1 ELSE 0 END AS `found_by_user`';
            $foundJoin = 'LEFT JOIN `cache_logs` AS `ul`
                          ON `ul`.`cache_id` = `c`.`cache_id`
                         AND `ul`.`user_id` = ' . (int) $userId . '
                         AND `ul`.`type` = 1';
        }

        $sql = 'SELECT `c`.`cache_id` AS `cache_id`,
                       `c`.`name` AS `name`,
                       `c`.`type` AS `type`,
                       `c`.`status` AS `status`,
                       `c`.`date_published` AS `date_published`,
                       `c`.`user_id` AS `owner_id`,
                       `u`.`username` AS `owner_name`,
                       ' . $foundSelect . '
                FROM (' . $query . ') AS `bp`
                JOIN `caches` AS `c` ON `c`.`cache_id` = `bp`.`badge_position_id`
                LEFT JOIN `user` AS `u` ON `u`.`user_id` = `c`.`user_id`
                ' . $foundJoin . '
                WHERE `c`.`status` <> ' . CacheStatusEnum::BLOCKED_BY_OC_TEAM->value . $this->foundFilterSql($userId, $foundFilter) . '
                ORDER BY ' . $orderBy . '
                LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;

        try {
            $statement = $this->db->simpleQuery($sql);

            return $this->db->dbResultFetchAll($statement);
        } catch (\Throwable $e) {
            Debug::errorLog('BadgePositionsRepository::findPositions failed for badge ' . $badgeId . ': ' . $e->getMessage());

            return [];
        }
    }
}