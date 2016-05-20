<?php

namespace lib\Objects\OcConfig;

/**
 * Settings container
 *
 * @author Andrzej Łza Wozniak
 */
final class OcConfig
{

    const OCNODE_GERMANY = 1; /* Opencaching Germany http://www.opencaching.de OC */
    const OCNODE_POLAND = 2; /* Opencaching Poland http://www.opencaching.pl OP */
    const OCNODE_CZECH = 3; /* Opencaching Czech http://www.opencaching.cz OZ */
    const OCNODE_DEVELOPER = 4; /* Local Development */
    const OCNODE_UK = 6; /* Opencaching Great Britain http://www.opencaching.org.uk OK */
    const OCNODE_SWEDEN = 7; /* Opencaching Sweden http://www.opencaching.se OS =>OC Scandinavia */
    const OCNODE_USA = 10; /* Opencaching United States http://www.opencaching.us OU */
    const OCNODE_RUSSIA = 12; /* Opencaching Russia http://www.opencaching.org.ru */
    const OCNODE_BENELUX = 14; /* Opencaching Nederland http://www.opencaching.nl OB => OC Benelux */
    const OCNODE_ROMANIA = 16; /* Opencaching Romania http://www.opencaching.ro OR */

    private $medalsModuleSwitchedOn = false;
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
    private $siteInService = false;
    private $pagetitle;
    private $defaultLanguage;
    private $pictureDirectory;
    private $pictureUrl;
    private $contactMail;
    private $wikiLinks;
    private $dateFormat;
    private $noreplyEmailAddress;
    private $mapsConfig;            //settings.inc: $config['mapsConfig']
    private $headerLogo;
    private $shortSiteName;
    private $needFindLimit;
    private $needAproveLimit;
    private $cogEmailAddress;

    // db config
    private $dbUser;
    private $dbPass;
    private $dbHost;
    private $dbName;

    /**
     * Call this method to get singleton
     * @return ocConfig
     */
    public static function instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new ocConfig();
        }
        return $inst;
    }

    /**
     * Private ctor so nobody else can instance it
     */
    private function __construct()
    {
        $this->loadConfig();
    }

    private function loadConfig()
    {
        require __DIR__ . '/../../settings.inc.php';
        $this->medalsModuleSwitchedOn = $config['medalsModuleSwitchedOn'];
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
        $this->siteInService = $site_in_service;
        $this->pagetitle = $pagetitle;
        $this->defaultLanguage = $lang;
        $this->pictureDirectory = $picdir;
        $this->pictureUrl = $picurl;
        $this->contactMail = $contact_mail;
        $this->wikiLinks = $wikiLinks;
        $this->dateFormat = $dateFormat;
        $this->noreplyEmailAddress = $emailaddr;
        $this->headerLogo = $config['headerLogo'];
        $this->shortSiteName = $short_sitename;
        $this->needAproveLimit = $NEED_APPROVE_LIMIT;
        $this->needFindLimit = $NEED_FIND_LIMIT;

        if( isset($config['mapsConfig']) && is_array( $config['mapsConfig'] ) ){
            $this->mapsConfig = $config['mapsConfig'];
        }else{
            $this->mapsConfig = array();
        }

        $this->isGoogleTranslationEnabled = !( isset( $disable_google_translation ) && $disable_google_translation );

        $this->dbHost = $opt['db']['server'];
        $this->dbName = $opt['db']['name'];
        $this->dbUser = $opt['db']['username'];
        $this->dbPass = $opt['db']['password'];

    }

    function getDateFormat()
    {
        return $this->dateFormat;
    }

    function getDatetimeFormat()
    {
        return $this->datetimeFormat;
    }

    public static function getWikiLinks()
    {
        return self::instance()->wikiLinks;
    }

    function getContactMail()
    {
        return $this->contactMail;
    }

    function getPictureDirectory()
    {
        return $this->pictureDirectory;
    }

    function getPictureUrl()
    {
        return $this->pictureUrl;
    }

    function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    function getSiteInService()
    {
        return $this->siteInService;
    }

    function getPagetitle()
    {
        return $this->pagetitle;
    }

    function getMedalsModuleSwitchedOn()
    {
        return $this->medalsModuleSwitchedOn;
    }

    function getGoogleMapKey()
    {
        return $this->googleMapKey;
    }

    function getMainPageMapCenterLat()
    {
        return $this->mainPageMapCenterLat;
    }

    function getMainPageMapCenterLon()
    {
        return $this->mainPageMapCenterLon;
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

    public function getMedalsModuleSwitchOn()
    {
        return $this->medalsModuleSwitchedOn;
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

    public function getPowerTrailModuleSwitchOn()
    {
        return $this->powerTrailModuleSwitchOn;
    }

    public static function getNoreplyEmailAddress()
    {
        return self::instance()->instance()->noreplyEmailAddress;
    }


    /**
     * returns true if google automatic translation is enabled in config
     */
    public function isGoogleTranslationEnabled()
    {
        return $this->isGoogleTranslationEnabled;
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

    public function getDbUser(){
        return $this->dbUser;
    }

    public function getDbPass(){
        return $this->dbPass;
    }

    public function getDbHost(){
        return $this->dbHost;
    }

    public function getDbName(){
        return $this->dbName;
    }

    public static function getTechAdminsEmailAddr(){
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
}
