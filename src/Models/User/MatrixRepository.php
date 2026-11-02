<?php

declare(strict_types=1);

namespace src\Models\User;

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
               AND cl.type = 1
               AND cl.deleted = 0
               AND c.difficulty BETWEEN 2 AND 10
               AND c.terrain BETWEEN 2 AND 10
             GROUP BY c.terrain, c.difficulty
             ORDER BY c.terrain, c.difficulty',
            $userId
        );

        return $this->db->dbResultFetchAll($statement);
    }

    /**
     * Return found-cache counts for every day of a leap year.
     *
     * The recursive query supplies all 366 days, so days without finds are
     * returned with a count of zero. Invalid calendar days for a month are
     * not returned and are represented as "X" by the service.
     *
     * The year 2024 is used as the base because it is a leap year, which
     * makes it easier to calculate findings for February 29.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findMonthDayCounts(int $userId): array
    {
        $statement = $this->db->multiVariableQuery(
            'WITH RECURSIVE days AS (
                SELECT DATE \'2024-01-01\' AS d
                UNION ALL
                SELECT d + INTERVAL 1 DAY FROM days WHERE d < DATE \'2024-12-31\'
            )
            SELECT MONTH(days.d) AS month,
                   DAY(days.d)   AS day,
                   COALESCE(f.cache_count, 0) AS cache_count
            FROM days
            LEFT JOIN (
                SELECT MONTH(cl.date) AS month,
                       DAY(cl.date)   AS day,
                       COUNT(DISTINCT cl.cache_id) AS cache_count
                FROM cache_logs cl
                WHERE cl.user_id = :1
                  AND cl.type = 1
                  AND cl.deleted = 0
                  AND cl.date IS NOT NULL
                GROUP BY MONTH(cl.date), DAY(cl.date)
            ) f ON f.month = MONTH(days.d)
               AND f.day   = DAY(days.d)
            ORDER BY days.d',
            $userId
        );

        return $this->db->dbResultFetchAll($statement);
    }
}
