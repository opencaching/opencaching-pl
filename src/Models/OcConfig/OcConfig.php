<?php

namespace src\Models\OcConfig;

final class OcConfig extends ConfigReader
{
    use EmailConfigTrait, SiteConfigTrait, I18nConfigTrait, PicturesConfigTrait, MapConfigTrait;

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
    private $minumumAge;
    private $meritBadgesEnabled;

    private $dbUser;
    private $dbPass;
    private $dbAdminUser;
    private $dbAdminPass;
    private $dbHost;
    private $dbName;

    /** @var array the \src\Utils\Lock objects configuration array */
    private $lockConfig;

    /** @var array the watchlist configuration array */
    private $geoCacheConfig;

    /** @var array the watchlist configuration array */
    private $watchlistConfig;

    /** @var array the logfilter configuration array */
    private $logfilterConfig;

    /** @var array */
    private $newsConfig;

    /** @var array - array of user settings from /Config/user.* files */
    private $userConfig;

    /** @var array - array of guides settings from /Config/guides.* files */
    private $guidesConfig;

    /** @var array */
    private $topBannerVideo;
    /** @var array */
    private $topBannerTxt;

    /** @var array - array of cronjob settings from /Config/cronjobs.* files */
    private $cronjobsConfig;

    /**
     * Call this method to get singleton
     * @return ocConfig
     */
    public static function instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new self();
        }
        return $inst;
    }

    /**
     * Private ctor so nobody else can instance it
     */
    protected function __construct()
    {
        parent::__construct();
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
        $this->minumumAge = $config['limits']['minimum_age'];
        $this->meritBadgesEnabled = $config['meritBadges'];

        $this->dbHost = $opt['db']['server'];
        $this->dbName = $opt['db']['name'];
        $this->dbUser = $opt['db']['username'];
        $this->dbPass = $opt['db']['password'];

        if (isset($opt['db']['admin_username'])) {
            $this->dbAdminUser = $opt['db']['admin_username'];
            $this->dbAdminPass = $opt['db']['admin_password'];
        } else {
            $this->dbAdminUser = $this->dbUser;
            $this->dbAdminPass = $this->dbPass;
        }

        if (isset($config['lock']) && is_array($config['lock'])) {
            $this->lockConfig = $config['lock'];
        }
        if (isset($config['watchlist']) && is_array($config['watchlist'])) {
            $this->watchlistConfig = $config['watchlist'];
        }
        if (isset($config['logfilter']) && is_array($config['logfilter'])) {
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

    /**
     * Returns array of wiki-links readed from config
     * @return array
     */
    public static function getWikiLinks()
    {
        return self::instance()->getLinks()['wiki'];
    }

    /**
     * Returns single link to wiki
     * @param string $wikiLinkKey
     * @return string - link to wiki
     */
    public static function getWikiLink($wikiLinkKey)
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
        if($trimTrailingSlash){
            return rtrim($path, '/');
        }
        return $path;
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



    public function isCacheAccesLogEnabled()
    {
        return $this->enableCacheAccessLogs;
    }

    /**
     * @return integer
     */
    public function getMinumumAge()
    {
        return $this->minumumAge;
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
     * Gives \src\Utils\Lock objects configuration, tries to initialize it if null
     *
     * @return array \src\Utils\Lock objects configuration
     *               ({@see /Config/lock.default.php})
     */
    public function getLockConfig()
    {
        if ($this->lockConfig == null) {
            $this->lockConfig = self::getConfig("lock", "lock");
        }
        return $this->lockConfig;
    }


    public function getGeoCacheConfig($setting = null)
    {
        if ($this->geoCacheConfig == null) {
            $this->geoCacheConfig = self::getConfig("geocache", "geocache");
        }
        if ($setting !== null) {
            return $this->geoCacheConfig[$setting];
        } else {
            return $this->geoCacheConfig;
        }
    }

    /**
     * Gives watchlist configuration, tries to initialize it if null
     *
     * @return array watchlist configuration
     *               ({@see /Config/watchlist.default.php})
     */
    public function getWatchlistConfig()
    {
        if ($this->watchlistConfig == null) {
            $this->watchlistConfig = self::getConfig("watchlist", "watchlist");
        }
        return $this->watchlistConfig;
    }

    /**
     * Gives logfilter configuration, tries to initialize it if null
     *
     * @return array logfilter configuration
     *               ({@see /Config/logfilter.default.php})
     */
    public function getLogfilterConfig()
    {
        if ($this->logfilterConfig == null) {
            $this->logfilterConfig = self::getConfig("logfilter", "logfilter");
        }
        return $this->logfilterConfig;
    }


    public function getUserConfig()
    {
        if ($this->userConfig == null) {
            $this->userConfig = self::getConfig("user", "user");
        }
        return $this->userConfig;
    }

    public function getGuidesConfig()
    {
        if ($this->guidesConfig == null) {
            $this->guidesConfig = self::getConfig("guides", "guides");
        }
        return $this->guidesConfig;
    }



    public function getCronjobSchedule($job = null)
    {
        if ($this->cronjobsConfig == null) {
            $this->cronjobsConfig = self::getConfig('cronjobs', 'cronjobs');
        }
        if ($job === null) {
            return $this->cronjobsConfig['schedule'];
        } elseif (isset($this->cronjobsConfig['schedule'][$job])) {
            return $this->cronjobsConfig['schedule'][$job];
        } else {
            return null;
        }
    }

    public function getNewsConfig($setting = null)
    {
        if ($this->newsConfig == null) {
            $this->newsConfig = self::getConfig("news", "news");
        }
        if ($setting === null) {
            return $this->newsConfig;
        } else {
            return $this->newsConfig[$setting];
        }
    }

    /**
     * Gives top banner texts
     *
     * @return array
     *               ({@see /Config/banner.default.php})
     */
    public function getTopBannerTxt()
    {
        if ($this->topBannerTxt == null) {
            $this->topBannerTxt = self::getConfig("banner", "bannerTxt");
        }
        return $this->topBannerTxt;
    }

    /**
     * Gives top banner video list
     *
     * @return array
     *               ({@see /Config/banner.default.php})
     */
    public function getTopBannerVideo()
    {
        if ($this->topBannerVideo == null) {
            $this->topBannerVideo = self::getConfig("banner", "bannerVideo");
        }
        return $this->topBannerVideo;
    }

}
