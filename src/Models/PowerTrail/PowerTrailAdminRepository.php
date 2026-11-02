<?php

namespace src\Models\PowerTrail;

use src\Enums\PowerTrailAdminEnum;
use src\Utils\Database\OcDb;

class PowerTrailAdminRepository
{
    private readonly OcDb $db;

    /**
     * Create the repository with an optional database connection.
     *
     * @param OcDb|null $db Database connection; defaults to the shared application connection.
     */
    public function __construct(?OcDb $db = null)
    {
        $this->db = $db ?? OcDb::instance();
    }

    /**
     * Return all GeoPaths for the admin list, including unavailable ones.
     *
     * @param int $limit Maximum number of rows.
     * @param int $offset Number of rows to skip.
     * @param int $orderBy Sort option from {@see PowerTrailAdminEnum}.
     * @param int|null $id Optional GeoPath ID filter.
     * @param string|null $name Optional GeoPath name filter.
     * @return array<int, array<string, mixed>>
     */
    public function findAll(
        int $limit = PowerTrailAdminEnum::LIMIT,
        int $offset = 0,
        int $orderBy = PowerTrailAdminEnum::SORT_ID_DESC,
        ?int $id = null,
        ?string $name = null
    ): array
    {
        $limit = min(max(1, $limit), PowerTrailAdminEnum::LIMIT);
        $offset = max(0, $offset);
        [$whereSql, $parameters] = $this->buildFilters($id, $name);

        $query = 'SELECT `id`, ' . PowerTrailAdminEnum::DISPLAY_NAME_SQL . ' AS `name`, `type`, `status`,
                         `dateCreated`, `cacheCount`, `conquestedCount`
                  FROM `PowerTrail`' . $whereSql;

        $query .= ' ORDER BY ' . PowerTrailAdminEnum::sortMapper($orderBy)
            . ' LIMIT ' . $limit . ' OFFSET ' . $offset;

        if ($parameters) {
            $statement = $this->db->multiVariableQuery($query, ...$parameters);
        } else {
            $statement = $this->db->simpleQuery($query);
        }

        return $this->db->dbResultFetchAll($statement);
    }

    /**
     * Count GeoPaths matching the same optional filters used by findAll().
     *
     * @param int|null $id Optional GeoPath ID filter.
     * @param string|null $name Optional GeoPath name filter.
     * @return int
     */
    public function countAll(?int $id = null, ?string $name = null): int
    {
        [$whereSql, $parameters] = $this->buildFilters($id, $name);
        $query = 'SELECT COUNT(*) AS `total` FROM `PowerTrail`' . $whereSql;

        if ($parameters) {
            $statement = $this->db->multiVariableQuery($query, ...$parameters);
        } else {
            $statement = $this->db->simpleQuery($query);
        }

        $row = $this->db->dbResultFetch($statement);

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Return a GeoPath row, or null when it does not exist.
     *
     * @param int $id GeoPath ID.
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `id`, ' . PowerTrailAdminEnum::DISPLAY_NAME_SQL . ' AS `name`, `type`, `status`, `dateCreated`, `cacheCount`,
                    `description`, `image`, `perccentRequired`, `conquestedCount`, `points`
             FROM `PowerTrail`
             WHERE `id` = :1
             LIMIT 1',
            $id
        );

        $row = $this->db->dbResultFetch($statement);

