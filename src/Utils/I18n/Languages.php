<?php

namespace src\Utils\I18n;

use src\Utils\Database\XDb;
use src\Utils\Debug\Debug;

/**
 * Handles operations on "languages" table.
 *
 * TODO: it should be refactored to translation files + config for lang defaults
 */
class Languages
{
    public static function isLanguageSupported($lang): bool
    {
        return 0 != XDb::xMultiVariableQueryValue(
            'SELECT COUNT(*) FROM languages
             WHERE short = :1 LIMIT 1',
            0,
            $lang
        );
    }

    public static function getLanguages($onlyDefaultList = null)
    {
        $currLang = I18n::getCurrentLang();

        $query = "SELECT `{$currLang}` AS localizedName,
                          short AS langCode,
                          `list_default_{$currLang}` AS defaultLang
                  FROM languages ";

        if ($onlyDefaultList) {
            $query .= "WHERE `list_default_{$currLang}` = 1 ";
        }
        $query .= "ORDER BY `{$currLang}` ASC";

        $rs = XDb::xSql($query);
        $result = [];

        while ($row = XDb::xFetchArray($rs)) {
            $result[] = $row;
        }

        return $result;
    }

    public static function languageNameFromCode($countryCode, $langCode)
    {
        $rs = XDb::xSql(
            "SELECT `short`, `{$langCode}` FROM `languages` WHERE `short`= ? ",
            $countryCode
        );

        return XDb::xFetchArray($rs)[$langCode] ?: false;
    }

    public static function setLocale($langCode)
    {
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
                if ($langCode !== CrowdinInContextMode::getPseudoLang()) {
                    Debug::errorLog("Error: trying to load unsupported locale: {$langCode}.");
                }

                setlocale(LC_CTYPE, 'en_EN');
                setlocale(LC_TIME, 'en_EN');
                break;
        }
    }

    public static function getCurrentLocale()
    {
        $currentLocale = '';

        switch (I18n::getCurrentLang()) {
            case 'pl':
                $currentLocale = 'pl_PL.UTF-8';
                break;
            case 'nl':
                $currentLocale = 'nl_NL.UTF-8';
                break;
            case 'fr':
                $currentLocale = 'fr_FR.UTF-8';
                break;
            case 'de':
                $currentLocale = 'de_DE.UTF-8';
                break;
            case 'sv':
                $currentLocale = 'sv_SV.UTF-8';
                break;
            case 'es':
                $currentLocale = 'es_ES.UTF-8';
                break;
            case 'cs':
                $currentLocale = 'cs_CS.UTF-8';
                break;
            case 'ro':
                $currentLocale = 'ro_RO.UTF-8';
                break;
            case 'hu':
                $currentLocale = 'hu_HU.UTF-8';
                break;
            case 'en':
                $currentLocale = 'en_EN';
                break;
            default:
                $currentLocale = 'en_EN';
                break;
        }

        return $currentLocale;
    }
}
