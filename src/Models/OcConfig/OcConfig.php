<?php

namespace src\Models\OcConfig;

final class OcConfig extends ConfigReader
{
    use EmailConfigTrait;
    use GeocacheConfigTrait;
    use I18nConfigTrait;
    use MapConfigTrait;
    use OkapiConfigTrait;
    use PicturesConfigTrait;
    use PrimaAprilisTrait;
    use SiteConfigTrait;

    /*
        const OCNODE_GERMANY    = 1;  // Opencaching Germany http://www.opencaching.de OC
        const OCNODE_POLAND     = 2;  // Opencaching Poland http://www.opencaching.pl OP
        const OCNODE_CZECH      = 3;  // Opencaching Czech http://www.opencaching.cz OZ
        const OCNODE_DEVELOPER  = 4;  // Local Development
        const OCNODE_UK         = 6;  // Opencaching Great Britain http://www.opencaching.org.uk OK
        const OCNODE_SWEDEN     = 7;  // Opencaching Sweden http://www.opencaching.se OS =>OC Scandinavia
        const OCNODE_USA        = 10; // Opencaching United States http://www.opencaching.us OU
        const OCNODE_RUSSIA     = 12; // Opencaching Russia http://www.opencaching.org.ru
        const OCNODE_BENELUX    = 14; // Opencaching Nederland https://www.opencaching.nl OB => OC Benelux
        const OCNODE_ROMANIA    = 16; // Opencaching Romania http://www.opencaching.ro OR
    */

    // old-style values - values from new-style config should be accessed through
    // $config[''] etc...

    private $debugMode = false;
    private $dbDatetimeFormat = 'Y-m-d H:i:s';
    private $datetimeFormat = 'Y-m-d H:i';
    private $absolute_server_URI = null;
    private $dynamicFilesPath;
    private $powerTrailModuleSwitchOn;
    private $googleMapKey;
    private $siteInService = false;
    private $dateFormat;
    private $headerLogo;
    private $shortSiteName;
    private $needFindLimit;
    private $needApproveLimit;
    private $enableCacheAccessLogs;
    private $minimumAge;
    private $meritBadgesEnabled;

    private $dbUser;
    private $dbPass;
    private $dbAdminUser;
    private $dbAdminPass;
    private $dbHost;
    private $dbName;

    /**
     * Configuration for src\Utils\Lock objects from /config/lock.* files.
     *
     * @var array
     */
    private $lockConfig;

    /**
     * Watchlist configuration from /config/watchlist.* files.
     *
     * @var array
     */
    private $watchlistConfig;

    /**
     * Cache log filter configuration from /config/logfilter.* files.
     *
     * @var array
     */
    private $logfilterConfig;

    /**
     * News configuration from /config/news.* files.
     *
     * @var array
     */
    private $newsConfig;

    /**
     * User configuration from /config/user.* files.
     *
     * @var array
     */
    private $userConfig;

    /**
     * Guides configuration from /config/guides.* files.
     *
     * @var array
     */
    private $guidesConfig;

    /**
     * Configuration from /config/banner.* files.
     *
     * @var array
     */
    private $topBannerVideo;

    /**
     * Configuration from /config/banner.* files.
     *
     * @var array
     */
    private $topBannerTxt;

    /**
     * Cronjob configuration from /config/cronjobs.* files.
     *
     * @var array
     */
    private $cronjobsConfig;

    /**
     * 'week' or 'month' - frequency of cache titled.
     *
     * @var string
     */
    private $titledCachePeriod;

    /**
     * Get the singleton.
     */
    public static function instance(): self
    {
        static $inst = null;

        if ($inst === null) {
            $inst = new self();
        }

        return $inst;
    }

    /**
     * Private constructor so nobody else can instantiate it.
     */
    protected function __construct()
    {
        $this->loadConfig();
    }

    private function loadConfig()
    {
        global $debug_page;

        require self::LEGACY_LOCAL_CONFIG;

        $this->debugMode = $debug_page;
        $this->datetimeFormat = $datetimeFormat;
        $this->absolute_server_URI = $absolute_server_URI;
        $this->dynamicFilesPath = $dynbasepath;
        $this->powerTrailModuleSwitchOn = $powerTrailModuleSwitchOn;
        $this->googleMapKey = $googlemap_key;
        $this->dateFormat = $dateFormat;
        $this->headerLogo = $config['headerLogo'];
        $this->shortSiteName = $short_sitename;
        $this->needApproveLimit = $NEED_APPROVE_LIMIT;
        $this->needFindLimit = $NEED_FIND_LIMIT;
        $this->enableCacheAccessLogs = $enable_cache_access_logs;
        $this->minimumAge = $config['limits']['minimum_age'];
        $this->meritBadgesEnabled = $config['meritBadges'];
        $this->titledCachePeriod = $titled_cache_period_prefix;

        $this->dbHost = $dbserver;
        $this->dbName = $dbname;
        $this->dbUser = $dbusername;
        $this->dbPass = $dbpasswd;

        $this->dbAdminUser = $opt['db']['admin_username'] ?? $this->dbUser;
        $this->dbAdminPass = $opt['db']['admin_password'] ?? $this->dbPass;

        if (is_array($config['lock'] ?? null)) {
            $this->lockConfig = $config['lock'];
        }

        if (is_array($config['watchlist'] ?? null)) {
            $this->watchlistConfig = $config['watchlist'];
        }

        if (is_array($config['logfilter'] ?? null)) {
            $this->logfilterConfig = $config['logfilter'];
        }
    }

