<?php

declare(strict_types=1);

namespace src\Enums;

use InvalidArgumentException;

/**
 * User roles stored in `user`.`role` (MySQL SET column).
 *
 * The backing values are the exact strings used by the database. The bit values are
 * kept for compatibility with the legacy UserCommons::ROLE_* constants.
 */
enum RoleManagerEnum: string
{
    /**
     * Number of users displayed per page in the role management panel.
     */
    public const DEFAULT_LIMIT = 50;

    case OC_TEAM = 'ocTeamMember';
    case ADV_USER = 'advUser';
    case NEWS_PUBLISHER = 'newsPublisher';
    case SYS_ADMIN = 'sysAdmin';

    /**
     * Legacy bit flag used by UserCommons::ROLE_*.
     */
    public function bit(): int
    {
        return match ($this) {
            self::OC_TEAM => 1,
            self::ADV_USER => 2,
            self::NEWS_PUBLISHER => 4,
            self::SYS_ADMIN => 8,
        };
    }

    /**
     * English label, used as a fallback when the view has no translation.
     */
    public function label(): string
    {
        return match ($this) {
            self::OC_TEAM => 'OC Team member',
            self::ADV_USER => 'Advanced user',
            self::NEWS_PUBLISHER => 'News publisher',
            self::SYS_ADMIN => 'System administrator',
        };
    }

    /**
     * Short description of what the role gives access to.
     */
    public function description(): string
    {
        return match ($this) {
            self::OC_TEAM => 'Access to the OC Team admin area',
            self::ADV_USER => 'Access to advanced and beta features',
            self::NEWS_PUBLISHER => 'Can publish news',
            self::SYS_ADMIN => 'Full technical access, including the sysadmin panel',
        };
    }

    /**
     * Translation key which can be used by the view: tr($role->translationKey()).
     */
    public function translationKey(): string
    {
        return match ($this) {
            self::OC_TEAM => 'role_oc_team',
            self::ADV_USER => 'role_adv_user',
            self::NEWS_PUBLISHER => 'role_news_publisher',
            self::SYS_ADMIN => 'role_sys_admin',
        };
    }

    /**
     * Return the role represented by a legacy bit flag, or null when the flag is unknown.
     */
    public static function fromBit(int $bit): ?self
    {
        return match ($bit) {
            1 => self::OC_TEAM,
            2 => self::ADV_USER,
            4 => self::NEWS_PUBLISHER,
            8 => self::SYS_ADMIN,
            default => null,
        };
    }

    /**
     * Parse the database SET value, e.g. "ocTeamMember,sysAdmin".
     *
     * Unknown values are ignored, so a role removed from the DB definition does not
     * break the whole page.
     *
     * @return array<int, self>
     */
    public static function fromDbList(?string $roleList): array
    {
        if ($roleList === null || trim($roleList) === '') {
            return [];
        }

        $roles = [];

        foreach (explode(',', $roleList) as $value) {
            $role = self::tryFrom(trim($value));

            if ($role !== null) {
                $roles[$role->value] = $role;
            }
        }

        return self::sortRoles(array_values($roles));
    }

    /**
     * Convert roles to the database SET value, e.g. "ocTeamMember,sysAdmin".
     *
     * Duplicates are removed and roles are ordered by their legacy bit flag.
     *
     * @param array<int, self|string> $roles
     *
     * @throws InvalidArgumentException when a role value is unknown
     */
    public static function toDbList(array $roles): string
    {
        $normalized = [];

        foreach ($roles as $role) {
            if ($role instanceof self) {
                $normalized[$role->value] = $role;

                continue;
            }

            if (! is_string($role) || ($roleEnum = self::tryFrom($role)) === null) {
                $roleName = is_scalar($role) ? (string) $role : get_debug_type($role);

                throw new InvalidArgumentException('Unknown user role: ' . $roleName);
            }

            $normalized[$roleEnum->value] = $roleEnum;
        }

        return implode(',', array_map(
            static fn (self $role): string => $role->value,
            self::sortRoles(array_values($normalized))
        ));
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $role): string => $role->value,
            self::cases()
        );
    }

    /**
     * Role options for a select: database value => English label.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $role) {
            $options[$role->value] = $role->label();
        }

        return $options;
    }

    /**
     * @param array<int, self> $roles
     *
     * @return array<int, self>
     */
    private static function sortRoles(array $roles): array
    {
        usort(
            $roles,
            static fn (self $first, self $second): int => $first->bit() <=> $second->bit()
        );

        return $roles;
    }
}
