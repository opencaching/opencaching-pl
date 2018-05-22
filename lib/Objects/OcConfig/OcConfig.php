<?php

namespace lib\Objects\OcConfig;



final class OcConfig extends ConfigReader
{

/*
    const OCNODE_GERMANY    = 1;  // Opencaching Germany http://www.opencaching.de OC
    const OCNODE_POLAND     = 2;  // Opencaching Poland http://www.opencaching.pl OP
    const OCNODE_CZECH      = 3;  // Opencaching Czech http://www.opencaching.cz OZ
    const OCNODE_DEVELOPER  = 4;  // Local Development
    const OCNODE_UK         = 6;  // Opencaching Great Britain http://www.opencaching.org.uk OK
    const OCNODE_SWEDEN     = 7;  // Opencaching Sweden http://www.opencaching.se OS =>OC Scandinavia
    const OCNODE_USA        = 10; // Opencaching United States http://www.opencaching.us OU
    const OCNODE_RUSSIA     = 12; // Opencaching Russia http://www.opencaching.org.ru
    const OCNODE_BENELUX    = 14; // Opencaching Nederland http://www.opencaching.nl OB => OC Benelux
    const OCNODE_ROMANIA    = 16; // Opencaching Romania http://www.opencaching.ro OR
*/


// old-style values - values from new-style config shoul be accessed through
// $config[''] etc...

    private $dbDatetimeFormat = 'Y-m-d H:i:s';
    private $datetimeFormat = 'Y-m-d H:i';
    private $ocNodeId = null;
    private $absolute_server_URI = null;
    private $octeamEmailsSignature = null;
    private $octeamEmailAddress;
    private $siteName;
    private $dynamicFilesPath;
    private $powerTrailModuleSwitchOn;
    private $googleMapKey;
    private $mainPageMapCenterLat;
    private $mainPageMapCenterLon;
    private $mainPageMapZoom;
    private $siteInService = false;
    private $pagetitle;
    private $defaultLanguage;
    private $pictureDirectory;
    private $pictureUrl;
    private $contactMail;
    private $dateFormat;
    private $noreplyEmailAddress;
    private $mapsConfig;            //settings.inc: $config['mapsConfig']
    private $headerLogo;
    private $shortSiteName;
    private $needFindLimit;
    private $needAproveLimit;
    private $cogEmailAddress;
    private $mailSubjectPrefixForSite;
    private $mailSubjectPrefixForReviewers;
    private $enableCacheAccessLogs;
    private $minumumAge;

    private $dbUser;
    private $dbPass;
    private $dbHost;
    private $dbName;

    /** @var array the \Utils\Lock objects configuration array */
    private $lockConfig;
    /** @var array the watchlist configuration array */
    private $watchlistConfig;

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
        require self::LEGACY_LOCAL_CONFIG;

        $this->datetimeFormat = $datetimeFormat;
        $this->ocNodeId = $oc_nodeid;
        $this->absolute_server_URI = $absolute_server_URI;
        $this->octeamEmailsSignature = $octeamEmailsSignature;
        $this->octeamEmailAddress = $octeam_email;
        $this->cogEmailAddress = $mail_cog;
        $this->siteName = $site_name;
        $this->dynamicFilesPath = $dynbasepath;
        $this->powerTrailModuleSwitchOn = $powerTrailModuleSwitchOn;
        $this->googleMapKey = $googlemap_key;
        $this->mainPageMapCenterLat = $main_page_map_center_lat;
        $this->mainPageMapCenterLon = $main_page_map_center_lon;
        $this->mainPageMapZoom = $main_page_map_zoom;
        $this->siteInService = $site_in_service;
        $this->pagetitle = $pagetitle;
        $this->defaultLanguage = $lang;
        $this->pictureDirectory = $picdir;
        $this->pictureUrl = $picurl;
        $this->contactMail = $contact_mail;
        $this->dateFormat = $dateFormat;
        $this->noreplyEmailAddress = $emailaddr;
        $this->headerLogo = $config['headerLogo'];
        $this->shortSiteName = $short_sitename;
        $this->needAproveLimit = $NEED_APPROVE_LIMIT;
        $this->needFindLimit = $NEED_FIND_LIMIT;
        $this->mailSubjectPrefixForSite = $subject_prefix_for_site_mails;
        $this->mailSubjectPrefixForReviewers = $subject_prefix_for_reviewers_mails;
        $this->enableCacheAccessLogs = $enable_cache_access_logs;
        $this->minumumAge = $config['limits']['minimum_age'];

        if (isset($config['mapsConfig']) && is_array($config['mapsConfig'])) {
            $this->mapsConfig = $config['mapsConfig'];
        } else {
            $this->mapsConfig = array();
        }

        $this->isGoogleTranslationEnabled = ! (isset($disable_google_translation) && $disable_google_translation);

        $this->dbHost = $opt['db']['server'];
        $this->dbName = $opt['db']['name'];
        $this->dbUser = $opt['db']['username'];
        $this->dbPass = $opt['db']['password'];

        if (isset($config['lock']) && is_array($config['lock'])) {
            $this->lockConfig = $config['lock'];
        }
        if (isset($config['watchlist']) && is_array($config['watchlist'])) {
            $this->watchlistConfig = $config['watchlist'];
        }
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

    public static function getOcteamEmailsSignature()
    {
        return self::instance()->octeamEmailsSignature;
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

    public function getOcteamEmailAddress()
    {
        return $this->octeamEmailAddress;
    }

    public function getDynamicFilesPath()
    {
        return $this->dynamicFilesPath;
    }

    public static function isPowertrailsEnabled()
    {
        return self::instance()->instance()->isPowerTrailModuleSwitchOn();
    }

    public function isPowerTrailModuleSwitchOn()
    {
        return $this->powerTrailModuleSwitchOn;
    }

    public static function getNoreplyEmailAddress()
    {
        return self::instance()->instance()->noreplyEmailAddress;
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

    public function getDbUser()
    {
        return $this->dbUser;
    }

    public function getDbPass()
    {
        return $this->dbPass;
    }

    public function getDbHost()
    {
        return $this->dbHost;
    }

    public function getDbName()
    {
        return $this->dbName;
    }

    public static function getTechAdminsEmailAddr()
    {
        //it will be implemented in a future
        //currently this is only a stub...
        global $mail_rt;

        return $mail_rt;
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

    public static function getNeedAproveLimit()
    {
        return self::instance()->needAproveLimit;
    }

    public static function getCogEmailAddress()
    {
        return self::instance()->cogEmailAddress;
    }

    public static function getMailSubjectPrefixForSite()
    {
        return self::instance()->mailSubjectPrefixForSite;
    }

    public static function getMailSubjectPrefixForReviewers()
    {
        return self::instance()->mailSubjectPrefixForReviewers;
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
}
