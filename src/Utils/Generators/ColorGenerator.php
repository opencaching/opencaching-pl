<?php
namespace src\Utils\Generators;


class ColorGenerator
{
    // HTML colors - just strings supported by browsers
    public const HTML_PINK_MEDIUMVIOLETRED = 'MediumVioletRed';
    public const HTML_PINK_DEEPPINK = 'DeepPink';
    public const HTML_PINK_PALEVIOLETRED = 'PaleVioletRed';
    public const HTML_PINK_HOTPINK = 'HotPink';
    public const HTML_PINK_LIGHTPINK = 'LightPink';
    public const HTML_PINK_PINK = 'Pink';

    public const HTML_RED_DARKRED = 'DarkRed';
    public const HTML_RED_RED = 'Red';
    public const HTML_RED_FIREBRICK = 'Firebrick';
    public const HTML_RED_CRIMSON = 'Crimson';
    public const HTML_RED_INDIANRED = 'IndianRed';
    public const HTML_RED_LIGHTCORAL = 'LightCoral';
    public const HTML_RED_SALMON = 'Salmon';
    public const HTML_RED_DARKSALMON = 'DarkSalmon';
    public const HTML_RED_LIGHTSALMON = 'LightSalmon';

    public const HTML_ORANGE_ORANGERED = 'OrangeRed';
    public const HTML_ORANGE_TOMATO = 'Tomato';
    public const HTML_ORANGE_DARKORANGE = 'DarkOrange';
    public const HTML_ORANGE_CORAL = 'Coral';
    public const HTML_ORANGE_ORANGE = 'Orange';

    public const HTML_YELLOW_DARKKHAKI = 'DarkKhaki';
    public const HTML_YELLOW_GOLD = 'Gold';
    public const HTML_YELLOW_KHAKI = 'Khaki';
    public const HTML_YELLOW_PEACHPUFF = 'PeachPuff';
    public const HTML_YELLOW_YELLOW = 'Yellow';
    public const HTML_YELLOW_PALEGOLDENROD = 'PaleGoldenrod';
    public const HTML_YELLOW_MOCCASIN = 'Moccasin';
    public const HTML_YELLOW_PAPAYAWHIP = 'PapayaWhip';
    public const HTML_YELLOW_LIGHTGOLDENRODYELLOW = 'LightGoldenrodYellow';
    public const HTML_YELLOW_LEMONCHIFFON = 'LemonChiffon';
    public const HTML_YELLOW_LIGHTYELLOW = 'LightYellow';

    public const HTML_BROWN_MAROON = 'Maroon';
    public const HTML_BROWN_BROWN = 'Brown';
    public const HTML_BROWN_SADDLEBROWN = 'SaddleBrown';
    public const HTML_BROWN_SIENNA = 'Sienna';
    public const HTML_BROWN_CHOCOLATE = 'Chocolate';
    public const HTML_BROWN_DARKGOLDENROD = 'DarkGoldenrod';
    public const HTML_BROWN_PERU = 'Peru';
    public const HTML_BROWN_ROSYBROWN = 'RosyBrown';
    public const HTML_BROWN_GOLDENROD = 'Goldenrod';
    public const HTML_BROWN_SANDYBROWN = 'SandyBrown';
    public const HTML_BROWN_TAN = 'Tan';
    public const HTML_BROWN_BURLYWOOD = 'Burlywood';
    public const HTML_BROWN_WHEAT = 'Wheat';
    public const HTML_BROWN_NAVAJOWHITE = 'NavajoWhite';
    public const HTML_BROWN_BISQUE = 'Bisque';
    public const HTML_BROWN_BLANCHEDALMOND = 'BlanchedAlmond';
    public const HTML_BROWN_CORNSILK = 'Cornsilk';

