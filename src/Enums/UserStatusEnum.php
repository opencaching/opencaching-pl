<?php

declare(strict_types=1);

namespace src\Enums;

/**
 * User account statuses stored in `user`.`is_active_flag`.
 *
 * The backing values are compatible with the legacy UserCommons::STATUS_* constants.
 */
enum UserStatusEnum: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;
    case BANNED = 2;
    case REMOVED = 3;

    /**
     * English label, used as a fallback when the view has no translation.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::BANNED => 'Banned',
            self::REMOVED => 'Removed',
        };
    }

    /**
     * Short description of the account status.
     */
    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active user account',
            self::INACTIVE => 'Inactive user account',
            self::BANNED => 'Banned user account',
            self::REMOVED => 'Removed user account',
        };
    }

    /**
     * Translation key which can be used by the view: tr($status->translationKey()).
     */
    public function translationKey(): string
    {
        return match ($this) {
            self::ACTIVE => 'user_status_active',
            self::INACTIVE => 'user_status_inactive',
            self::BANNED => 'user_status_banned',
            self::REMOVED => 'user_status_removed',
        };
    }

    /**
     * @return array<int, int>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $status): int => $status->value,
            self::cases()
        );
    }

    /**
     * Status options for a select: database value => English label.
     *
     * @return array<int, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
