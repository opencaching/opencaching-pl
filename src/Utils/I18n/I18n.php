<?php

/**
 * Global nameespace here is only to define global function tr
 * used to translate phrases in OC code
 *
 * After refactoring all views it should be moved to View class.
 */
namespace {

    use src\Utils\I18n\I18n;

    /**
     * Return tranlated string
     *
     * @param string $str - translation key
     * @param array $args - arguments to insert into string (see vsprintf for details)
     * @return string - localized string
     */
    function tr($str, array $args = null)
    {
        if (is_null($args)) {
            return I18n::translatePhrase($str);
        } else {
            return vsprintf(I18n::translatePhrase($str), $args);
        }

    }

} // namespace / (global)

namespace src\Utils\I18n {

    use Exception;
    use src\Utils\Text\UserInputFilter;
    use src\Utils\Uri\OcCookie;
    use src\Utils\Uri\Uri;
    use src\Models\OcConfig\OcConfig;
    use src\Utils\Debug\Debug;
    use src\Utils\Database\XDb;
    use src\Utils\Cache\OcMemCache;

    class I18n
    {
        const FAILOVER_LANGUAGE = 'en';
        const COOKIE_LANG_VAR = 'lang';
        const URI_LANG_VAR = 'lang';

        private $currentLanguage;
        private $trArray;

        private function __construct()
        {
            $this->trArray = [];
        }

        /**
         * Returns instance of itself.
         *
         * @return I18n object
         */
        public static function instance()
        {
            static $instance = null;
            if ($instance === null) {
                $instance = new static();
                $instance->initialize();
            }

            return $instance;
        }

        private function initialize()
        {
            $initLang = $this->getInitLang();

            if (!$this->isLangSupported($initLang)) {
                $initLang = self::FAILOVER_LANGUAGE;
            }

            $this->setCurrentLang($initLang);
            $this->loadLangFile($initLang);
            Languages::setLocale($initLang);
        }


        /**
         * Retruns current language of tranlsations
         * @return string - two-char lang code - for example: 'pl' or 'en'
         */
        public static function getCurrentLang()
        {
            return self::instance()->currentLanguage;
        }

        /**
         * Returns default language for this node.
         * @return string - two-letters lang code
         */
        public static function getDefaultLang()
        {
            return OcConfig::getI18nDefaultLang();
        }

        /**
         * Return language which can be used to look up for translation in given Db table
         * DON'T USE DB TRANSLATION! This method is only to support legacy code.
         *
         * @param string $tableName
         * @return string - language code
         */
        public static function getLangForDbTranslations($tableName)
        {
            $langCode = self::getCurrentLang();
            if (XDb::xContainsColumn($tableName, $langCode)) {
                return $langCode;
            } else {
                return self::FAILOVER_LANGUAGE;
            }
        }

        /**
         * The only function to initilize I18n for OC code.
         * This should be called at the begining of every request.
         *
         * @return \src\Utils\I18n\I18n
         */
        public static function init()
        {
            // just be sure that instance of this class is created
            return self::instance();
        }

        /**
         * Main translate function
         *
         * @param string $translationId - id of the phrase
         * @param string $langCode - two-letter language code
         * @param boolean $skipPostprocess - if true skip "old-template" postprocessing
         * @return string -
         */
        public static function translatePhrase($translationId, $langCode=null, $skipPostprocess=false, $skipFailoverLang=false)
        {
            return self::instance()->translate($translationId, $langCode, $skipPostprocess, $skipFailoverLang);
        }

        /**
         * Allow to check if given phrase is present
         *
         * @param string $str - phrase to translate
         * @return boolean
         */
        public static function isTranslationAvailable($str)
        {
            $language = self::getCurrentLang();
            $instance = self::instance();
            return isset($instance->trArray[$language][$str]) && $instance->trArray[$language][$str];
        }

        public static function getLanguagesFlagsData(){
            $instance = self::instance();
            return $instance->getFlags();
        }

        private function getFlags($currentLang=null){

            $result = array();
            foreach ($this->getSupportedTranslations() as $language) {
                if (!isset($currentLang) || $language != $currentLang) {
                    $result[$language]['name'] = $language;
                    $result[$language]['img'] = '/images/flags/' . $language . '.svg';
                    $result[$language]['link'] = Uri::setOrReplaceParamValue(self::URI_LANG_VAR, $language);
                }
            }
            return $result;
        }

        /**
         * Returns language code which should be apply for current instance
         */
        private function getInitLang()
        {
            if (isset($_REQUEST['lang'])) {
                // language switch is requested
                $langToUse = $_REQUEST[self::URI_LANG_VAR];
            } else {
                // use previous lang or default
                $langToUse = OcCookie::getOrDefault(self::COOKIE_LANG_VAR, $this->getDefaultLang());
            }

            // check request for CrowdinInContext mode commands
            CrowdinInContextMode::checkRequest($langToUse);
            if (CrowdinInContextMode::enabled()){
                return CrowdinInContextMode::getPseudoLang();
            }

            return $langToUse;
        }