    public const HTML_GREEN_DARKGREEN = 'DarkGreen';
    public const HTML_GREEN_GREEN = 'Green';
    public const HTML_GREEN_DARKOLIVEGREEN = 'DarkOliveGreen';
    public const HTML_GREEN_FORESTGREEN = 'ForestGreen';
    public const HTML_GREEN_SEAGREEN = 'SeaGreen';
    public const HTML_GREEN_OLIVE = 'Olive';
    public const HTML_GREEN_OLIVEDRAB = 'OliveDrab';
    public const HTML_GREEN_MEDIUMSEAGREEN = 'MediumSeaGreen';
    public const HTML_GREEN_LIMEGREEN = 'LimeGreen';
    public const HTML_GREEN_LIME = 'Lime';
    public const HTML_GREEN_SPRINGGREEN = 'SpringGreen';
    public const HTML_GREEN_MEDIUMSPRINGGREEN = 'MediumSpringGreen';
    public const HTML_GREEN_DARKSEAGREEN = 'DarkSeaGreen';
    public const HTML_GREEN_MEDIUMAQUAMARINE = 'MediumAquamarine';
    public const HTML_GREEN_YELLOWGREEN = 'YellowGreen';
    public const HTML_GREEN_LAWNGREEN = 'LawnGreen';
    public const HTML_GREEN_CHARTREUSE = 'Chartreuse';
    public const HTML_GREEN_LIGHTGREEN = 'LightGreen';
    public const HTML_GREEN_GREENYELLOW = 'GreenYellow';
    public const HTML_GREEN_PALEGREEN = 'PaleGreen';

    public const HTML_CYAN_TEAL = 'Teal';
    public const HTML_CYAN_DARKCYAN = 'DarkCyan';
    public const HTML_CYAN_LIGHTSEAGREEN = 'LightSeaGreen';
    public const HTML_CYAN_CADETBLUE = 'CadetBlue';
    public const HTML_CYAN_DARKTURQUOISE = 'DarkTurquoise';
    public const HTML_CYAN_MEDIUMTURQUOISE = 'MediumTurquoise';
    public const HTML_CYAN_TURQUOISE = 'Turquoise';
    public const HTML_CYAN_AQUA = 'Aqua';
    public const HTML_CYAN_CYAN = 'Cyan';
    public const HTML_CYAN_AQUAMARINE = 'Aquamarine';
    public const HTML_CYAN_PALETURQUOISE = 'PaleTurquoise';
    public const HTML_CYAN_LIGHTCYAN = 'LightCyan';

    public const HTML_BLUE_NAVY = 'Navy';
    public const HTML_BLUE_DARKBLUE = 'DarkBlue';
    public const HTML_BLUE_MEDIUMBLUE = 'MediumBlue';
    public const HTML_BLUE_BLUE = 'Blue';
    public const HTML_BLUE_MIDNIGHTBLUE = 'MidnightBlue';
    public const HTML_BLUE_ROYALBLUE = 'RoyalBlue';
    public const HTML_BLUE_STEELBLUE = 'SteelBlue';
    public const HTML_BLUE_DODGERBLUE = 'DodgerBlue';
    public const HTML_BLUE_DEEPSKYBLUE = 'DeepSkyBlue';
    public const HTML_BLUE_CORNFLOWERBLUE = 'CornflowerBlue';
    public const HTML_BLUE_SKYBLUE = 'SkyBlue';
    public const HTML_BLUE_LIGHTSKYBLUE = 'LightSkyBlue';
    public const HTML_BLUE_LIGHTSTEELBLUE = 'LightSteelBlue';
    public const HTML_BLUE_LIGHTBLUE = 'LightBlue';
    public const HTML_BLUE_POWDERBLUE = 'PowderBlue';

    public const HTML_PURPLE_INDIGO = 'Indigo';
    public const HTML_PURPLE_PURPLE = 'Purple';
    public const HTML_PURPLE_DARKMAGENTA = 'DarkMagenta';
    public const HTML_PURPLE_DARKVIOLET = 'DarkViolet';
    public const HTML_PURPLE_DARKSLATEBLUE = 'DarkSlateBlue';
    public const HTML_PURPLE_BLUEVIOLET = 'BlueViolet';
    public const HTML_PURPLE_DARKORCHID = 'DarkOrchid';
    public const HTML_PURPLE_FUCHSIA = 'Fuchsia';
    public const HTML_PURPLE_MAGENTA = 'Magenta';
    public const HTML_PURPLE_SLATEBLUE = 'SlateBlue';
    public const HTML_PURPLE_MEDIUMSLATEBLUE = 'MediumSlateBlue';
    public const HTML_PURPLE_MEDIUMORCHID = 'MediumOrchid';
    public const HTML_PURPLE_MEDIUMPURPLE = 'MediumPurple';
    public const HTML_PURPLE_ORCHID = 'Orchid';
    public const HTML_PURPLE_VIOLET = 'Violet';
    public const HTML_PURPLE_PLUM = 'Plum';
    public const HTML_PURPLE_THISTLE = 'Thistle';
    public const HTML_PURPLE_LAVENDER = 'Lavender';

