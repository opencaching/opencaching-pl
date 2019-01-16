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

    public static function LanguageNameFromCode($countryCode, $langCode){

        $rs = XDb::xSql(
            "SELECT `short`, `$langCode` FROM `languages` WHERE `short`= ? ", $countryCode);

        if ( $record = XDb::xFetchArray($rs) ) {
            return $record[$langCode];
        } else {
            return false;
        }
    }

    public static function setLocale($langCode){
        switch ($langCode) {
            case 'pl':
                setlocale(LC_CTYPE, 'pl_PL.UTF-8');
                setlocale(LC_TIME, 'pl_PL.UTF-8');
                break;
            case 'nl':
                setlocale(LC_CTYPE, 'nl_NL.UTF-8');
                setlocale(LC_TIME, 'nl_NL.UTF-8');
                break;
            case 'fr':
                setlocale(LC_CTYPE, 'fr_FR.UTF-8');
                setlocale(LC_TIME, 'fr_FR.UTF-8');
                break;
            case 'de':
                setlocale(LC_CTYPE, 'de_DE.UTF-8');
                setlocale(LC_TIME, 'de_DE.UTF-8');
                break;
            case 'sv':
                setlocale(LC_CTYPE, 'sv_SV.UTF-8');
                setlocale(LC_TIME, 'sv_SV.UTF-8');
                break;
            case 'es':
                setlocale(LC_CTYPE, 'es_ES.UTF-8');
                setlocale(LC_TIME, 'es_ES.UTF-8');
                break;
            case 'cs':
                setlocale(LC_CTYPE, 'cs_CS.UTF-8');
                setlocale(LC_TIME, 'cs_CS.UTF-8');
                break;
            case 'ro':
                setlocale(LC_CTYPE, 'ro_RO.UTF-8');
                setlocale(LC_TIME, 'ro_RO.UTF-8');
                break;
            case 'hu':
                setlocale(LC_CTYPE, 'hu_HU.UTF-8');
                setlocale(LC_TIME, 'hu_HU.UTF-8');
                break;
            case 'en':
                setlocale(LC_CTYPE, 'en_EN');
                setlocale(LC_TIME, 'en_EN');
                break;
            default:
                error_log(__METHOD__.": Error: trying to load unsupported locale: $langCode !?");
                setlocale(LC_CTYPE, 'en_EN');
                setlocale(LC_TIME, 'en_EN');
                break;
        }
    }


}