    public function inDebugMode()
    {
        return $this->debugMode;
    }

    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    public function getDatetimeFormat()
    {
        return $this->datetimeFormat;
    }

    public static function getWikiLinks(): array
    {
        return self::instance()->getLinks()['wiki'];
    }

    public static function getWikiLink(string $wikiLinkKey): string
    {
        return self::getWikiLinks()[$wikiLinkKey];
    }

    public static function getAbsolute_server_URI()
    {
        return self::instance()->absolute_server_URI;
    }

    public function getDbDateTimeFormat()
    {
        return $this->dbDatetimeFormat;
    }

    public static function getDynFilesPath($trimTrailingSlash = false)
    {
        $path = self::instance()->getDynamicFilesPath();

        return $trimTrailingSlash ? rtrim($path, '/') : $path;
    }

    public function getDynamicFilesPath()
    {
        return $this->dynamicFilesPath;
    }

    public static function isPowertrailsEnabled()
    {
        return self::instance()->isPowerTrailModuleSwitchOn();
    }

    public function isPowerTrailModuleSwitchOn()
    {
        return $this->powerTrailModuleSwitchOn;
    }

    public function isCacheAccessLogEnabled()
    {
        return $this->enableCacheAccessLogs;
    }

    public function getMinumumAge(): int
    {
        return $this->minimumAge;
    }

    public function isMeritBadgesEnabled()
    {
        return $this->meritBadgesEnabled;
    }

    public function getDbUser($admin = false)
    {
        return $admin ? $this->dbAdminUser : $this->dbUser;
    }

    public function getDbPass($admin = false)
    {
        return $admin ? $this->dbAdminPass : $this->dbPass;
    }

    public function getDbHost()
    {
        return $this->dbHost;
    }

    public function getDbName()
    {
        return $this->dbName;
    }

    public static function getHeaderLogo()
    {
        return self::instance()->headerLogo;
    }

    public static function getShortSiteName()
    {
        return self::instance()->shortSiteName;
    }

    public static function getNeedFindLimit()
    {
        return self::instance()->needFindLimit;
    }

    public static function getNeedApproveLimit()
    {
        return self::instance()->needApproveLimit;
    }

    /**
     * @see /config/lock.default.php
     */
    public function getLockConfig(): array
    {
        if (! $this->lockConfig) {
            $this->lockConfig = self::getConfig('lock', 'lock');
        }

        return $this->lockConfig;
    }

    /**
     * @see /config/watchlist.default.php
     */
    public function getWatchlistConfig(): array
    {
        if (! $this->watchlistConfig) {
            $this->watchlistConfig = self::getConfig('watchlist', 'watchlist');
        }

        return $this->watchlistConfig;
    }

    /**
     * @see /config/logfilter.default.php
     */
    public function getLogfilterConfig(): array
    {
        if (! $this->logfilterConfig) {
            $this->logfilterConfig = self::getConfig('logfilter', 'logfilter');
        }

        return $this->logfilterConfig;
    }

    /**
     * @see /config/user.default.php
     */
    public function getUserConfig()
    {
        if (! $this->userConfig) {
            $this->userConfig = self::getConfig('user', 'user');
        }

        return $this->userConfig;
    }

    /**
     * @see /config/guides.default.php
     */
    public function getGuidesConfig(): array
    {
        if (! $this->guidesConfig) {
            $this->guidesConfig = self::getConfig('guides', 'guides');
        }

        return $this->guidesConfig;
    }

    /**
     * @see /config/cronjobs.default.php
     */
    public function getCronjobSchedule($job = null)
    {
        if (! $this->cronjobsConfig) {
            $this->cronjobsConfig = self::getConfig('cronjobs', 'cronjobs');
        }

        return $job === null
            ? $this->cronjobsConfig['schedule']
            : $this->cronjobsConfig['schedule'][$job] ?? null;
    }

    /**
     * @see /config/news.default.php
     */
    public function getNewsConfig($key = null)
    {
        if (! $this->newsConfig) {
            $this->newsConfig = self::getConfig('news', 'news');
        }

        return $key === null
            ? $this->newsConfig
            : $this->newsConfig[$key];
    }

    /**
     * @see /config/banner.default.php
     *
     * @return string[]
     */
    public function getTopBannerTxt(): array
    {
        if (! $this->topBannerTxt) {
            $this->topBannerTxt = self::getConfig('banner', 'bannerTxt');
        }

        return $this->topBannerTxt;
    }

    /**
     * @see /config/banner.default.php
     *
     * @return string[]
     */
    public function getTopBannerVideo(): array
    {
        if (! $this->topBannerVideo) {
            $this->topBannerVideo = self::getConfig('banner', 'bannerVideo');
        }

        return $this->topBannerVideo;
    }

    public function getTitledCachePeriod(): string
    {
        return $this->titledCachePeriod;
    }
}
