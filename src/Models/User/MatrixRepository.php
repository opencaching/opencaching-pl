<?php

declare(strict_types=1);

namespace src\Models\User;

use src\Enums\LogTypeEnum;
use src\Utils\Database\OcDb;

/**
 * Database queries for the user cache Matrix.
 */
class MatrixRepository
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
            'SELECT c.terrain,
                    c.difficulty,
                    COUNT(DISTINCT cl.cache_id) AS cache_count
             FROM cache_logs cl
             JOIN caches c ON c.cache_id = cl.cache_id
             WHERE cl.user_id = :1
               AND cl.type IN (:2, :3)
               AND cl.deleted = 0
               AND c.difficulty BETWEEN 2 AND 10
               AND c.terrain BETWEEN 2 AND 10
             GROUP BY c.terrain, c.difficulty
             ORDER BY c.terrain, c.difficulty',
            $userId,
            LogTypeEnum::FOUND_IT->value,
            LogTypeEnum::ATTENDED->value
        );

        return $this->db->dbResultFetchAll($statement);
    }

    /**
     * Return found-cache counts grouped by year and month.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findYearMonthCounts(int $userId): array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT YEAR(cl.date) AS year,
                    MONTH(cl.date) AS month,
                    COUNT(DISTINCT cl.cache_id) AS cache_count
             FROM cache_logs cl
             WHERE cl.user_id = :1
               AND cl.type IN (:2, :3)
               AND cl.deleted = 0
               AND cl.date IS NOT NULL
             GROUP BY YEAR(cl.date), MONTH(cl.date)
             ORDER BY year, month',
            $userId,
            LogTypeEnum::FOUND_IT->value,
            LogTypeEnum::ATTENDED->value
        );

        return $this->db->dbResultFetchAll($statement);
    }

    /**
     * Return found-cache counts grouped by month and day.
     *
     * Only days with at least one found cache are returned. Missing days are
     * filled with zero or "X" by the service.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findMonthDayCounts(int $userId): array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT MONTH(cl.date) AS month,
                   DAY(cl.date)   AS day,
                   COUNT(DISTINCT cl.cache_id) AS cache_count
            FROM cache_logs cl
            WHERE cl.user_id = :1
              AND cl.type IN (:2, :3)
              AND cl.deleted = 0
              AND cl.date IS NOT NULL
            GROUP BY MONTH(cl.date), DAY(cl.date)
            ORDER BY month, day',
            $userId,
            LogTypeEnum::FOUND_IT->value,
            LogTypeEnum::ATTENDED->value
        );

        return $this->db->dbResultFetchAll($statement);
    }
}