    public const HTML_WHITE_MISTYROSE = 'MistyRose';
    public const HTML_WHITE_ANTIQUEWHITE = 'AntiqueWhite';
    public const HTML_WHITE_LINEN = 'Linen';
    public const HTML_WHITE_BEIGE = 'Beige';
    public const HTML_WHITE_WHITESMOKE = 'WhiteSmoke';
    public const HTML_WHITE_LAVENDERBLUSH = 'LavenderBlush';
    public const HTML_WHITE_OLDLACE = 'OldLace';
    public const HTML_WHITE_ALICEBLUE = 'AliceBlue';
    public const HTML_WHITE_SEASHELL = 'Seashell';
    public const HTML_WHITE_GHOSTWHITE = 'GhostWhite';
    public const HTML_WHITE_HONEYDEW = 'Honeydew';
    public const HTML_WHITE_FLORALWHITE = 'FloralWhite';
    public const HTML_WHITE_AZURE = 'Azure';
    public const HTML_WHITE_MINTCREAM = 'MintCream';
    public const HTML_WHITE_SNOW = 'Snow';
    public const HTML_WHITE_IVORY = 'Ivory';
    public const HTML_WHITE_WHITE = 'White';

    public const HTML_GREY_BLACK = 'Black';
    public const HTML_GREY_DARKSLATEGRAY = 'DarkSlateGray';
    public const HTML_GREY_DIMGRAY = 'DimGray';
    public const HTML_GREY_SLATEGRAY = 'SlateGray';
    public const HTML_GREY_GRAY = 'Gray';
    public const HTML_GREY_LIGHTSLATEGRAY = 'LightSlateGray';
    public const HTML_GREY_DARKGRAY = 'DarkGray';
    public const HTML_GREY_SILVER = 'Silver';
    public const HTML_GREY_LIGHTGRAY = 'LightGray';
    public const HTML_GREY_GAINSBORO = 'Gainsboro';

