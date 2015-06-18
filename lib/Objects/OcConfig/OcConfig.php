<?php

namespace lib\Objects\OcConfig;

/**
 * Description of ocConfig
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

    /**
     * Call this method to get singleton
     * @return ocConfig
     */
    public static function Instance()
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
        include __DIR__ . '/../../settings.inc.php';
        $this->medalsModuleSwitchedOn = $config['medalsModuleSwitchedOn'];
        $this->datetimeFormat = $datetimeFormat;
        $this->ocNodeId = $oc_nodeid;
        $this->absolute_server_URI = $absolute_server_URI;
        $this->octeamEmailsSignature = $octeamEmailsSignature;
        $this->octeamEmailAddress = $octeam_email;
        $this->siteName = $site_name;
        $this->dynamicFilesPath =  $dynbasepath;
        $this->powerTrailModuleSwitchOn = $powerTrailModuleSwitchOn;
    }

    public function getAbsolute_server_URI()
    {
        return $this->absolute_server_URI;
    }
    
    public function getOcteamEmailsSignature()
    {
        return $this->octeamEmailsSignature;
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
    

    public function getSiteName()
    {
        return $this->siteName;
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

}
