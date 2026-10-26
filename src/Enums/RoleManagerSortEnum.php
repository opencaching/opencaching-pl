<?php

declare(strict_types=1);

namespace src\Enums;

/**
 * Sort options available in the role management panel.
 *
 * The SQL fragments live here, next to the option they describe, so the
 * repository can stay focused on building and executing the query.
 */
enum RoleManagerSortEnum: string
{
    case USERNAME_ASC = 'username_asc';
    case USERNAME_DESC = 'username_desc';
    case USER_ID_ASC = 'user_id_asc';
    case USER_ID_DESC = 'user_id_desc';

    public function sqlOrder(): string
    {
        return match ($this) {
            self::USERNAME_ASC => '`username` ASC, `user_id` ASC',
            self::USERNAME_DESC => '`username` DESC, `user_id` ASC',
            self::USER_ID_ASC => '`user_id` ASC',
            self::USER_ID_DESC => '`user_id` DESC',
        };
    }

    public function translationKey(): string
    {
        return match ($this) {
            self::USERNAME_ASC => 'role_manager_sort_username_asc',
            self::USERNAME_DESC => 'role_manager_sort_username_desc',
            self::USER_ID_ASC => 'role_manager_sort_user_id_asc',
            self::USER_ID_DESC => 'role_manager_sort_user_id_desc',
        };
    }

    public static function fromValue(?string $value): self
    {
        return self::tryFrom((string) $value) ?? self::USERNAME_ASC;
    }

    /**
     * Sort options for a select: option value => translated label.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $sort) {
            $options[$sort->value] = tr($sort->translationKey());
        }

        return $options;
    }
}