        return is_array($row) ? $row : null;
    }

    /**
     * Return the caches currently assigned to a GeoPath.
     *
     * @param int $powerTrailId GeoPath ID.
     * @return array<int, array<string, mixed>>
     */
    public function findCaches(int $powerTrailId): array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT c.`cache_id`, c.`name`, c.`wp_oc`, c.`status`, c.`founds`,
                    u.`user_id`, u.`username`, ptc.`isFinal`
             FROM `powerTrail_caches` AS ptc
             JOIN `caches` AS c ON c.`cache_id` = ptc.`cacheId`
             LEFT JOIN `user` AS u ON u.`user_id` = c.`user_id`
             WHERE ptc.`PowerTrailId` = :1
             ORDER BY c.`name`, c.`cache_id`',
            $powerTrailId
        );

        return $this->db->dbResultFetchAll($statement);
    }

    /**
     * Return the founders (owners) of a GeoPath with their user names.
     *
     * @param int $powerTrailId GeoPath ID.
     * @return array<int, array<string, mixed>>
     */
    public function findFounders(int $powerTrailId): array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT u.`user_id`, u.`username`, po.`privileages`
             FROM `PowerTrail_owners` AS po
             JOIN `user` AS u ON u.`user_id` = po.`userId`
             WHERE po.`PowerTrailId` = :1
             ORDER BY u.`username`, u.`user_id`',
            $powerTrailId
        );

        return $this->db->dbResultFetchAll($statement);
    }

    /**
     * Return a single founder of a GeoPath.
     *
     * @param int $powerTrailId GeoPath ID.
     * @param int $userId User ID.
     * @return array<string, mixed>|null
     */
    public function findFounder(int $powerTrailId, int $userId): ?array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT u.`user_id`, u.`username`, po.`privileages`
             FROM `PowerTrail_owners` AS po
             JOIN `user` AS u ON u.`user_id` = po.`userId`
             WHERE po.`PowerTrailId` = :1 AND po.`userId` = :2
             LIMIT 1',
            $powerTrailId,
            $userId
        );
        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? $row : null;
    }

    /**
     * Find a user by the exact user name.
     *
     * The comparison is binary, so it does not use LIKE wildcards and is
     * case-sensitive.
     *
     * @param string $username Exact user name.
     * @return array<string, mixed>|null
     */
    public function findUserByUsername(string $username): ?array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `user_id`, `username`
             FROM `user`
             WHERE BINARY `username` = :1
             LIMIT 1',
            $username
        );
        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? $row : null;
    }

    /**
     * Check whether a user is already a founder of a GeoPath.
     *
     * @param int $powerTrailId GeoPath ID.
     * @param int $userId User ID.
     * @return bool
     */
    public function isFounder(int $powerTrailId, int $userId): bool
    {
        return $this->db->multiVariableQueryValue(
            'SELECT COUNT(*) FROM `PowerTrail_owners`
             WHERE `PowerTrailId` = :1 AND `userId` = :2',
            0,
            $powerTrailId,
            $userId
        ) > 0;
    }

    /**
     * Add a user as a founder of a GeoPath.
     *
     * @param int $powerTrailId GeoPath ID.
     * @param int $userId User ID.
     * @return bool True when the founder was added.
     */
    public function addFounder(int $powerTrailId, int $userId): bool
    {
        $statement = $this->db->multiVariableQuery(
            'INSERT IGNORE INTO `PowerTrail_owners`
                (`PowerTrailId`, `userId`, `privileages`)
             VALUES (:1, :2, 1)',
            $powerTrailId,
            $userId
        );

        return $this->db->rowCount($statement) > 0;
    }

    /**
     * Remove a founder from a GeoPath.
     *
     * @param int $powerTrailId GeoPath ID.
     * @param int $userId User ID.
     * @return bool True when the founder was removed.
     */
    public function removeFounder(int $powerTrailId, int $userId): bool
    {
        $statement = $this->db->multiVariableQuery(
            'DELETE FROM `PowerTrail_owners`
             WHERE `PowerTrailId` = :1 AND `userId` = :2
             LIMIT 1',
            $powerTrailId,
            $userId
        );

        return $this->db->rowCount($statement) > 0;
    }

    /**
     * Build the WHERE clause and bound parameters for the optional GeoPath filters.
     *
     * @param int|null $id Optional GeoPath ID filter.
     * @param string|null $name Optional GeoPath name filter.
     * @return array{0: string, 1: array<int, int|string>}
     */
    private function buildFilters(?int $id, ?string $name): array
    {
        $conditions = [];
        $parameters = [];

        if ($id !== null && $id > 0) {
            $parameters[] = $id;
            $conditions[] = '`id` = :' . count($parameters);
        }

        if ($name !== null && trim($name) !== '') {
            $parameters[] = '%' . trim($name) . '%';
            $conditions[] = PowerTrailAdminEnum::DISPLAY_NAME_SQL . ' LIKE :' . count($parameters);
        }

        $whereSql = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

        return [$whereSql, $parameters];
    }

    /**
     * Remove a cache assignment from a GeoPath.
     *
     * @param int $powerTrailId GeoPath ID.
     * @param int $cacheId Cache ID.
     * @return bool True when an assignment was removed.
     */
    public function removeCacheFromPowerTrail(int $powerTrailId, int $cacheId): bool
    {
        $statement = $this->db->multiVariableQuery(
            'DELETE FROM `powerTrail_caches`
             WHERE `PowerTrailId` = :1 AND `cacheId` = :2
             LIMIT 1',
            $powerTrailId,
            $cacheId
        );

        return $this->db->rowCount($statement) > 0;
    }

    /**
     * Check whether a GeoPath exists.
     *
     * @param int $powerTrailId GeoPath ID.
     * @return bool
     */
    public function exists(int $powerTrailId): bool
    {
        return $this->db->multiVariableQueryValue(
            'SELECT COUNT(*) FROM `PowerTrail` WHERE `id` = :1',
            0,
            $powerTrailId
        ) > 0;
    }

    /**
     * Return WIS stored in PowerTrail.perccentRequired.
     *
     * @param int $powerTrailId GeoPath ID.
     * @return int|null Null when the GeoPath does not exist.
     */
    public function findWis(int $powerTrailId): ?int
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `perccentRequired` FROM `PowerTrail` WHERE `id` = :1 LIMIT 1',
            $powerTrailId
        );
        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? (int) $row['perccentRequired'] : null;
    }

    /**
     * Update WIS stored in PowerTrail.perccentRequired.
     *
     * @param int $powerTrailId GeoPath ID.
     * @param int $wis New WIS value.
     * @return bool True when the value was changed.
     */
    public function updateWis(int $powerTrailId, int $wis): bool
    {
        $statement = $this->db->multiVariableQuery(
            'UPDATE `PowerTrail` SET `perccentRequired` = :1 WHERE `id` = :2',
            $wis,
            $powerTrailId
        );

        return $this->db->rowCount($statement) > 0;
    }

    /**
     * Update the GeoPath creation date stored in PowerTrail.dateCreated.
     *
     * @param int $powerTrailId GeoPath ID.
     * @param string $dateCreated New date and time in Y-m-d H:i:s format.
     * @return bool True when the value was changed.
     */
    public function updateDateCreated(int $powerTrailId, string $dateCreated): bool
    {
        $statement = $this->db->multiVariableQuery(
            'UPDATE `PowerTrail` SET `dateCreated` = :1 WHERE `id` = :2',
            $dateCreated,
            $powerTrailId
        );

        return $this->db->rowCount($statement) > 0;
    }

    /**
     * Update the GeoPath description stored in PowerTrail.description.
     *
     * @param int $powerTrailId GeoPath ID.
     * @param string $description Description already escaped for database storage.
     * @return bool True when the value was changed.
     */
    public function updateDescription(int $powerTrailId, string $description): bool
    {
        $statement = $this->db->multiVariableQuery(
            'UPDATE `PowerTrail` SET `description` = :1 WHERE `id` = :2',
            $description,
            $powerTrailId
        );

        return $this->db->rowCount($statement) > 0;
    }
}
