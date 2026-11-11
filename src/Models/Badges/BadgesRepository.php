<?php

declare(strict_types=1);

namespace src\Models\Badges;

use src\Utils\Database\OcDb;

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
                    `belonging_query`, `gained_query`
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

        $statement = $this->db->simpleQuery($query);
        $rows = $this->db->dbResultFetchAll($statement);

        $ids = [];
        foreach ($rows as $row) {
            $ids[] = (int) ($row['badge_position_id'] ?? reset($row));
        }

        return array_values(array_unique($ids));
    }

    /**
     * Count active caches among the badge's positions.
     *
     * @return int
     */
    public function countActiveBadgePositions(int $badgeId): int
    {
        $badge = $this->findBadgeById($badgeId);
        if ($badge === null) {
            return 0;
        }

        $query = trim($badge['belonging_query'] ?? '');
        if ($query === '' || strtolower($query) === 'all') {
            return 0;
        }

        $sql = 'SELECT COUNT(*) AS cnt FROM `caches` WHERE `cache_id` IN (' . $query . ') AND `status` = 1';
        $statement = $this->db->simpleQuery($sql);
        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return (int) ($row['cnt'] ?? 0);
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

        $statement = $this->db->multiVariableQuery($query, $userId);

        return $this->db->dbResultFetchAll($statement);
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
