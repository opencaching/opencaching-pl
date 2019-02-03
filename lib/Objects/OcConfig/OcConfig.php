<?php

namespace lib\Objects\OcConfig;

final class OcConfig extends ConfigReader
{
    use EmailConfigTrait, SiteConfigTrait;

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
    private $ocNodeId = null;
    private $absolute_server_URI = null;
    private $siteName;
    private $dynamicFilesPath;
    private $powerTrailModuleSwitchOn;
    private $googleMapKey;
    private $mainPageMapCenterLat;
    private $mainPageMapCenterLon;
    private $mainPageMapZoom;
    private $siteInService = false;
    private $pagetitle;
    private $pictureDirectory;
    private $pictureUrl;
    private $dateFormat;
    private $mapsConfig;            //settings.inc: $config['mapsConfig']
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

    /** @var array of i18n settings */
    private $i18nConfig;

    /** @var array the \Utils\Lock objects configuration array */
    private $lockConfig;

    /** @var array the watchlist configuration array */
    private $geoCacheConfig;

    /** @var array the watchlist configuration array */
    private $watchlistConfig;

    /** @var array the logfilter configuration array */
    private $logfilterConfig;

    /** @var array */
    private $newsConfig;

    /** @var array - array of map settings from /Config/map.* files */
    private $mapConfig;

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
        $this->ocNodeId = $oc_nodeid;
        $this->absolute_server_URI = $absolute_server_URI;
        $this->siteName = $site_name;
        $this->dynamicFilesPath = $dynbasepath;
        $this->powerTrailModuleSwitchOn = $powerTrailModuleSwitchOn;
        $this->googleMapKey = $googlemap_key;
        $this->mainPageMapCenterLat = $main_page_map_center_lat;
        $this->mainPageMapCenterLon = $main_page_map_center_lon;
        $this->mainPageMapZoom = $main_page_map_zoom;
        $this->siteInService = $site_in_service;
        $this->pagetitle = $pagetitle;
        $this->pictureDirectory = $picdir;
        $this->pictureUrl = $picurl;
        $this->dateFormat = $dateFormat;
        $this->headerLogo = $config['headerLogo'];
        $this->shortSiteName = $short_sitename;
        $this->needApproveLimit = $NEED_APPROVE_LIMIT;
        $this->needFindLimit = $NEED_FIND_LIMIT;
        $this->enableCacheAccessLogs = $enable_cache_access_logs;
        $this->minumumAge = $config['limits']['minimum_age'];
        $this->meritBadgesEnabled = $config['meritBadges'];

        if (isset($config['mapsConfig']) && is_array($config['mapsConfig'])) {
            $this->mapsConfig = $config['mapsConfig'];
        } else {
            $this->mapsConfig = array();
        }

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

    public function getPageTitle()
    {
        return $this->pagetitle;
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

    public function getMainPageMapCenterLat()
    {
        return $this->mainPageMapCenterLat;
    }

    public function getMainPageMapCenterLon()
    {
        return $this->mainPageMapCenterLon;
    }

    public function getMainPageMapZoom()
    {
        return $this->mainPageMapZoom;
    }

    public static function getAbsolute_server_URI()
    {
        return self::instance()->absolute_server_URI;
    }



    public function getOcNodeId()
    {
        return $this->ocNodeId;
    }

    public function getDbDateTimeFormat()
    {
        return $this->dbDatetimeFormat;
    }

    public static function getSiteName()
    {
        return self::instance()->siteName;
    }



    public static function getDynFilesPath()
    {
        return self::instance()->getDynamicFilesPath();
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

    protected function getMapsConfig()
    {
        return $this->mapsConfig;
    }

    /**
     * get $config['mapsConfig'] from settings.inc.php in a static way
     * always return an array
     */
    public static function mapsConfig()
    {
        return self::instance()->getMapsConfig();
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
     * Gives \Utils\Lock objects configuration, tries to initialize it if null
     *
     * @return array \Utils\Lock objects configuration
     *               ({@see /Config/lock.default.php})
     */
    public function getLockConfig()
    {
        if ($this->lockConfig == null) {
            $this->lockConfig = self::getConfig("lock", "lock");
        }
        return $this->lockConfig;
    }


    public function getI18Config()
    {
        if ($this->i18nConfig == null) {
            $this->i18nConfig = self::getConfig("i18n");
        }
        return $this->i18nConfig;
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

    /**
     * Gives map configuration, tries to initialize it if null
     *
     * @return array map configuration
     *               ({@see /Config/map.default.php})
     */
    public function getMapConfig()
    {
        if ($this->mapConfig == null) {
            $this->mapConfig = self::getConfig("map", "map");
        }
        return $this->mapConfig;
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
