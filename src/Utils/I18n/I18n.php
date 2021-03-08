<?php

/**
 * Global namespace is here to define the global tr() function
 * used to translate phrases in OC code.
 *
 * After refactoring all views it should be moved to the View class.
 */
namespace
{
    use src\Utils\I18n\I18n;

    /**
     * Translate the given message.
     *
     * @param string $key The message key in /lib/languages/*.php
     * @param array|null $replace Replacements to be made using vsprintf()
     */
    function tr(string $key, array $replace = null): string
    {
        if (! $replace) {
            return I18n::translatePhrase($key);
        }

        return vsprintf(I18n::translatePhrase($key), $replace);
    }
}

namespace src\Utils\I18n
{
    use Exception;
    use src\Models\OcConfig\OcConfig;
    use src\Utils\Cache\OcMemCache;
    use src\Utils\Database\XDb;
    use src\Utils\Debug\Debug;
    use src\Utils\Uri\OcCookie;
    use src\Utils\Uri\Uri;

    final class I18n
    {
        const FALLBACK_LANGUAGE = 'en';
        const COOKIE_LANG_VAR = 'lang';
        const URI_LANG_VAR = 'lang';

        protected $currentLanguage;
        protected $trArray = [];

        /**
         * Returns instance of itself.
         */
        public static function instance(): self
        {
            static $instance = null;

            if (! $instance) {
                $instance = (new static())->initialize();
            }

            return $instance;
        }

        protected function initialize(): self
        {
            $initLang = $this->getInitLang();

            if (! $this->isLangSupported($initLang)) {
                $initLang = self::FALLBACK_LANGUAGE;
            }

            $this->setCurrentLang($initLang);
            $this->loadLangFile($initLang);

            Languages::setLocale($initLang);

            return $this;
        }

        /**
         * Get the current application language.
         */
        public static function getCurrentLang(): string
        {
            return self::instance()->currentLanguage;
        }

        /**
         * Get the default language for this node.
         */
        public static function getDefaultLang(): string
        {
            return OcConfig::getI18nDefaultLang();
        }

        /**
         * Get the language which can be used to look up for a translation in
         * the given DB table.
         *
         * @deprecated DB translations should not be used.
         */
        public static function getLangForDbTranslations(string $table): string
        {
            $language = self::getCurrentLang();

            if (! XDb::xContainsColumn($table, $language)) {
                self::FALLBACK_LANGUAGE;
            }

            return $language;
        }

        /**
         * The only function to initialize I18n for OC code.
         * This should be called at the beginning of every request.
         */
        public static function init(): self
        {
            // Just be sure that instance of this class is created
            return self::instance();
        }

        /**
         * Get the translation of the given message.
         *
         * @param bool $skipPostprocess - Skip "old-template" postprocessing
         */
        public static function translatePhrase(
            string $key,
            string $language = null,
            bool $skipPostprocess = false,
            bool $skipFallback = false
        ): string {
            return self::instance()->translate($key, $language, $skipPostprocess, $skipFallback);
        }

        /**
         * Check if the given message exists.
         */
        public static function isTranslationAvailable(string $key): bool
        {
            $language = self::getCurrentLang();

            return (bool) (self::instance()->trArray[$language][$key] ?? false);
        }

        public static function getLanguagesFlagsData(bool $withoutCurrent = false): array
        {
            return self::instance()->getFlags($withoutCurrent);
        }

        protected function getFlags(bool $withoutCurrent = false): array
        {
            $result = [];

            foreach ($this->getSupportedTranslations() as $language) {
                if ($withoutCurrent && $language === self::getCurrentLang()) {
                    continue;
                }

                $result[$language] = [
                    'name' => $language,
                    'img' => "/images/flags/{$language}.svg",
                    'link' => Uri::setOrReplaceParamValue(self::URI_LANG_VAR, $language),
                ];
            }

            return $result;
        }

        /**
         * Get the language which should be used for the current instance.
         */
        protected function getInitLang(): string
        {
            // 'lang' being set in request means that the user wants to change a language
            $langToUse = $_REQUEST[self::URI_LANG_VAR]
                ?? OcCookie::get(self::COOKIE_LANG_VAR)
                ?? $this->getDefaultLang();

            // Check request for CrowdinInContext mode commands
            CrowdinInContextMode::checkRequest($langToUse);

            if (CrowdinInContextMode::enabled()) {
                return CrowdinInContextMode::getPseudoLang();
            }

            return $langToUse;
        }

        protected function translate(
            string $key,
            string $language = null,
            $skipPostprocess = false,
            $skipFallback = false
        ): string {
            $language = $language ?? self::getCurrentLang();

            if (! isset($this->trArray[$language])) {
                $this->loadLangFile($language);
            }

            // Requested phrase found
            if ($this->trArray[$language][$key] ?? false) {
                if ($skipPostprocess) {
                    $this->trArray[$language][$key];
                }

                return $this->postProcessTr($this->trArray[$language][$key]);
            }

            // There is no such phrase, try to use the fallback language
            if (! $skipFallback && $language !== self::FALLBACK_LANGUAGE) {
                return $this->translate($key, self::FALLBACK_LANGUAGE, $skipPostprocess);
            }

            // Oops - no such phrase at all - even in the fallback language...
            Debug::errorLog("Unknown translation for id: {$key}");
            return "No translation available (id: {$key})";
        }