    // RGB values of HTML colors
    public const RGB_HTML_COLORS = [
        'MediumVioletRed' => [ 199, 21, 133 ],
        'DeepPink' => [ 255, 20, 147 ],
        'PaleVioletRed' => [ 219, 112, 147 ],
        'HotPink' => [ 255, 105, 180 ],
        'LightPink' => [ 255, 182, 193 ],
        'Pink' => [ 255, 192, 203 ],
        'DarkRed' => [ 139, 0, 0 ],

        'Red' => [ 255, 0, 0 ],
        'Firebrick' => [ 178, 34, 34 ],
        'Crimson' => [ 220, 20, 60 ],
        'IndianRed' => [ 205, 92, 92 ],
        'LightCoral' => [ 240, 128, 128 ],
        'Salmon' => [ 250, 128, 114 ],
        'DarkSalmon' => [ 233, 150, 122 ],
        'LightSalmon' => [ 255, 160, 122 ],

        'OrangeRed' => [ 255, 69, 0 ],
        'Tomato' => [ 255, 99, 71 ],
        'DarkOrange' => [ 255, 140, 0 ],
        'Coral' => [ 255, 127, 80 ],
        'Orange' => [ 255, 165, 0 ],

        'DarkKhaki' => [ 189, 183, 107 ],
        'Gold' => [ 255, 215, 0 ],
        'Khaki' => [ 240, 230, 140 ],
        'PeachPuff' => [ 255, 218, 185 ],
        'Yellow' => [ 255, 255, 0 ],
        'PaleGoldenrod' => [ 238, 232, 170 ],
        'Moccasin' => [ 255, 228, 181 ],
        'PapayaWhip' => [ 255, 239, 213 ],
        'LightGoldenrodYellow' => [ 250, 250, 210 ],
        'LemonChiffon' => [ 255, 250, 205 ],
        'LightYellow' => [ 255, 255, 224 ],

        'Maroon' => [ 128, 0, 0 ],
        'Brown' => [ 165, 42, 42 ],
        'SaddleBrown' => [ 139, 69, 19 ],
        'Sienna' => [ 160, 82, 45 ],
        'Chocolate' => [ 210, 105, 30 ],
        'DarkGoldenrod' => [ 184, 134, 11 ],
        'Peru' => [ 205, 133, 63 ],
        'RosyBrown' => [ 188, 143, 143 ],
        'Goldenrod' => [ 218, 165, 32 ],
        'SandyBrown' => [ 244, 164, 96 ],
        'Tan' => [ 210, 180, 140 ],
        'Burlywood' => [ 222, 184, 135 ],
        'Wheat' => [ 245, 222, 179 ],
        'NavajoWhite' => [ 255, 222, 173 ],
        'Bisque' => [ 255, 228, 196 ],
        'BlanchedAlmond' => [ 255, 235, 205 ],
        'Cornsilk' => [ 255, 248, 220 ],

        'DarkGreen' => [ 0, 100, 0 ],
        'Green' => [ 0, 128, 0 ],
        'DarkOliveGreen' => [ 85, 107, 47 ],
        'ForestGreen' => [ 34, 139, 34 ],
        'SeaGreen' => [ 46, 139, 87 ],
        'Olive' => [ 128, 128, 0 ],
        'OliveDrab' => [ 107, 142, 35 ],
        'MediumSeaGreen' => [ 60, 179, 113 ],
        'LimeGreen' => [ 50, 205, 50 ],
        'Lime' => [ 0, 255, 0 ],
        'SpringGreen' => [ 0, 255, 127 ],
        'MediumSpringGreen' => [ 0, 250, 154 ],
        'DarkSeaGreen' => [ 143, 188, 143 ],
        'MediumAquamarine' => [ 102, 205, 170 ],
        'YellowGreen' => [ 154, 205, 50 ],
        'LawnGreen' => [ 124, 252, 0 ],
        'Chartreuse' => [ 127, 255, 0 ],
        'LightGreen' => [ 144, 238, 144 ],
        'GreenYellow' => [ 173, 255, 47 ],
        'PaleGreen' => [ 152, 251, 152 ],

        'Teal' => [ 0, 128, 128 ],
        'DarkCyan' => [ 0, 139, 139 ],
        'LightSeaGreen' => [ 32, 178, 170 ],
        'CadetBlue' => [ 95, 158, 160 ],
        'DarkTurquoise' => [ 0, 206, 209 ],
        'MediumTurquoise' => [ 72, 209, 204 ],
        'Turquoise' => [ 64, 224, 208 ],
        'Aqua' => [ 0, 255, 255 ],
        'Cyan' => [ 0, 255, 255 ],
        'Aquamarine' => [ 127, 255, 212 ],
        'PaleTurquoise' => [ 175, 238, 238 ],
        'LightCyan' => [ 224, 255, 255 ],

        'Navy' => [ 0, 0, 128 ],
        'DarkBlue' => [ 0, 0, 139 ],
        'MediumBlue' => [ 0, 0, 205 ],
        'Blue' => [ 0, 0, 255 ],
        'MidnightBlue' => [ 25, 25, 112 ],
        'RoyalBlue' => [ 65, 105, 225 ],
        'SteelBlue' => [ 70, 130, 180 ],
        'DodgerBlue' => [ 30, 144, 255 ],
        'DeepSkyBlue' => [ 0, 191, 255 ],
        'CornflowerBlue' => [ 100, 149, 237 ],
        'SkyBlue' => [ 135, 206, 235 ],
        'LightSkyBlue' => [ 135, 206, 250 ],
        'LightSteelBlue' => [ 176, 196, 222 ],
        'LightBlue' => [ 173, 216, 230 ],
        'PowderBlue' => [ 176, 224, 230 ],

        'Indigo' => [ 75, 0, 130 ],
        'Purple' => [ 128, 0, 128 ],
        'DarkMagenta' => [ 139, 0, 139 ],
        'DarkViolet' => [ 148, 0, 211 ],
        'DarkSlateBlue' => [ 72, 61, 139 ],
        'BlueViolet' => [ 138, 43, 226 ],
        'DarkOrchid' => [ 153, 50, 204 ],
        'Fuchsia' => [ 255, 0, 255 ],
        'Magenta' => [ 255, 0, 255 ],
        'SlateBlue' => [ 106, 90, 205 ],
        'MediumSlateBlue' => [ 123, 104, 238 ],
        'MediumOrchid' => [ 186, 85, 211 ],
        'MediumPurple' => [ 147, 112, 219 ],
        'Orchid' => [ 218, 112, 214 ],
        'Violet' => [ 238, 130, 238 ],
        'Plum' => [ 221, 160, 221 ],
        'Thistle' => [ 216, 191, 216 ],
        'Lavender' => [ 230, 230, 250 ],

        'MistyRose' => [ 255, 228, 225 ],
        'AntiqueWhite' => [ 250, 235, 215 ],
        'Linen' => [ 250, 240, 230 ],
        'Beige' => [ 245, 245, 220 ],
        'WhiteSmoke' => [ 245, 245, 245 ],
        'LavenderBlush' => [ 255, 240, 245 ],
        'OldLace' => [ 253, 245, 230 ],
        'AliceBlue' => [ 240, 248, 255 ],
        'Seashell' => [ 255, 245, 238 ],
        'GhostWhite' => [ 248, 248, 255 ],
        'Honeydew' => [ 240, 255, 240 ],
        'FloralWhite' => [ 255, 250, 240 ],
        'Azure' => [ 240, 255, 255 ],
        'MintCream' => [ 245, 255, 250 ],
        'Snow' => [ 255, 250, 250 ],
        'Ivory' => [ 255, 255, 240 ],
        'White' => [ 255, 255, 255 ],

        'Black' => [ 0, 0, 0 ],
        'DarkSlateGray' => [ 47, 79, 79 ],
        'DimGray' => [ 105, 105, 105 ],
        'SlateGray' => [ 112, 128, 144 ],
        'Gray' => [ 128, 128, 128 ],
        'LightSlateGray' => [ 119, 136, 153 ],
        'DarkGray' => [ 169, 169, 169 ],
        'Silver' => [ 192, 192, 192 ],
        'LightGray' => [ 211, 211, 211 ],
        'Gainsboro' => [ 220, 220, 220 ],
    ];

