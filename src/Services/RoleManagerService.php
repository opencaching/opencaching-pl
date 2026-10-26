<?php

declare(strict_types=1);

namespace src\Services;

use InvalidArgumentException;
use RuntimeException;
use src\Enums\RoleManagerEnum;
use src\Enums\RoleManagerSortEnum;
use src\Models\ApplicationContainer;
use src\Models\User\User;
use src\Models\User\RoleManagerRepository;

/**
 * Business logic for the sysadmin role management panel.
 *
 * Read operations require a logged-in sysadmin. Operations which change roles
 * additionally require an HTTP POST request.
 *
 * All public methods of this service are sysadmin-only.
 *
 * Requires PHP >= 8.1 (enum, readonly properties, nullsafe operator).
 */
class RoleManagerService
{
    use RequiresSysAdminPostTrait;

    private readonly RoleManagerRepository $repository;

    public function __construct(?RoleManagerRepository $repository = null)
    {
        $this->repository = $repository ?? new RoleManagerRepository();
    }

    /**
     * Roles which can be assigned in the panel.
     *
     * @return array<int, array{value: string, label: string, description: string, bit: int, translationKey: string}>
     */
    public function getRoles(): array
    {
        $this->requireSysAdmin();

        $roles = [];

        foreach (RoleManagerEnum::cases() as $role) {
            $roles[] = [
                'value' => $role->value,
                'label' => tr($role->translationKey()),
                'description' => $role->description(),
                'bit' => $role->bit(),
                'translationKey' => $role->translationKey(),
            ];
        }

        return $roles;
    }

    /**
     * Role options for a select: database value => label.
     *
     * @return array<string, string>
     */
    public function getRoleOptions(): array
    {
        $this->requireSysAdmin();

        $options = [];

        foreach (RoleManagerEnum::cases() as $role) {
            $options[$role->value] = tr($role->translationKey());
        }

        return $options;
    }

    /**
     * Sort options for the user list: sort key => label.
     *
     * @return array<string, string>
     */
    public function getSortOptions(): array
    {
        $this->requireSysAdmin();

        return RoleManagerSortEnum::options();
    }

    /**
     * Return a page of users together with their roles.
     *
     * @param string|null $search matched against username and e-mail
     * @param string|null $role   RoleManagerEnum database value used as a filter,
     *                            or RoleManagerRepository::ROLE_FILTER_NONE to list users without a role
     * @param int         $page   1-based page number
     * @param string      $sort   one of RoleManagerSortEnum values
     * @param bool        $onlyWithRoles when true, users without any role are omitted
     *
     * @return array{
     *     items: array<int, array<string, mixed>>,
     *     total: int,
     *     page: int,
     *     limit: int,
     *     pages: int,
     *     offset: int
     * }
     *
     * @throws InvalidArgumentException when the role filter is not a known role
     */
    public function getUsers(
        ?string $search = null,
        ?string $role = null,
        int $page = 1,
        string $sort = RoleManagerSortEnum::USERNAME_ASC->value,
        bool $onlyWithRoles = true
    ): array {
        $this->requireSysAdmin();

        $withoutRole = $role === RoleManagerRepository::ROLE_FILTER_NONE;
        $roleFilter = null;

        if ($withoutRole) {
            $onlyWithRoles = false;
        } elseif ($role !== null && $role !== '') {
            $roleFilter = RoleManagerEnum::tryFrom($role);

            if ($roleFilter === null) {
                throw new InvalidArgumentException('Unknown role filter: ' . $role);
            }
        }

        $limit = RoleManagerEnum::DEFAULT_LIMIT;
        $page = max(1, $page);
        $offset = ($page - 1) * $limit;

        $total = $this->repository->countAll($search, $roleFilter, $onlyWithRoles, $withoutRole);
        $rows = $this->repository->findAll(
            $search,
            $roleFilter,
            $sort,
            $offset,
            $onlyWithRoles,
            $withoutRole
        );

        return [
            'items' => array_map(fn (array $row): array => $this->mapUser($row), $rows),
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => $total > 0 ? (int) ceil($total / $limit) : 0,
            'offset' => $offset,
        ];
    }

    /**
     * Replace all roles of a user.
     *
     * @param array<int, string> $roleValues RoleManagerEnum database values
     *
     * @return array{success: bool, code: string, message: string, user: array<string, mixed>|null}
     *
     * @throws RuntimeException 403 when the current user is not a sysadmin, 405 for non-POST requests
     */
    public function setRoles(int $userId, array $roleValues): array
    {
        $actor = $this->requireSysAdminPost();

        try {
            $roles = $this->normalizeRoles($roleValues);
        } catch (InvalidArgumentException $exception) {
            return $this->failure('invalid_role', $exception->getMessage());
        }

        $row = $this->repository->findById($userId);

        if ($row === null) {
            return $this->failure('user_not_found', 'User not found');
        }

        $currentRoles = RoleManagerEnum::fromDbList((string) ($row['role'] ?? ''));

        if ($this->removesSysAdmin($currentRoles, $roles)) {
            $protectionError = $this->checkSysAdminRemoval($actor, $userId);

            if ($protectionError !== null) {
                return $protectionError;
            }
        }

        $this->repository->setRoles($userId, $roles);

        return $this->success('roles_updated', 'Roles updated', $userId);
    }

