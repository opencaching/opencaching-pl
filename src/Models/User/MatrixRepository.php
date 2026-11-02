<?php

declare(strict_types=1);

namespace src\Models\User;

use src\Utils\Database\OcDb;

/**
 * Database queries for the user cache Matrix.
 */
class MatrixRepository
{
    /**
     * @var OcDb
     */
    private readonly OcDb $db;

    /**
     * @param OcDb|null $db
     */
    public function __construct(?OcDb $db = null)
    {
        $this->db = $db ?? OcDb::instance();
    }

    /**
     * Return the number of distinct found caches for every difficulty/terrain pair.
     *
     * Difficulty and terrain values are stored as integers from 2 to 10,
     * where each step represents half a star.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findDifficultyTerrainCounts(int $userId): array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT c.difficulty,
                    COUNT(DISTINCT CASE WHEN c.terrain = 2 THEN cl.cache_id END) AS terrain_2,
                    COUNT(DISTINCT CASE WHEN c.terrain = 3 THEN cl.cache_id END) AS terrain_3,
                    COUNT(DISTINCT CASE WHEN c.terrain = 4 THEN cl.cache_id END) AS terrain_4,
                    COUNT(DISTINCT CASE WHEN c.terrain = 5 THEN cl.cache_id END) AS terrain_5,
                    COUNT(DISTINCT CASE WHEN c.terrain = 6 THEN cl.cache_id END) AS terrain_6,
                    COUNT(DISTINCT CASE WHEN c.terrain = 7 THEN cl.cache_id END) AS terrain_7,
                    COUNT(DISTINCT CASE WHEN c.terrain = 8 THEN cl.cache_id END) AS terrain_8,
                    COUNT(DISTINCT CASE WHEN c.terrain = 9 THEN cl.cache_id END) AS terrain_9,
                    COUNT(DISTINCT CASE WHEN c.terrain = 10 THEN cl.cache_id END) AS terrain_10
             FROM cache_logs cl
             JOIN caches c ON c.cache_id = cl.cache_id
             WHERE cl.user_id = :1
               AND cl.type = 1
               AND cl.deleted = 0
               AND c.difficulty BETWEEN 2 AND 10
               AND c.terrain BETWEEN 2 AND 10
             GROUP BY c.difficulty
             ORDER BY c.difficulty',
            $userId
        );

        return $this->db->dbResultFetchAll($statement);
    }
}