    /**
     * Palette of nice colors to generate some random colors for charts etc.
     *
     * Its hard to find the "best pallette" see for example this:
     *  https://graphicdesign.stackexchange.com/questions/3682/where-can-i-find-a-large-palette-set-of-contrasting-colors-for-coloring-many-d
     */
    private const NICE_PALETTE = [
        self::HTML_BROWN_MAROON,
        self::HTML_BROWN_SIENNA,
        self::HTML_GREEN_OLIVE,
        self::HTML_CYAN_DARKCYAN,
        self::HTML_BLUE_NAVY,
        self::HTML_GREY_BLACK,
        self::HTML_RED_RED,
        self::HTML_ORANGE_DARKORANGE,
        self::HTML_YELLOW_YELLOW,
        self::HTML_GREEN_YELLOWGREEN,
        self::HTML_GREEN_GREEN,
        self::HTML_CYAN_CYAN,
        self::HTML_BLUE_ROYALBLUE,
        self::HTML_PURPLE_PURPLE,
        self::HTML_PURPLE_MAGENTA,
        self::HTML_GREY_SILVER,
        self::HTML_PINK_PINK,
        self::HTML_YELLOW_MOCCASIN,
        self::HTML_WHITE_BEIGE,
        self::HTML_GREEN_PALEGREEN,
        self::HTML_PURPLE_LAVENDER,
        self::HTML_WHITE_WHITE
    ];

    /**
     * Returns fully random color as hash string in format: #001122
     *
     * @return string
     */
    public static function trueRandomColor(): string
    {
        return sprintf("#%06X", mt_rand(0, 0xFFFFFF));
    }

    public static function rgb(string $htmlColorName): array
    {
        return self::RGB_HTML_COLORS[$htmlColorName] ?? [0,0,0];
    }

    public static function niceColorOf20(): string
    {
        return self::NICE_PALETTE[array_rand(self::NICE_PALETTE, 1)];
    }

    public static function niceSetLightBg($howManyColors):array
    {
        $pallete = self::NICE_PALETTE;
        $skipLights = [self::HTML_WHITE_WHITE, self::HTML_PURPLE_LAVENDER, self::HTML_WHITE_BEIGE];
        foreach ($skipLights as $col) {
            if (($key = array_search($col, $pallete)) !== false) {
                unset($pallete[$key]);
            }
        }
        $randKeys = array_rand($pallete, $howManyColors);
        $result = array_intersect_key($pallete, array_flip($randKeys));
        shuffle($result);
        return $result;
    }
}