    /**
     * Remove all roles from a user.
     *
     * If the target user is a sysadmin, the same protection as setRoles() applies:
     * a sysadmin cannot remove the role from their own account, and the last
     * sysadmin cannot lose it.
     *
     * @return array{success: bool, code: string, message: string, user: array<string, mixed>|null}
     *
     * @throws RuntimeException 403 when the current user is not a sysadmin, 405 for non-POST requests
     */
    public function removeAllRoles(int $userId): array
    {
        $actor = $this->requireSysAdminPost();

        $row = $this->repository->findById($userId);

        if ($row === null) {
            return $this->failure('user_not_found', 'User not found');
        }

        $currentRoles = RoleManagerEnum::fromDbList((string) ($row['role'] ?? ''));

        if (in_array(RoleManagerEnum::SYS_ADMIN, $currentRoles, true)) {
            $protectionError = $this->checkSysAdminRemoval($actor, $userId);

            if ($protectionError !== null) {
                return $protectionError;
            }
        }

        $this->repository->setRoles($userId, []);

        return $this->success('roles_removed', 'All roles removed', $userId);
    }

    /**
     * @param array<int, string> $roleValues
     *
     * @return array<int, RoleManagerEnum>
     *
     * @throws InvalidArgumentException when any role value is unknown
     */
    private function normalizeRoles(array $roleValues): array
    {
        return RoleManagerEnum::fromDbList(RoleManagerEnum::toDbList($roleValues));
    }

    /**
     * @param array<int, RoleManagerEnum> $currentRoles
     * @param array<int, RoleManagerEnum> $newRoles
     */
    private function removesSysAdmin(array $currentRoles, array $newRoles): bool
    {
        return in_array(RoleManagerEnum::SYS_ADMIN, $currentRoles, true)
            && ! in_array(RoleManagerEnum::SYS_ADMIN, $newRoles, true);
    }

    /**
     * @return array{success: bool, code: string, message: string, user: array<string, mixed>|null}|null
     */
    private function checkSysAdminRemoval(User $actor, int $targetUserId): ?array
    {
        if ($actor->getUserId() === $targetUserId) {
            return $this->failure(
                'cannot_remove_own_sysadmin_role',
                'You cannot remove the sysadmin role from your own account'
            );
        }

        if ($this->repository->countUsersWithRole(RoleManagerEnum::SYS_ADMIN) <= 1) {
            return $this->failure(
                'last_sysadmin',
                'The last sysadmin cannot lose this role'
            );
        }

        return null;
    }

    /**
     * @param array<string, mixed> $row
     *
     * @return array<string, mixed>
     */
    private function mapUser(array $row): array
    {
        $roles = RoleManagerEnum::fromDbList((string) ($row['role'] ?? ''));

        return [
            'userId' => (int) $row['user_id'],
            'username' => (string) ($row['username'] ?? ''),
            'email' => (string) ($row['email'] ?? ''),
            'isActive' => (int) ($row['is_active_flag'] ?? 0) === 1,
            'lastLogin' => $this->normalizeDate($row['last_login'] ?? null),
            'dateCreated' => $this->normalizeDate($row['date_created'] ?? null),
            'roles' => $roles,
            'roleValues' => array_map(
                static fn (RoleManagerEnum $role): string => $role->value,
                $roles
            ),
            'roleLabels' => array_map(
                static fn (RoleManagerEnum $role): string => $role->label(),
                $roles
            ),
        ];
    }

    private function normalizeDate(mixed $value): ?string
    {
        if (! is_string($value) || $value === '' || str_starts_with($value, '0000-00-00')) {
            return null;
        }

        return $value;
    }

    /**
     * @return array{success: true, code: string, message: string, user: array<string, mixed>|null}
     */
    private function success(string $code, string $message, int $userId): array
    {
        $row = $this->repository->findById($userId);

        return [
            'success' => true,
            'code' => $code,
            'message' => $message,
            'user' => $row !== null ? $this->mapUser($row) : null,
        ];
    }

    /**
     * @return array{success: false, code: string, message: string, user: null}
     */
    private function failure(string $code, string $message): array
    {
        return [
            'success' => false,
            'code' => $code,
            'message' => $message,
            'user' => null,
        ];
    }

    /**
     * @throws RuntimeException 403 when the current user is not a sysadmin
     */
    private function requireSysAdmin(): User
    {
        $user = ApplicationContainer::GetAuthorizedUser();

        if (! $user?->hasSysAdminRole()) {
            throw new RuntimeException('Sysadmin access required', 403);
        }

        return $user;
    }
}
