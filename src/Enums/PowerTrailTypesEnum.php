<?php

namespace src\Enums;

final class PowerTrailTypesEnum
{
    public const GEODRAW = 1;
    public const TOURING = 2;
    public const NATURE = 3;
    public const THEMATIC = 4;

    public static function types(): array
    {
        return [
            self::GEODRAW => [
                'translationKey' => 'cs_typeGeoDraw',
                'icon' => 'footprintRed.png',
            ],
            self::TOURING => [
                'translationKey' => 'cs_typeTouring',
                'icon' => 'footprintBlue.png',
            ],
            self::NATURE => [
                'translationKey' => 'cs_typeNature',
                'icon' => 'footprintGreen.png',
            ],
            self::THEMATIC => [
                'translationKey' => 'cs_typeThematic',
                'icon' => 'footprintYellow.png',
            ],
        ];
    }
}