        private function translate($str, $langCode=null, $skipPostprocess=false, $skipFailoverLang=false)
        {
            if(!$langCode){
                $langCode = self::getCurrentLang();
            }

            if (!isset($this->trArray[$langCode])) {
                $this->loadLangFile($langCode);
            }

            if (isset($this->trArray[$langCode][$str]) && $this->trArray[$langCode][$str]) {
                // requested phrase found
                if (!$skipPostprocess) {
                    return $this->postProcessTr($this->trArray[$langCode][$str]);
                } else {
                    return $this->trArray[$langCode][$str];
                }
            } else {
                // there is no such phrase
                if(!$skipFailoverLang && $langCode != self::FAILOVER_LANGUAGE){
                    // try to handle it in failover language
                    return $this->translate($str, self::FAILOVER_LANGUAGE, $skipPostprocess);
                }
            }

            // ups - no such phrase at all - even in failover language...
            Debug::errorLog('Unknown translation for id: '.$str);
            return "No translation available (id: $str)";
        }

        private function postProcessTr(&$ref)
        {
            if (strpos($ref, "{") !== false) {
                if (!function_exists('tpl_do_replace')) {
                    throw new Exception(
                        // Desktop template system is not available on mobile pages.
                        '{} placeholders must not be used in mobile_ translation texts.'
                    );
                }
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
        private function loadLangFile($langCode)
        {
            // load selected language/translation file
            $languageFilename = self::languageFilename($langCode);
            include $languageFilename;
            $this->trArray[$langCode] = $translations;
        }

        private function languageFileTime($langCode)
        {
            $languageFilename = self::languageFilename($langCode);
            return filemtime($languageFilename);
        }

        private static function languageFilename($langCode)
        {
            $languageFilename = __DIR__ . "/../../../lib/languages/" . $langCode.'.php';
            if (!file_exists($languageFilename)) {
                throw new \Exception("Can't find translation file for requested language!");
            }
            return $languageFilename;
        }

        private function setCurrentLang($languageCode)
        {
            $this->currentLanguage = $languageCode;
            OcCookie::set(self::COOKIE_LANG_VAR, $languageCode, true);
        }

        /**
         * supported translations list is stored in i18n::$config['supportedLanguages'] var in config files
         * @return array of supported languags
         */
        private function getSupportedTranslations(){
            return OcConfig::getI18nSupportedLangs();
        }

        private function isLangSupported($langCode){

            if (CrowdinInContextMode::isSupportedInConfig()) {
                if ($langCode == CrowdinInContextMode::getPseudoLang() ){
                    return true;
                }
            }
            return in_array($langCode, $this->getSupportedTranslations());
        }


        /**
         * @param string $prefix
         * @return array dictionay of all translations that's keys start with the given prefix
         */
        public static function getPrefixedTranslationsWithFailover($prefix)
        {
            $cacheKey = 'translations-'.self::getCurrentLang().'-'.$prefix;

            $currentLangFiletime = self::languageFileTime(self::getCurrentLang());
            $failoverFiletime = self::languageFileTime(self::FAILOVER_LANGUAGE);

            $t = OcMemCache::get($cacheKey);

            if ($t !== false &&
                $t['failover-filetime'] == $failoverFiletime &&
                $t['main-filetime'] == $currentLangFiletime
            ) {
                // use translations from cache
                return $t['translations'];
            }

            // not in cache - compile new translation set
            $t = self::instance()->makePrefixedTranslationsWithFailover($prefix);

            OcMemCache::store(
                $cacheKey, 3600 * 24, [

                    // If you change anything here, also change the name of the cache key!
                    // Otherwise the old cached data could crash the changed implementation.

                    'failover-filetime' => $failoverFiletime,
                    'main-filetime' => $currentLangFiletime,
                    'translations' => $t
                ]
            );
            return $t;
        }

        private function makePrefixedTranslationsWithFailover($prefix)
        {
            if (!isset($this->trArray[self::FAILOVER_LANGUAGE])) {
                $this->loadLangFile(self::FAILOVER_LANGUAGE);
            }

            $prefixLen = strlen($prefix);
            $currentLanguage = self::getCurrentLang();
            $t = [];

            foreach ($this->trArray[self::FAILOVER_LANGUAGE] as $key => $text) {
                if (substr($key, 0, $prefixLen) == $prefix) {
                    if (isset($this->trArray[$currentLanguage][$key])) {
                        $t[substr($key, $prefixLen)] = $this->trArray[$currentLanguage][$key];
                    } else {
                        $t[substr($key, $prefixLen)] = $text;
                    }
                }
            }

            return $t;
        }


        /**
         * Methods for retrieving and maintaining old-style database translations.
         * This should become obsolete some time.
         * TODO: cache_atttrib
         */
        public static function getTranslationTables()
        {
            return [ 'cache_size', 'cache_status', 'cache_type', 'log_types', 'waypoint_type', 'languages' ];
        }

        public static function getTranslationIdColumnName($table)
        {
            if ($table == 'languages') {
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
                    throw new Exception('invalid size ID passed to getTranslationId(): '.$id);
                }
                $id = $sizeIds[$id - 1];
            };

            return $prefixes[$table] . $id;
        }
    }

} //namespace src\Utils\I18n