        protected function postProcessTr(string $ref): string
        {
            if (strpos($ref, '{') === false) {
                return $ref;
            }

            // Desktop template system is not available on mobile pages.
            if (! function_exists('tpl_do_replace')) {
                throw new Exception(
                    '{} placeholders must not be used in mobile_ translation texts.'
                );
            }

            return tpl_do_replace($ref, true);
        }

        /**
         * Load the translation file for the given locale.
         */
        protected function loadLangFile(string $language): void
        {
            require self::languageFilename($language);

            $this->trArray[$language] = $translations;
        }

        protected function languageFileTime(string $language): int
        {
            return filemtime(self::languageFilename($language));
        }

        protected static function languageFilename(string $language): string
        {
            $filename = __DIR__ . "/../../../lib/languages/{$language}.php";

            if (! file_exists($filename)) {
                throw new Exception("Can't find the translation file for {$language}!");
            }

            return $filename;
        }

        protected function setCurrentLang(string $language): void
        {
            $this->currentLanguage = $language;

            OcCookie::set(self::COOKIE_LANG_VAR, $language, true);
        }

        /**
         * @see /config/i18n.default.php.
         */
        protected function getSupportedTranslations(): array
        {
            return OcConfig::getI18nSupportedLangs();
        }

        protected function isLangSupported(string $language): bool
        {
            if (
                CrowdinInContextMode::isSupportedInConfig()
                && $language === CrowdinInContextMode::getPseudoLang()
            ) {
                return true;
            }

            return in_array($language, $this->getSupportedTranslations());
        }

        /**
         * Get all translations for keys starting with the given prefix.
         */
        public static function getPrefixedTranslationsWithFallback(string $prefix): array
        {
            $cacheKey = 'translations-' . self::getCurrentLang() . '-' . $prefix;

            $currentLangFiletime = self::instance()->languageFileTime(self::getCurrentLang());
            $fallbackFiletime = self::instance()->languageFileTime(self::FALLBACK_LANGUAGE);

            $translations = OcMemCache::get($cacheKey);

            if (
                $translations !== false
                && $translations['failover-filetime'] == $fallbackFiletime
                && $translations['main-filetime'] == $currentLangFiletime
            ) {
                // Use cached translations
                return $translations['translations'];
            }

            // Compile a new translation set
            $translations = self::instance()->makePrefixedTranslationsWithFallback($prefix);

            OcMemCache::store($cacheKey, 3600 * 24, [
                // If you change anything here, also change the name of the cache key!
                // Otherwise the old cached data can crash the changed implementation.
                'failover-filetime' => $fallbackFiletime,
                'main-filetime' => $currentLangFiletime,
                'translations' => $translations
            ]);

            return $translations;
        }

        protected function makePrefixedTranslationsWithFallback(string $prefix): array
        {
            if (! isset($this->trArray[self::FALLBACK_LANGUAGE])) {
                $this->loadLangFile(self::FALLBACK_LANGUAGE);
            }

            $prefixLength = strlen($prefix);
            $currentLanguage = self::getCurrentLang();
            $translations = [];

            foreach ($this->trArray[self::FALLBACK_LANGUAGE] as $key => $text) {
                if (substr($key, 0, $prefixLength) !== $prefix) {
                    continue;
                }

                $translations[substr($key, $prefixLength)] = $this->trArray[$currentLanguage][$key] ?? $text;
            }

            return $translations;
        }

        /**
         * TODO: cache_atttrib
         *
         * @deprecated Method for retrieving and maintaining old-style database translations.
         */
        public static function getTranslationTables()
        {
            return ['cache_size', 'cache_status', 'cache_type', 'log_types', 'waypoint_type', 'languages'];
        }

        /**
         * @deprecated Method for retrieving and maintaining old-style database translations.
         */
        public static function getTranslationIdColumnName($table)
        {
            if ($table === 'languages') {
                return 'short';
            }

            if ($table === 'cache_type') {
                return 'sort';  // not 'id' !
            }

            if (in_array($table, self::getTranslationTables())) {
                return 'id';
            }

            throw new Exception("Unknown table: '{$table}'");
        }

        /**
         * @deprecated Method for retrieving and maintaining old-style database translations.
         */
        public static function getTranslationKey($table, $id)
        {
            $prefixes = [
                'cache_size' => 'cacheSize_',
                'cache_status' => 'cacheStatus_',
                'cache_type' => 'cacheType_',
                'languages' => 'language_',
                'log_types' => 'logType',
                'waypoint_type' => 'wayPointType',
            ];

            if (! isset($prefixes[$table])) {
                throw new Exception("Unknown table: '{$table}'");
            }

            if ($table === 'cache_size') {
                $sizeIds = ['other', 'micro', 'small', 'regular', 'large', 'xLarge', 'none', 'nano'];

                if (! array_key_exists($id - 1, $sizeIds)) {
                    throw new Exception("Invalid size ID passed to getTranslationId(): {$id}");
                }

                $id = $sizeIds[$id - 1];
            }

            return $prefixes[$table] . $id;
        }
    }
}
