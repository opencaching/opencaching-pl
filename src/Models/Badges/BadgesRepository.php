<?php

declare(strict_types=1);

namespace src\Models\Badges;

use src\Utils\Database\OcDb;
use src\Utils\Debug\Debug;

/**
 * Database access for badges.
 */
class BadgesRepository
{
    private readonly OcDb $db;

    public function __construct(?OcDb $db = null)
    {
        $this->db = $db ?? OcDb::instance();
    }

    /**
     * Read all badge categories in their configured order.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAllCategories(): array
    {
        $statement = $this->db->simpleQuery(
            'SELECT `id`, `name`
             FROM `badge_categories`
             WHERE `active` = 1
             ORDER BY `sequence` ASC'
        );

        return $this->db->dbResultFetchAll($statement);
    }

    /**
     * Read available description languages for a badge.
     *
     * @return array<int, string>
     */
    public function findAvailableDescriptionLanguages(int $badgeId): array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `language` FROM `badges_description` WHERE `id_badge` = :1',
            $badgeId
        );

        $rows = $this->db->dbResultFetchAll($statement);
        $langs = [];

        foreach ($rows as $row) {
            $langs[] = (string) $row['language'];
        }

        return $langs;
    }

    /**
     * Read a badge description for a specific language.
     *
     * @return array<string, mixed>|null
     */
    public function findDescription(int $badgeId, string $language): ?array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT * FROM `badges_description` WHERE `id_badge` = :1 AND `language` = :2 LIMIT 1',
            $badgeId,
            $language
        );

        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? $row : null;
    }

    /**
     * Read the main picture of a badge.
     *
     * @param int $badgeId
     * @return string|null
     */
    public function findBadgePicture(int $badgeId): ?string
    {
        $badge = $this->findBadgeById($badgeId);

        if (!is_array($badge) || empty($badge['picture'])) {
            return null;
        }

        return (string) $badge['picture'];
    }

    /**
     * Read pictures from badge levels (raw, ordered by level ascending).
     *
     * @return array<int, string>
     */
    public function findBadgeLevelPictures(int $badgeId): array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `picture`
             FROM `badge_levels`
             WHERE `badge_id` = :1
               AND `picture` IS NOT NULL
               AND `picture` <> \'\'
             ORDER BY `level` ASC',
            $badgeId
        );

        $rows = $this->db->dbResultFetchAll($statement);

        $pictures = [];
        foreach ($rows as $row) {
            $pictures[] = (string) $row['picture'];
        }

        return $pictures;
    }

    /**
     * Read a single badge by its id.
     *
     * @return array<string, mixed>|null
     */
    public function findBadgeById(int $badgeId): ?array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `id`, `name`, `long_name`, `category_id`, `sequence`,
                    `picture`, `trigger_type`, `short_description`, `description`,
                    `cfg_period_threshold`, `cfg_show_positions`,
                    `active`, `visible_in_map`, `display_restricted`,
                    `graphic_author`, `description_author`, `attendant`,
                    `belonging_query`, `gained_query`,
                    `count_query`, `recommended_query`, `titled_query`,
                    `show_positions_list`, `show_positions_map`, `level_presentation_mode`
             FROM `badges`
             WHERE `id` = :1
             LIMIT 1',
            $badgeId
        );

        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? $row : null;
    }

    /**
     * Read distinct owner ids of a badge.
     *
     * One person may hold several owner roles (keeper, description author,
     * graphic author), so DISTINCT is used to avoid duplicates.
     *
     * @return array<int, int>
     */
    public function findDistinctOwnerIds(int $badgeId): array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT DISTINCT `id_owner`
             FROM `badges_owners`
             WHERE `id_badge` = :1',
            $badgeId
        );

        $rows = $this->db->dbResultFetchAll($statement);
        $ids = [];

        foreach ($rows as $row) {
            $ids[] = (int) $row['id_owner'];
        }

        return $ids;
    }

    /**
     * Check whether the given user is an owner of the badge.
     */
    public function isOwner(int $badgeId, int $userId): bool
    {
        return in_array($userId, $this->findDistinctOwnerIds($badgeId), true);
    }

    /**
     * Read badge owners (raw rows joined with user and owner type).
     *
     * Returns one row per (badge, owner, type) assignment, ordered by
     * owner type and position. Repository only reads data.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findBadgeOwners(int $badgeId): array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `bo`.`id_owner` AS `id_owner`,
                    `bo`.`id_owner_type` AS `id_owner_type`,
                    `bo`.`position` AS `position`,
                    `u`.`username` AS `username`,
                    `bot`.`translation_key` AS `translation_key`
             FROM `badges_owners` AS `bo`
             LEFT JOIN `user` AS `u` ON `u`.`user_id` = `bo`.`id_owner`
             LEFT JOIN `badges_owners_types` AS `bot` ON `bot`.`id` = `bo`.`id_owner_type`
             WHERE `bo`.`id_badge` = :1
             ORDER BY `bo`.`id_owner_type` ASC, `bo`.`position` ASC',
            $badgeId
        );

        return $this->db->dbResultFetchAll($statement);
    }

    /**
     * Read all cache positions that belong to a badge.
     *
     * `belonging_query` always returns cache ids (as `badge_position_id`).
     *
     * @return array<int, int>
     */
    public function findBadgePositions(int $badgeId): array
    {
        $badge = $this->findBadgeById($badgeId);
        if ($badge === null) {
            return [];
        }

        $query = trim($badge['belonging_query'] ?? '');
        if ($query === '' || strtolower($query) === 'all') {
            return [];
        }

        try {
            $statement = $this->db->simpleQuery($query);
            $rows = $this->db->dbResultFetchAll($statement);
        } catch (\Throwable $e) {
            Debug::errorLog('BadgesRepository::findBadgePositions failed for badge ' . $badgeId . ': ' . $e->getMessage());

            return [];
        }

        $ids = [];
        foreach ($rows as $row) {
            $ids[] = (int) ($row['badge_position_id'] ?? reset($row));
        }

        return array_values(array_unique($ids));
    }

    /**
     * Count active caches among the badge's positions.
     *
     * @param int $badgeId
     * @return int
     */
    public function countActiveBadgePositions(int $badgeId): int
    {
        $badge = $this->findBadgeById($badgeId);
        if ($badge === null) {
            return 0;
        }

        $query = trim($badge['count_query'] ?? '');
        if ($query === '' || strtolower($query) === 'all') {
            return 0;
        }

        try {
            $statement = $this->db->simpleQuery($query);
            $row = $this->db->dbResultFetchOneRowOnly($statement);
        } catch (\Throwable $e) {
            Debug::errorLog('BadgesRepository::countActiveBadgePositions failed for badge ' . $badgeId . ': ' . $e->getMessage());

            return 0;
        }

        if (is_array($row)) {
            if (isset($row['cnt'])) {
                return (int) $row['cnt'];
            }
            // Fallback: first column value.
            $first = reset($row);
            if (is_numeric($first)) {
                return (int) $first;
            }
        }

        return 0;
    }

    /**
     * Read caches gained by a user for a badge.
     *
     * `gained_query` always filters by `:1` (user id) and returns position rows.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findGainedPositions(int $badgeId, int $userId): array
    {
        $badge = $this->findBadgeById($badgeId);
        if ($badge === null) {
            return [];
        }

        $query = trim($badge['gained_query'] ?? '');
        if ($query === '' || strtolower($query) === 'all') {
            return [];
        }

        try {
            $statement = $this->db->multiVariableQuery($query, $userId);

            return $this->db->dbResultFetchAll($statement);
        } catch (\Throwable $e) {
            Debug::errorLog('BadgesRepository::findGainedPositions failed for badge ' . $badgeId . ': ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Read levels of a badge, sorted by level ascending.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findLevels(int $badgeId): array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `badge_levels`.`level` AS `level`,
                    `badge_levels`.`name` AS `name`,
                    `badge_levels`.`threshold` AS `threshold`,
                    `badge_levels`.`picture` AS `picture`,
                    `gain`.`counter` AS `gain_counter`,
                    `gain`.`last_date` AS `gain_last_date`
             FROM `badge_levels`
             LEFT OUTER JOIN (
                 SELECT `level_id`, COUNT(*) AS `counter`, MAX(`level_date`) AS `last_date`
                 FROM `badge_user`
                 WHERE `badge_id` = :1
                 GROUP BY `level_id`
             ) AS `gain` ON `gain`.`level_id` = `badge_levels`.`level`
             WHERE `badge_levels`.`badge_id` = :1
             ORDER BY `badge_levels`.`level` ASC',
            $badgeId
        );

        return $this->db->dbResultFetchAll($statement);
    }

    /**
     * Read recommended caches for a badge.
     *
     * Uses `recommended_query` (must return `cache_id` and `cache_name`).
     * Returns [] if the query is empty or fails (error is logged).
     *
     * @return array<int, array<string, mixed>>
     */
    public function findRecommendedCaches(int $badgeId): array
    {
        return $this->runBadgeQuery($badgeId, 'recommended_query', 'BadgesRepository::findRecommendedCaches');
    }

    /**
     * Read caches of the week for a badge.
     *
     * Uses `titled_query` (must return `cache_id` and `cache_name`).
     * Returns [] if the query is empty or fails (error is logged).
     *
     * @return array<int, array<string, mixed>>
     */
    public function findTitledCaches(int $badgeId): array
    {
        return $this->runBadgeQuery($badgeId, 'titled_query', 'BadgesRepository::findTitledCaches');
    }

    /**
     * Execute a badge SQL query stored in a column. Returns [] on empty/error.
     *
     * @return array<int, array<string, mixed>>
     */
    private function runBadgeQuery(int $badgeId, string $column, string $context): array
    {
        $badge = $this->findBadgeById($badgeId);
        if ($badge === null) {
            return [];
        }

        $query = trim($badge[$column] ?? '');
        if ($query === '' || strtolower($query) === 'all') {
            return [];
        }

        try {
            $statement = $this->db->simpleQuery($query);

            return $this->db->dbResultFetchAll($statement);
        } catch (\Throwable $e) {
            Debug::errorLog($context . ' failed for badge ' . $badgeId . ': ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Read active badges for a given category.
     *
     * Only badges with `active = 1` are returned. The sort direction is taken
     * from the category (`sort_desc`: 0 = ascending, 1 = descending).
     *
     * @return array<int, array<string, mixed>>
     */
    public function findBadgesByCategory(int $categoryId): array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `id`, `name`, `long_name`, `category_id`, `sequence`,
                    `picture`, `trigger_type`, `short_description`,
                    `cfg_period_threshold`, `cfg_show_positions`,
                    `active`, `visible_in_map`, `display_restricted`
             FROM `badges`
             WHERE `category_id` = :1
               AND `active` = 1
             ORDER BY `sequence` ASC',
            $categoryId
        );

        return $this->db->dbResultFetchAll($statement);
    }
}
