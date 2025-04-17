<?php

namespace src\Libs\CalendarButtons;

use src\Utils\Uri\Uri;

class CalendarButtonAssets
{
    public const CSS_PATH = '/js/libs/add-to-calendar-button/assets/css/atcb.min.css';
    public const JS_PATH = '/js/libs/add-to-calendar-button/dist/atcb.min.js';

    public static function getCss(): string
    {
        return Uri::getLinkWithModificationTime(self::CSS_PATH);
    }

    public static function getJs(): string
    {
        return Uri::getLinkWithModificationTime(self::JS_PATH);
    }
}
