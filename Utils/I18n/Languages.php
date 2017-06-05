<?php
namespace Utils\I18n;

use Utils\Database\XDb;

/**
 *
 * Operations on "languages" table
 * TODO: it should be refactored to translation files + config for lang defaults
 *
 */

class Languages
{

    public static function LanguageNameFromCode($countryCode, $lang){

        $rs = XDb::xSql(
            "SELECT `short`, `$lang` FROM `languages` WHERE `short`= ? ", $countryCode);

        if ( $record = XDb::xFetchArray($rs) ) {
            return $record[$lang];
        } else {
            return false;
        }
    }


}

