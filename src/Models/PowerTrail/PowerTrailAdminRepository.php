<?php

namespace src\Models\PowerTrail;

use src\Enums\PowerTrailAdminEnum;
use src\Utils\Database\OcDb;

class PowerTrailAdminRepository
{
    private readonly OcDb $db;

    public function __construct(?OcDb $db = null)
    {
        $this->db = $db ?? OcDb::instance();
    }

    /**
     * Return all GeoPaths for the admin list, including unavailable ones.
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
     * @param int|null $id
     * @param string|null $name
     * @return array
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
     * @param int $powerTrailId
     * @param int $cacheId
     * @return bool
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

    public function exists(int $powerTrailId): bool
    {
        return $this->db->multiVariableQueryValue(
            'SELECT COUNT(*) FROM `PowerTrail` WHERE `id` = :1',
            0,
            $powerTrailId
        ) > 0;
    }

    /** Return the complete PowerTrail row. */
    public function findRowById(int $powerTrailId): ?array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT * FROM `PowerTrail` WHERE `id` = :1 LIMIT 1',
            $powerTrailId
        );
        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? $row : null;
    }

    public function findDescription(int $powerTrailId): ?string
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `description` FROM `PowerTrail` WHERE `id` = :1 LIMIT 1',
            $powerTrailId
        );
        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? $row['description'] : null;
    }

    public function updateDescription(int $powerTrailId, string $description): void
    {
        $this->db->multiVariableQuery(
            'UPDATE `PowerTrail` SET `description` = :1 WHERE `id` = :2',
            $description,
            $powerTrailId
        );
    }

    public function findStatus(int $powerTrailId): ?int
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `status` FROM `PowerTrail` WHERE `id` = :1 LIMIT 1',
            $powerTrailId
        );
        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? (int) $row['status'] : null;
    }

    /** WIS is stored in PowerTrail.perccentRequired. */
    public function findWis(int $powerTrailId): ?int
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `perccentRequired` FROM `PowerTrail` WHERE `id` = :1 LIMIT 1',
            $powerTrailId
        );
        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? (int) $row['perccentRequired'] : null;
    }

    public function updateWis(int $powerTrailId, int $wis): bool
    {
        $statement = $this->db->multiVariableQuery(
            'UPDATE `PowerTrail` SET `perccentRequired` = :1 WHERE `id` = :2',
            $wis,
            $powerTrailId
        );

        return $this->db->rowCount($statement) > 0;
    }

    /** Return all users assigned as GeoPath founders/owners (not the mentor). */
    public function findFounders(int $powerTrailId): array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT u.`user_id`, u.`username`, u.`email`, pto.`privileages`
             FROM `PowerTrail_owners` AS pto
             JOIN `user` AS u ON u.`user_id` = pto.`userId`
             WHERE pto.`PowerTrailId` = :1
             ORDER BY u.`username`, u.`user_id`',
            $powerTrailId
        );

        return $this->db->dbResultFetchAll($statement);
    }

    public function countFounders(int $powerTrailId): int
    {
        return (int) $this->db->multiVariableQueryValue(
            'SELECT COUNT(*) FROM `PowerTrail_owners` WHERE `PowerTrailId` = :1',
            0,
            $powerTrailId
        );
    }

    public function userExists(int $userId): bool
    {
        return $this->db->multiVariableQueryValue(
            'SELECT COUNT(*) FROM `user` WHERE `user_id` = :1',
            0,
            $userId
        ) > 0;
    }

    public function addFounder(int $powerTrailId, int $userId): bool
    {
        $statement = $this->db->multiVariableQuery(
            'INSERT IGNORE INTO `PowerTrail_owners` (`PowerTrailId`, `userId`, `privileages`)
             VALUES (:1, :2, 1)',
            $powerTrailId,
            $userId
        );

        return $this->db->rowCount($statement) > 0;
    }

    public function removeFounder(int $powerTrailId, int $userId): bool
    {
        $statement = $this->db->multiVariableQuery(
            'DELETE FROM `PowerTrail_owners` WHERE `PowerTrailId` = :1 AND `userId` = :2',
            $powerTrailId,
            $userId
        );

        return $this->db->rowCount($statement) > 0;
    }
}
