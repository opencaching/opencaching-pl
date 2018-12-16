<?php
namespace Utils\I18n;

use Exception;
use Utils\Uri\Uri;

class I18n
{

    /**
     * supported translations list is stored in config['supportedLanguages'] var in settings* files
     * @return array of supported tranlation in form: 'PL', 'EN', 'NL', 'RO'
     */
    public static function getSupportedTranslations(){

        if(isset($GLOBALS['config']['supportedLanguages']) && is_array($GLOBALS['config']['supportedLanguages'])){
            return $GLOBALS['config']['supportedLanguages'];
        } else {
            error_log(__METHOD__.': There is no $config[supportedLanguages] settings -'.
                        'please load settingsDefault.inc.php in your setting.inc.php!');
            return array('en', 'nl', 'pl', 'ro');
        }
    }

    public static function isTranslationSupported($lang){
        return in_array($lang, self::getSupportedTranslations());
    }

    /**
     * Returns locale for given language in format: <xx_YY>,
     * where
     * - xx: 2-letter language code (list of lang codes: http://www.loc.gov/standards/iso639-2/php/code_list.php)
     * - YY: 2-letter country code (list of countries codes: https://en.wikipedia.org/wiki/ISO_3166-1)
     *
     * For example for 'pl': defaults is pl_PL
     *
     * @param string $lang
     * @param string $tie - sometimes '-' is needed instead of '_'
     *
     * @return string
     */
    public static function getLocaleCode($lang, $tie='_')
    {
        switch($lang){
            case 'pl': return "pl$tiePL";
            case 'en': return "en$tieGB";
            case 'nl': return "nl$tieNL";
            case 'ro': return "ro$tieRO";
            default:
                return "en$tieGB";
        }
    }

    public static function getLanguagesFlagsData($currentLang=null){

        $result = array();
        foreach(self::getSupportedTranslations() as $lang){
            if(!isset($currentLang) || $lang != $currentLang){
                $result[$lang]['name'] = $lang;
                $result[$lang]['img'] = '/images/flags/' . $lang . '.svg';
                $result[$lang]['link'] = Uri::setOrReplaceParamValue('lang',$lang);
            }
        }
        return $result;
    }

    // Helper methods for retrieving and maintaining old-style database
    // translations. This should become obsolete some time.

    // TODO: cache_atttrib

    public static function getTranslationTables()
    {
        return [
            'cache_size', 'cache_status', 'cache_type', 'log_types', 'waypoint_type', 
            'countries', 'languages'
        ];
    }

    public static function getIdColumnName($table)
    {
        if ($table == 'countries' || $table == 'languages') {
            return 'short';
        } elseif ($table == 'cache_type') {
            return 'sort';  // !!
        } elseif (in_array($table, self::getTranslationTables())) {
            return 'id';
        } else {
            throw new Exception('unknown table in getTranslationColumnName(): '.$table);
        }
    }

    public static function getTranslationKey($table, $id)
    {
        static $prefixes;

        if (!$prefixes) {
            $prefixes = [
                'cache_size' => 'cacheSize_',
                'cache_status' => 'cacheStatus_',
                'cache_type' => 'cacheType_',
                'countries' => '',
                'languages' => 'language_',
                'log_types' => 'logType',
                'waypoint_type' => 'wayPointType'
            ];
        }
        if (!isset($prefixes[$table])) {
            throw new Exception('unknown table in getTranslationId(): '.$table);
        }

        if ($table == 'cache_size') {
            static $sizeIds;
            if (!$sizeIds) {
                $sizeIds = ['other', 'micro', 'small', 'regular', 'large', 'xLarge', 'none', 'nano'];
            }
            if ($id < 1 || $id > count($sizeIds)) {
                throw new Exception('invalid size ID passed to getTranslationId(): '.$size);
            }
            $id = $sizeIds[$id - 1];
        };

        return $prefixes[$table] . $id;
    }
}
