<?php

declare(strict_types=1);

namespace src\Models\User;

use src\Enums\RoleManagerEnum;
use src\Enums\RoleManagerSortEnum;
use src\Utils\Database\OcDb;

/**
 * Database access for the role management panel.
 *
 * The `user`.`role` column is a MySQL SET; roles are read and replaced as a
 * comma-separated list instead of using a separate join table.
 *
 * Requires PHP >= 8.1 (readonly properties).
 */
class RoleManagerRepository
{
    /**
     * Pseudo role used by the UI to list users without any role.
     */
    public const ROLE_FILTER_NONE = 'none';

    private readonly OcDb $db;

    public function __construct(?OcDb $db = null)
    {
        $this->db = $db ?? OcDb::instance();
    }

    /**
     * Return users matching the filters, with pagination.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(
        ?string $search = null,
        ?RoleManagerEnum $role = null,
        string $sort = RoleManagerSortEnum::USERNAME_ASC->value,
        int $offset = 0,
        bool $onlyWithRoles = false,
        bool $withoutRole = false
    ): array {
        $offset = max(0, $offset);

        [$whereSql, $parameters] = $this->buildFilters($search, $role, $onlyWithRoles, $withoutRole);

        $query = 'SELECT `user_id`, `username`, `email`, `role`, `is_active_flag`, `last_login`, `date_created`
                  FROM `user`' . $whereSql . '
                  ORDER BY ' . RoleManagerSortEnum::fromValue($sort)->sqlOrder() . '
                  LIMIT ' . RoleManagerEnum::DEFAULT_LIMIT . ' OFFSET ' . $offset;

        $statement = $parameters
            ? $this->db->multiVariableQuery($query, ...$parameters)
            : $this->db->simpleQuery($query);

        return $this->db->dbResultFetchAll($statement);
    }

    /**
     * Count users matching the same filters as findAll().
     */
    public function countAll(
        ?string $search = null,
        ?RoleManagerEnum $role = null,
        bool $onlyWithRoles = false,
        bool $withoutRole = false
    ): int {
        [$whereSql, $parameters] = $this->buildFilters($search, $role, $onlyWithRoles, $withoutRole);

        $query = 'SELECT COUNT(*) AS `total` FROM `user`' . $whereSql;

        $statement = $parameters
            ? $this->db->multiVariableQuery($query, ...$parameters)
            : $this->db->simpleQuery($query);

        return (int) $this->db->dbResultFetchOneRowOnly($statement)['total'];
    }

    /**
     * Return a single user with the raw `role` value.
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $userId): ?array
    {
        $statement = $this->db->multiVariableQuery(
            'SELECT `user_id`, `username`, `email`, `role`, `is_active_flag`, `last_login`, `date_created`
             FROM `user`
             WHERE `user_id` = :1
             LIMIT 1',
            $userId
        );

        $row = $this->db->dbResultFetchOneRowOnly($statement);

        return is_array($row) ? $row : null;
    }

    /**
     * Replace all roles of a user.
     *
     * @param array<int, RoleManagerEnum> $roles
     */
    public function setRoles(int $userId, array $roles): bool
    {
        $this->db->multiVariableQuery(
            'UPDATE `user` SET `role` = :1 WHERE `user_id` = :2',
            RoleManagerEnum::toDbList($roles),
            $userId
        );

        return true;
    }

    public function countUsersWithRole(RoleManagerEnum $role): int
    {
        return (int) $this->db->multiVariableQueryValue(
            'SELECT COUNT(*) FROM `user` WHERE FIND_IN_SET(:1, `role`) > 0',
            0,
            $role->value
        );
    }

    /**
     * @return array{0: string, 1: array<int, mixed>} SQL WHERE clause and its parameters
     */
    private function buildFilters(
        ?string $search,
        ?RoleManagerEnum $role,
        bool $onlyWithRoles,
        bool $withoutRole
    ): array {
        $conditions = [];
        $parameters = [];

        if ($withoutRole) {
            $conditions[] = "`role` = ''";
        } elseif ($onlyWithRoles) {
            $conditions[] = "`role` <> ''";
        }

        if (! $withoutRole && $role !== null) {
            $parameters[] = $role->value;
            $conditions[] = 'FIND_IN_SET(:' . count($parameters) . ', `role`) > 0';
        }

        $search = $search !== null ? trim($search) : '';

        if ($search !== '') {
            $parameters[] = '%' . $search . '%';
            $usernamePlaceholder = count($parameters);

            $parameters[] = '%' . $search . '%';
            $emailPlaceholder = count($parameters);

            $searchConditions = [
                '`username` LIKE :' . $usernamePlaceholder,
                '`email` LIKE :' . $emailPlaceholder,
            ];

            $conditions[] = '(' . implode(' OR ', $searchConditions) . ')';
        }

        $whereSql = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

        return [$whereSql, $parameters];
    }
}
