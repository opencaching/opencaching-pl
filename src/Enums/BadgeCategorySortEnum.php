<?php

declare(strict_types=1);

namespace src\Enums;

enum BadgeCategorySortEnum: string
{
    case ID_ASC = 'id_asc';
    case ID_DESC = 'id_desc';
    case NAME_ASC = 'name_asc';
    case NAME_DESC = 'name_desc';

    public function sqlOrder(): string
    {
        return match ($this) {
            self::ID_ASC => '`id` ASC',
            self::ID_DESC => '`id` DESC',
            self::NAME_ASC => '`name` ASC, `id` ASC',
            self::NAME_DESC => '`name` DESC, `id` ASC',
        };
    }

    public function translationKey(): string
    {
        return match ($this) {
            self::ID_ASC => 'sort_id_asc',
            self::ID_DESC => 'sort_id_desc',
            self::NAME_ASC => 'sort_name_asc',
            self::NAME_DESC => 'sort_name_desc',
        };
    }

    public static function fromValue(?string $value): self
    {
        return self::tryFrom((string) $value) ?? self::ID_ASC;
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $sort) {
            $options[$sort->value] = tr($sort->translationKey());
        }
        return $options;
    }
}
