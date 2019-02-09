<?php
namespace src\Models\Pictures;

use src\Models\BaseObject;
use Utils\I18n\I18n;

class Thumbnail extends BaseObject
{
    // Error paceholders
    const ERROR_404 = 'thumb404.gif';        // file not found
    const ERROR_INTERN = 'thumbintern.gif';  // internal error
    const ERROR_FORMAT ='thumbunknown.gif';  // unknown file format
    const EXTERN = 'thumbextern.gif';        // external image, no thumb available
    const SPOILER = 'thumbspoiler.gif';      // spoiler image

    public static function placeholderUri($placeholder)
    {
        $path = 'tpl/stdstyle/images/thumb/'.I18n::getCurrentLang().'/'.$placeholder;
        if (!file_exists($path)) {
            $path = 'tpl/stdstyle/images/thumb/en/'.$placeholder;
        }
        return self::OcConfig()->getAbsolute_server_URI() . $path;
    }
}
