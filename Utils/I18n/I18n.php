<?php
namespace Utils\I18n;

use Exception;
use Utils\Text\UserInputFilter;
use Utils\Uri\OcCookie;
use Utils\Uri\Uri;
use lib\Objects\OcConfig\OcConfig;
use Utils\Debug\Debug;

class I18n
{
    const FAILOVER_LANGUAGE = 'en';

    /**
     * Retruns current language of tranlsations
     * @return string - two-char lang code - for example: 'pl' or 'en'
     */
    public static function getCurrentLang()
    {
        global $lang;
        return $lang;
    }

    /**
     * Main initialization of translations - should be called for all the scipts
     */
    public static function initTranslations()
    {
        // language changed
        if (isset($_REQUEST['lang'])) {
            $requestedLang = $_REQUEST['lang'];
        } else {
            $requestedLang = OcCookie::getOrDefault('lang', I18n::getDefaultLang());
        }

        // check if $requestedLang is supported by node
        if (!self::isLangSupported($requestedLang)) {
            // requested language is not supported - display error...
            self::handleUnsupportedLangAndExit ($requestedLang);
        }

        CrowdinInContextMode::initHandler();
        if (CrowdinInContextMode::enabled()) {
            // CrowdinInContext mode is enabled => force loading crowdin "pseudo" lang
            $requestedLang = CrowdinInContextMode::getPseudoLang();
        }

        self::setLang($requestedLang);
        self::loadLangFile($requestedLang);
        Languages::setLocale($requestedLang);
    }

    public static function translatePhrase($str, $langCode)
    {
        global $language;

        if (!isset($language[$langCode])) {
            self::loadLangFile($langCode);
        }

        if (isset($language[$langCode][$str]) && $language[$langCode][$str]) {
            // requested phrase found
            return self::postProcessTr($language[$langCode][$str]);
        } else {
            if($langCode != self::FAILOVER_LANGUAGE){
                // there is no such phrase - try to handle it in failover language
                return self::translatePhrase($str, self::FAILOVER_LANGUAGE);
            }
        }

        // ups - no such phrase at all - even in failover language...
        Debug::errorLog('Unknown translation for id: '.$str);
        return "No translation available (id: $str)";
    }

    /**
     * Allow to check if given phrase is present
     *
     * @param string $str - phrase to translate
     * @return boolean
     */
    public static function isTranslationAvailable($str)
    {
        global $language;
        $language = self::getCurrentLang();

        return isset($language[$language][$str]) && $language[$language][$str];
    }

    private static function postProcessTr(&$ref)
    {
        if (strpos($ref, "{") !== false) {
            return tpl_do_replace($ref, true);
        } else {
            return $ref;
        }
    }

    /**
     * Load given translation file
     *
     * THIS METHOD SHOULD BE PRIVATE!
     *
     * @param string $langCode - two-letter language code
     */
    public static function loadLangFile($langCode)
    {
        global $language;

        $languageFilename = __DIR__ . "/../../lib/languages/" . $langCode.'.php';
        if (!file_exists($languageFilename)) {
            throw new \Exception("Can't find translation file for requested language!");
            return;
        }

        if (!is_array($language)){
            $language = [];
        }

        // load selected language/translation file
        include ($languageFilename);
        $language[$langCode] = $translations;
    }

    private static function setLang($languageCode)
    {
        global $lang;
        $lang = $languageCode;
    }

    /**
     * supported translations list is stored in i18n::$config['supportedLanguages'] var in config files
     * @return array of supported languags
     */
    public static function getSupportedTranslations(){
        return OcConfig::instance()->getI18Config()['supportedLanguages'];
    }

    public static function getDefaultLang()
    {
        return OcConfig::instance()->getI18Config()['defaultLang'];
    }

    private static function isLangSupported($lang){

        if (CrowdinInContextMode::isSupportedInConfig()) {
            if ($lang == CrowdinInContextMode::getPseudoLang() ){
                return true;
            }
        }
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
        foreach(self::getSupportedTranslations() as $language){
            if(!isset($currentLang) || $language != $currentLang){
                $result[$language]['name'] = $language;
                $result[$language]['img'] = '/images/flags/' . $language . '.svg';
                $result[$language]['link'] = Uri::setOrReplaceParamValue('lang',$language);
            }
        }
        return $result;
    }

    private static function handleUnsupportedLangAndExit($requestedLang)
    {
        tpl_set_tplname('error/langNotSupported');
        $view = tpl_getView();

        $view->loadJQuery();
        $view->setVar("localCss",
            Uri::getLinkWithModificationTime('/tpl/stdstyle/error/error.css'));
        $view->setVar('requestedLang', UserInputFilter::purifyHtmlString($requestedLang));

        self::setLang(self::FAILOVER_LANGUAGE);

        $view->setVar('allLanguageFlags', self::getLanguagesFlagsData());

        self::loadLangFile(self::FAILOVER_LANGUAGE);

        tpl_BuildTemplate();
        exit;
    }

    // Methods for retrieving and maintaining old-style database translations.
    // This should become obsolete some time.

    // TODO: cache_atttrib

    public static function getTranslationTables()
    {
        return [
            'cache_size', 'cache_status', 'cache_type', 'log_types', 'waypoint_type',
            'countries', 'languages'
        ];
    }

    public static function getTranslationIdColumnName($table)
    {
        if ($table == 'countries' || $table == 'languages') {
            return 'short';
        } elseif ($table == 'cache_type') {
            return 'sort';  // not 'id' !
        } elseif (in_array($table, self::getTranslationTables())) {
            return 'id';
        } else {
            throw new Exception("unknown table: '".$table."'");
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
            throw new Exception("unknown table: '".$table."'");
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
